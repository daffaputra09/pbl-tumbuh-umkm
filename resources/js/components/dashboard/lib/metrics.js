export const VERIFICATION_STATUSES = [
    { key: 'verified', label: 'Terverifikasi', color: '#0f766e' },
    { key: 'pending', label: 'Menunggu', color: '#d97706' },
    { key: 'needs_revision', label: 'Perlu perbaikan', color: '#e11d48' },
    { key: 'rejected', label: 'Ditolak', color: '#64748b' },
];

export const NEED_LEVELS = [
    { key: 'high', label: 'Tinggi', color: '#b45309', className: 'bg-sun-700' },
    { key: 'moderate', label: 'Sedang', color: '#fbbf24', className: 'bg-sun-400' },
    { key: 'low', label: 'Rendah', color: '#99f6e4', className: 'bg-brand-200' },
];

const LEVEL_RANK = { high: 2, moderate: 1, low: 0 };

const monthFormatter = new Intl.DateTimeFormat('id-ID', { month: 'short' });
const longMonthFormatter = new Intl.DateTimeFormat('id-ID', { month: 'long', year: 'numeric' });

export function hasCompletedCoaching(record) {
    return record.coachingSession?.status === 'completed';
}

export function assessmentScores(record) {
    return record.currentAssessment?.scores ?? [];
}

export function scoreFor(record, slug) {
    return assessmentScores(record).find((item) => item.slug === slug) ?? null;
}

export function dominantObstacle(record) {
    return assessmentScores(record).reduce(
        (top, item) => (item.score > top.score ? item : top),
        { slug: null, score: -1, level: 'low', obstacleCategoryId: null },
    );
}

export function daysSince(dateString, now) {
    return Math.max(0, Math.floor((now - new Date(`${dateString}T00:00:00`)) / 86_400_000));
}

export function filterRecords(records, { hamlet, businessType }) {
    return records.filter(
        (record) =>
            (hamlet === 'semua' || record.hamlet === hamlet) &&
            (businessType === 'semua' || record.businessType.slug === businessType),
    );
}

