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

export default function Dashboard({ role, generatedAt, isSampleData, references, businesses }) {
    const [filters, setFilters] = useState(DEFAULT_FILTERS);
    const now = useMemo(() => new Date(generatedAt), [generatedAt]);

    const records = useMemo(() => filterRecords(businesses, filters), [businesses, filters]);
    const metrics = useMemo(
        () => computeMetrics(records, { references, periodMonths: filters.periodMonths, now }),
        [records, references, filters.periodMonths, now],
    );
    const obstacleLabels = useMemo(
        () => Object.fromEntries(references.obstacleCategories.map((category) => [category.slug, category.name])),
        [references],
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
                    />

                    <FilterBar
                        references={references}
                        filters={filters}
                        onChange={updateFilters}
                        onReset={() => setFilters((current) => ({ ...DEFAULT_FILTERS, periodMonths: current.periodMonths }))}
                        shownCount={records.length}
                        totalCount={businesses.length}
                    />

                    {role === 'kepala-desa' && <HeadInsights metrics={metrics} />}

                    <SectionLabel>Ringkasan UMKM</SectionLabel>
                    <StatCards summary={metrics.summary} periodLabel={periodLabel} />

                    <div className="grid gap-5 sm:gap-6 lg:grid-cols-3">
                        <Panel
                            className="lg:col-span-2"
                            title="Tren pendaftaran UMKM"
                            description={`Pendaftar baru per bulan selama ${periodLabel}.`}
                        >
                            <Suspense fallback={<ChartPlaceholder />}>
                                <TrendChart data={metrics.trend} />
                            </Suspense>
                        </Panel>
                        <Panel title="Status verifikasi" description="Kondisi data yang sudah masuk.">
                            <Suspense fallback={<ChartPlaceholder />}>
                                <VerificationDonut data={metrics.verification} />
                            </Suspense>
                        </Panel>
                    </div>

                    <SectionLabel>Status kendala</SectionLabel>
                    <div className="grid gap-5 sm:gap-6 lg:grid-cols-3">
                        <ObstaclePanel
                            className="lg:col-span-2"
                            obstacles={metrics.obstacles}
                            assessedCount={metrics.assessedCount}
                        />
                        <GroupingPanel grouping={metrics.grouping} unassigned={metrics.groupingUnassigned} totalRecords={records.length} />
                    </div>

                    <SectionLabel>Sebaran data</SectionLabel>
                    <div className="grid gap-5 sm:gap-6 lg:grid-cols-3">
                        <Panel
                            className="lg:col-span-2"
                            title="Sebaran per dusun"
                            description="Jumlah UMKM aktif dan tidak aktif di setiap dusun."
                        >
                            <Suspense fallback={<ChartPlaceholder />}>
                                <DusunChart data={metrics.hamlets} />
                            </Suspense>
                        </Panel>
                        <BusinessTypePanel
                            businessTypes={metrics.businessTypes}
                            totalRecords={records.length}
                            selectedType={filters.businessType}
                            onSelect={(businessType) => updateFilters({ businessType })}
                        />
                    </div>

                    <SectionLabel>Tindak lanjut</SectionLabel>
                    <div className="grid items-start gap-5 sm:gap-6 lg:grid-cols-3">
                        <div className="min-w-0 lg:col-span-2">
                            <AttentionTable records={metrics.needsAttention} obstacleLabels={obstacleLabels} />
                        </div>
                        <VerificationQueue records={metrics.verificationQueue} />
                    </div>
            </div>
        </MotionConfig>
    );
}
