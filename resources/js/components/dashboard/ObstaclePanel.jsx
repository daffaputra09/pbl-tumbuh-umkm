import { useState } from 'react';
import { motion } from 'motion/react';
import { Factory01Icon, Idea01Icon, LegalDocument01Icon, Megaphone01Icon, Money03Icon, SmartPhone01Icon } from '@hugeicons/core-free-icons';
import { Icon } from '@/components/landing/shared';
import { EmptyState, Legend, Panel, SegmentedControl } from '@/components/dashboard/Panel';
import { NEED_LEVELS, formatNumber } from '@/components/dashboard/lib/metrics';

export const OBSTACLE_ICONS = {
    modal: Money03Icon,
    pemasaran: Megaphone01Icon,
    legalitas: LegalDocument01Icon,
    produksi: Factory01Icon,
    digitalisasi: SmartPhone01Icon,
};

const DISPLAY_OPTIONS = [
    { value: 'jumlah', label: 'Jumlah' },
    { value: 'persen', label: 'Persen' },
];

export default function ObstaclePanel({ obstacles, assessedCount, className }) {
    const [displayMode, setDisplayMode] = useState('jumlah');
    const topObstacle = obstacles[0];
    const denominator = assessedCount || 1;

    const formatValue = (value) => (displayMode === 'persen' ? `${Math.round((value / denominator) * 100)}%` : formatNumber(value));

    return (
        <Panel
            className={className}
            title="Status kendala per bidang"
            description="Skor dari asesmen terkini, diurutkan dari bidang dengan kebutuhan tinggi terbanyak."
            action={<SegmentedControl label="Tampilkan nilai sebagai" options={DISPLAY_OPTIONS} value={displayMode} onChange={setDisplayMode} />}
        >
            {assessedCount === 0 ? (
                <EmptyState message="Belum ada UMKM yang menyelesaikan kuesioner kendala." />
            ) : (
                <div className="flex flex-col gap-5">
                    <Legend items={NEED_LEVELS.map((level) => ({ label: `Kebutuhan ${level.label.toLowerCase()}`, className: level.className }))} />

                    <ul className="flex flex-col gap-4">
                        {obstacles.map((obstacle, index) => (
                            <li key={obstacle.key} className="grid grid-cols-[auto_1fr] items-center gap-x-3 gap-y-2 sm:grid-cols-[auto_9rem_1fr_4.5rem]">
                                <span className="grid size-9 place-items-center rounded-xl bg-brand-50 text-brand">
                                    <Icon icon={OBSTACLE_ICONS[obstacle.key]} size={18} />
                                </span>
                                <div className="min-w-0">
                                    <p className="text-sm font-semibold text-ink">{obstacle.label}</p>
                                    <p className="text-xs text-muted-foreground">Utama bagi {obstacle.dominantCount} UMKM</p>
                                </div>

                                <div
                                    className="col-span-2 flex h-3 overflow-hidden rounded-full bg-slate-100 sm:col-span-1"
                                    role="img"
                                    aria-label={`${obstacle.label}: ${obstacle.high} tinggi, ${obstacle.moderate} sedang, ${obstacle.low} rendah`}
                                >
                                    {NEED_LEVELS.map((level) => (
                                        <motion.span
                                            key={level.key}
                                            className={`${level.className} h-full first:rounded-l-full last:rounded-r-full`}
                                            title={`${level.label}: ${formatValue(obstacle[level.key])}`}
                                            initial={{ width: 0 }}
                                            animate={{ width: `${(obstacle[level.key] / denominator) * 100}%` }}
                                            transition={{ duration: 0.7, delay: 0.05 * index, ease: [0.22, 1, 0.36, 1] }}
                                        />
                                    ))}
                                </div>

                                <p className="col-span-2 flex items-baseline justify-between gap-2 text-xs text-muted-foreground sm:col-span-1 sm:flex-col sm:items-end sm:gap-0">
                                    <span className="sm:hidden">
                                        Sedang {formatValue(obstacle.moderate)} · Rendah {formatValue(obstacle.low)}
                                    </span>
                                    <span>
                                        <span className="text-sm font-bold text-sun-700 tabular-nums">{formatValue(obstacle.high)}</span> tinggi
                                    </span>
                                </p>
                            </li>
                        ))}
                    </ul>

                    {topObstacle && topObstacle.high > 0 && (
                        <div className="flex items-start gap-3 rounded-xl border border-sun-200 bg-sun-50/70 p-4">
                            <span className="grid size-8 shrink-0 place-items-center rounded-lg bg-white text-sun">
                                <Icon icon={Idea01Icon} size={18} />
                            </span>
                            <p className="text-sm leading-relaxed text-ink">
                                <span className="font-semibold">{topObstacle.label}</span> jadi kendala paling mendesak:{' '}
                                {formatNumber(topObstacle.high)} UMKM berkebutuhan tinggi, skor rata-rata {topObstacle.averageScore}/100.
                                Bidang ini bisa jadi acuan saat petugas memilih program bantuan.
                            </p>
                        </div>
                    )}
                </div>
            )}
        </Panel>
    );
}