function monthKey(date) {
    return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`;
}

export function buildMonths(now, count) {
    return Array.from({ length: count }, (_, index) => {
        const date = new Date(now.getFullYear(), now.getMonth() - (count - 1 - index), 1);

        return { key: monthKey(date), label: monthFormatter.format(date), longLabel: longMonthFormatter.format(date) };
    });
}

function countBy(records, keyOf) {
    return records.reduce((counts, record) => {
        const key = keyOf(record);
        counts[key] = (counts[key] ?? 0) + 1;

        return counts;
    }, {});
}

function percent(part, total) {
    return total === 0 ? 0 : Math.round((part / total) * 100);
}

function createdMonth(record) {
    return record.createdAt.slice(0, 7);
}

/**
 * Hitung angka dashboard dari proyeksi businesses + asesmen terkini + sesi pembinaan.
 */
export function computeMetrics(records, { references, periodMonths, now }) {
    const months = buildMonths(now, periodMonths);
    const periodStartKey = months[0].key;
    const registrationsByMonth = countBy(records, createdMonth);
    const verifiedByMonth = countBy(
        records.filter((record) => record.verificationStatus === 'verified'),
        createdMonth,
    );

    const isInPeriod = (record) => createdMonth(record) >= periodStartKey;
    const active = records.filter((record) => record.operationalStatus === 'active');
    const verified = records.filter((record) => record.verificationStatus === 'verified');
    const mentored = records.filter(hasCompletedCoaching);
    const assessed = records.filter((record) => record.currentAssessment);
    const verificationCounts = countBy(records, (record) => record.verificationStatus);

    const cumulativeSeries = (subset) => {
        const byMonth = countBy(subset, createdMonth);
        let runningTotal = subset.filter((record) => createdMonth(record) < periodStartKey).length;

        return months.map((month) => {
            runningTotal += byMonth[month.key] ?? 0;

            return runningTotal;
        });
    };

    const summary = {
        total: { value: records.length, added: records.filter(isInPeriod).length, series: cumulativeSeries(records) },
        active: {
            value: active.length,
            share: percent(active.length, records.length),
            added: active.filter(isInPeriod).length,
            series: cumulativeSeries(active),
        },
        verified: {
            value: verified.length,
            share: percent(verified.length, records.length),
            waiting: (verificationCounts.pending ?? 0) + (verificationCounts.needs_revision ?? 0),
            series: cumulativeSeries(verified),
        },
        mentored: {
            value: mentored.length,
            share: percent(mentored.length, verified.length),
            added: mentored.filter(isInPeriod).length,
            series: cumulativeSeries(mentored),
        },
    };

    const trend = months.map((month) => ({
        ...month,
        pendaftar: registrationsByMonth[month.key] ?? 0,
        terverifikasi: verifiedByMonth[month.key] ?? 0,
    }));

    const verification = VERIFICATION_STATUSES.map((status) => ({
        ...status,
        value: verificationCounts[status.key] ?? 0,
        share: percent(verificationCounts[status.key] ?? 0, records.length),
    }));

    const obstacles = references.obstacleCategories
        .map((category) => {
            const levels = countBy(assessed, (record) => scoreFor(record, category.slug)?.level ?? 'low');
            const averageScore =
                assessed.length === 0
                    ? 0
                    : Math.round(assessed.reduce((sum, record) => sum + (scoreFor(record, category.slug)?.score ?? 0), 0) / assessed.length);

            return {
                key: category.slug,
                label: category.name,
                id: category.id,
                high: levels.high ?? 0,
                moderate: levels.moderate ?? 0,
                low: levels.low ?? 0,
                averageScore,
                dominantCount: assessed.filter((record) => {
                    const top = dominantObstacle(record);

                    return top.obstacleCategoryId === category.id && LEVEL_RANK[top.level] >= 1;
                }).length,
            };
        })
        .sort((first, second) => second.high - first.high);

    const hamletCounts = references.hamlets.map((name) => {
        const inHamlet = records.filter((record) => record.hamlet === name);
        const activeInHamlet = inHamlet.filter((record) => record.operationalStatus === 'active').length;

        return {
            name,
            aktif: activeInHamlet,
            tidakAktif: inHamlet.length - activeInHamlet,
            total: inHamlet.length,
            highNeed: inHamlet.filter((record) => dominantObstacle(record).level === 'high').length,
        };
    });

    const businessTypeCounts = references.businessTypes
        .map((type) => {
            const count = records.filter((record) => record.businessType.slug === type.slug).length;

            return { key: type.slug, label: type.name, id: type.id, value: count, share: percent(count, records.length) };
        })
        .sort((first, second) => second.value - first.value);

    const grouping = references.obstacleCategories
        .map((category) => {
            const value = assessed.filter((record) => record.currentAssessment.primaryObstacleCategoryId === category.id).length;

            return { key: category.slug, label: category.name, id: category.id, value, share: percent(value, records.length) };
        })
        .sort((first, second) => second.value - first.value);

    const groupingUnassigned = {
        key: 'unassigned',
        label: 'Belum asesmen',
        value: records.length - assessed.length,
        share: percent(records.length - assessed.length, records.length),
    };

    const needsAttention = records
        .filter(
            (record) =>
                record.operationalStatus === 'active' &&
                record.verificationStatus === 'verified' &&
                !hasCompletedCoaching(record),
        )
        .map((record) => ({ ...record, dominant: dominantObstacle(record) }))
        .filter((record) => record.dominant.level === 'high')
        .sort((first, second) => second.dominant.score - first.dominant.score);

    const verificationQueue = records
        .filter((record) => record.verificationStatus === 'pending' || record.verificationStatus === 'needs_revision')
        .map((record) => ({ ...record, waitingDays: daysSince(record.createdAt, now) }))
        .sort((first, second) => second.waitingDays - first.waitingDays);

    return {
        months,
        summary,
        trend,
        verification,
        obstacles,
        assessedCount: assessed.length,
        hamlets: hamletCounts,
        businessTypes: businessTypeCounts,
        grouping,
        groupingUnassigned,
        needsAttention,
        verificationQueue,
    };
}

export function formatNumber(value) {
    return value.toLocaleString('id-ID');
}

export function formatDate(dateString) {
    return new Date(`${dateString}T00:00:00`).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
}
