import { Suspense, lazy, useMemo, useState } from 'react';
import { MotionConfig } from 'motion/react';
import StatCards from '@/components/dashboard/StatCards';
import ObstaclePanel from '@/components/dashboard/ObstaclePanel';
import { AttentionTable, VerificationQueue } from '@/components/dashboard/ActionPanels';
import { BusinessTypePanel, GroupingPanel } from '@/components/dashboard/DistributionPanels';
import { FilterBar, HeadInsights, WelcomeBanner } from '@/components/dashboard/Overview';
import { ChartPlaceholder, Panel } from '@/components/dashboard/Panel';
import { computeMetrics, filterRecords } from '@/components/dashboard/lib/metrics';

const loadCharts = () => import('@/components/dashboard/Charts');
const TrendChart = lazy(() => loadCharts().then((module) => ({ default: module.TrendChart })));
const VerificationDonut = lazy(() => loadCharts().then((module) => ({ default: module.VerificationDonut })));
const DusunChart = lazy(() => loadCharts().then((module) => ({ default: module.DusunChart })));

const DEFAULT_FILTERS = { hamlet: 'semua', businessType: 'semua', periodMonths: 12 };

function SectionLabel({ children }) {
    return <h2 className="-mb-1 pt-2 text-xs font-bold tracking-wider text-slate-500 uppercase">{children}</h2>;
}

export default function Dashboard({
    role = 'petugas',
    generatedAt = new Date().toISOString(),
    isSampleData = false,
    references = { hamlets: [], businessTypes: [], obstacleCategories: [] },
    businesses = [],
    followUpQueue = [],
    coachingProgress = { scheduled: 0, completed: 0, cancelled: 0, total: 0 },
    routes = {},
}) {
    const safeReferences = useMemo(() => ({
        hamlets: Array.isArray(references?.hamlets) ? references.hamlets : [],
        businessTypes: Array.isArray(references?.businessTypes) ? references.businessTypes : [],
        obstacleCategories: Array.isArray(references?.obstacleCategories) ? references.obstacleCategories : [],
    }), [references]);
    const safeBusinesses = Array.isArray(businesses) ? businesses : [];
    const safeFollowUpQueue = Array.isArray(followUpQueue) ? followUpQueue : [];
    const safeCoachingProgress = coachingProgress || { scheduled: 0, completed: 0, cancelled: 0, total: 0 };
    const safeRoutes = routes || {};

    const [filters, setFilters] = useState(DEFAULT_FILTERS);
    const now = useMemo(() => new Date(generatedAt || Date.now()), [generatedAt]);

    const records = useMemo(() => filterRecords(safeBusinesses, filters), [safeBusinesses, filters]);
    const filteredFollowUpQueue = useMemo(() => {
        if (!filters.hamlet || filters.hamlet === 'semua') {
            return safeFollowUpQueue;
        }

        return safeFollowUpQueue.filter((item) => (item.hamlet || '').toLowerCase() === filters.hamlet.toLowerCase());
    }, [safeFollowUpQueue, filters.hamlet]);

    const metrics = useMemo(
        () => computeMetrics(records, { references: safeReferences, periodMonths: filters.periodMonths, now }),
        [records, safeReferences, filters.periodMonths, now],
    );
    const obstacleLabels = useMemo(
        () => Object.fromEntries((safeReferences.obstacleCategories || []).map((category) => [category.slug, category.name])),
        [safeReferences],
    );

    const updateFilters = (changes) => setFilters((current) => ({ ...current, ...changes }));
    const periodLabel = `${filters.periodMonths} bulan terakhir`;

    return (
        <MotionConfig reducedMotion="user">
            <div className="flex flex-col gap-5 sm:gap-6">
                    <WelcomeBanner
                        role={role}
                        now={now}
                        pendingCount={metrics.summary.verified.waiting}
                        attentionCount={metrics.needsAttention.length}
                        isSampleData={isSampleData}
                        followUpCount={safeFollowUpQueue.length}
                        routes={safeRoutes}
                    />

                    <FilterBar
                        references={safeReferences}
                        filters={filters}
                        onChange={updateFilters}
                        onReset={() => setFilters((current) => ({ ...DEFAULT_FILTERS, periodMonths: current.periodMonths }))}
                        shownCount={records.length}
                        totalCount={safeBusinesses.length}
                    />

                    {role === 'kepala-desa' && (
                        <HeadInsights
                            metrics={metrics}
                            coachingProgress={safeCoachingProgress}
                            followUpQueue={filteredFollowUpQueue}
                            routes={safeRoutes}
                        />
                    )}

                    <SectionLabel>Ringkasan UMKM</SectionLabel>
                    <StatCards
                        summary={metrics.summary}
                        periodLabel={periodLabel}
                        role={role}
                        coachingProgress={safeCoachingProgress}
                        routes={safeRoutes}
                    />

                    <div className="grid gap-5 sm:gap-6 lg:grid-cols-3">
                        <Panel
                            className="h-[480px] lg:col-span-2"
                            title="Tren pendaftaran UMKM"
                            description={`Pendaftar baru per bulan selama ${periodLabel}.`}
                        >
                            <Suspense fallback={<ChartPlaceholder />}>
                                <TrendChart data={metrics.trend} />
                            </Suspense>
                        </Panel>
                        <Panel className="h-[480px]" title="Status verifikasi" description="Kondisi data yang sudah masuk.">
                            <Suspense fallback={<ChartPlaceholder />}>
                                <VerificationDonut data={metrics.verification} />
                            </Suspense>
                        </Panel>
                    </div>

                    <SectionLabel>Status kendala</SectionLabel>
                    <div className="grid gap-5 sm:gap-6 lg:grid-cols-3">
                        <ObstaclePanel
                            className="h-[480px] lg:col-span-2"
                            obstacles={metrics.obstacles}
                            assessedCount={metrics.assessedCount}
                        />
                        <GroupingPanel
                            className="h-[480px]"
                            grouping={metrics.grouping}
                            unassigned={metrics.groupingUnassigned}
                            totalRecords={records.length}
                        />
                    </div>

                    <SectionLabel>Sebaran data</SectionLabel>
                    <div className="grid gap-5 sm:gap-6 lg:grid-cols-3">
                        <Panel
                            className="h-[480px] lg:col-span-2"
                            title="Sebaran per dusun"
                            description="Jumlah UMKM aktif dan tidak aktif di setiap dusun."
                        >
                            <Suspense fallback={<ChartPlaceholder />}>
                                <DusunChart data={metrics.hamlets} />
                            </Suspense>
                        </Panel>
                        <BusinessTypePanel
                            className="h-[480px]"
                            businessTypes={metrics.businessTypes}
                            totalRecords={records.length}
                            selectedType={filters.businessType}
                            onSelect={(businessType) => updateFilters({ businessType })}
                        />
                    </div>

                    <SectionLabel>Tindak lanjut</SectionLabel>
                    <div className="grid gap-5 sm:gap-6 lg:grid-cols-3">
                        <AttentionTable
                            className="h-[480px] lg:col-span-2"
                            records={metrics.needsAttention}
                            obstacleLabels={obstacleLabels}
                        />
                        <VerificationQueue
                            className="h-[480px]"
                            records={metrics.verificationQueue}
                            role={role}
                            followUpQueue={filteredFollowUpQueue}
                            routes={safeRoutes}
                        />
                    </div>
            </div>
        </MotionConfig>
    );
}
