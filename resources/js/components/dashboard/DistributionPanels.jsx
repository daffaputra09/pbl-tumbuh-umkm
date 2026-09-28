import { motion } from 'motion/react';
import { EmptyState, Panel } from '@/components/dashboard/Panel';
import { formatNumber } from '@/components/dashboard/lib/metrics';
import { cn } from '@/lib/utils';
import { OBSTACLE_ICONS } from '@/components/dashboard/ObstaclePanel';
import { Icon } from '@/components/landing/shared';

const BUSINESS_TYPE_COLORS = ['bg-brand', 'bg-brand-400', 'bg-sun', 'bg-sun-400', 'bg-cyan-600', 'bg-slate-400'];

export function BusinessTypePanel({ businessTypes, totalRecords, selectedType, onSelect }) {
    return (
        <Panel title="Jenis usaha" description="Klik salah satu untuk memfilter seluruh dashboard.">
            {totalRecords === 0 ? (
                <EmptyState />
            ) : (
                <div className="flex flex-col gap-4">
                    <div className="flex h-3 overflow-hidden rounded-full bg-slate-100" aria-hidden="true">
                        {businessTypes.map((type, index) => (
                            <motion.span
                                key={type.key}
                                className={cn('h-full', BUSINESS_TYPE_COLORS[index % BUSINESS_TYPE_COLORS.length])}
                                initial={{ width: 0 }}
                                animate={{ width: `${type.share}%` }}
                                transition={{ duration: 0.7, ease: [0.22, 1, 0.36, 1] }}
                            />
                        ))}
                    </div>

                    <ul className="flex flex-col gap-1">
                        {businessTypes.map((type, index) => {
                            const isSelected = selectedType === type.key;

                            return (
                                <li key={type.key}>
                                    <button
                                        type="button"
                                        onClick={() => onSelect(isSelected ? 'semua' : type.key)}
                                        aria-pressed={isSelected}
                                        className={cn(
                                            'flex w-full items-center gap-3 rounded-xl px-3 py-2 text-left transition-colors',
                                            isSelected ? 'bg-brand-50 ring-1 ring-brand-200' : 'hover:bg-slate-50',
                                        )}
                                    >
                                        <span className={cn('size-2.5 shrink-0 rounded-full', BUSINESS_TYPE_COLORS[index % BUSINESS_TYPE_COLORS.length])} />
                                        <span className="flex-1 truncate text-sm font-medium text-ink">{type.label}</span>
                                        <span className="text-sm font-bold text-ink tabular-nums">{formatNumber(type.value)}</span>
                                        <span className="w-10 text-right text-xs text-muted-foreground tabular-nums">{type.share}%</span>
                                    </button>
                                </li>
                            );
                        })}
                    </ul>
                </div>
            )}
        </Panel>
    );
}

export function GroupingPanel({ grouping, unassigned, totalRecords }) {
    const rows = unassigned.value > 0 ? [...grouping, unassigned] : grouping;

    return (
        <Panel title="Kelompok kendala utama" description="Kategori dengan skor tertinggi pada asesmen terkini.">
            {totalRecords === 0 ? (
                <EmptyState />
            ) : (
                <ul className="flex flex-col gap-3">
                    {rows.map((item) => (
                        <li key={item.key}>
                            <div className="mb-1.5 flex items-baseline justify-between gap-3">
                                <p className="flex items-center gap-2 text-sm font-semibold text-ink">
                                    {OBSTACLE_ICONS[item.key] ? <Icon icon={OBSTACLE_ICONS[item.key]} size={16} className="text-brand" /> : null}
                                    {item.label}
                                </p>
                                <span className="text-sm font-bold text-ink tabular-nums">{item.share}%</span>
                            </div>
                            <div className="h-2 overflow-hidden rounded-full bg-slate-100">
                                <motion.div
                                    className={cn('h-full rounded-full', item.key === 'unassigned' ? 'bg-slate-400' : 'bg-brand')}
                                    initial={{ width: 0 }}
                                    animate={{ width: `${item.share}%` }}
                                    transition={{ duration: 0.7, ease: [0.22, 1, 0.36, 1] }}
                                />
                            </div>
                            <p className="mt-1 text-xs text-muted-foreground">{formatNumber(item.value)} UMKM</p>
                        </li>
                    ))}
                </ul>
            )}
        </Panel>
    );
}
