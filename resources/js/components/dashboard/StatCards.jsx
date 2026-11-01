import { useId } from 'react';
import { motion } from 'motion/react';
import { CheckmarkBadge01Icon, Store01Icon, TeachingIcon, UserGroupIcon } from '@hugeicons/core-free-icons';
import { Icon } from '@/components/landing/shared';
import { AnimatedNumber } from '@/components/dashboard/Panel';
import { cn } from '@/lib/utils';

function Sparkline({ values = [], color }) {
    const gradientId = useId();
    const safeValues = Array.isArray(values) ? values : [];
    if (safeValues.length === 0) return null;

    const width = 120;
    const height = 36;
    const max = Math.max(...safeValues, 1);
    const min = Math.min(...safeValues, 0);
    const range = max - min || 1;
    const step = safeValues.length > 1 ? width / (safeValues.length - 1) : width;
    const points = safeValues.map((value, index) => [index * step, height - 3 - ((value - min) / range) * (height - 6)]);
    const line = points.map(([x, y], index) => `${index === 0 ? 'M' : 'L'}${x.toFixed(1)} ${y.toFixed(1)}`).join(' ');

    return (
        <svg viewBox={`0 0 ${width} ${height}`} className="h-9 w-full max-w-28" preserveAspectRatio="none" aria-hidden="true">
            <defs>
                <linearGradient id={gradientId} x1="0" x2="0" y1="0" y2="1">
                    <stop offset="0%" stopColor={color} stopOpacity="0.25" />
                    <stop offset="100%" stopColor={color} stopOpacity="0" />
                </linearGradient>
            </defs>
            <path d={`${line} L${width} ${height} L0 ${height} Z`} fill={`url(#${gradientId})`} />
            <path d={line} fill="none" stroke={color} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" vectorEffect="non-scaling-stroke" />
        </svg>
    );
}

export default function StatCards({
    summary,
    periodLabel,
    role,
    coachingProgress = { scheduled: 0, completed: 0, cancelled: 0, total: 0 },
    routes = {},
}) {
    const isHead = role === 'kepala-desa';
    const safeRoutes = routes || {};

    const cards = [
        {
            key: 'total',
            label: 'UMKM terdaftar',
            icon: Store01Icon,
            value: summary.total.value,
            note: `+${summary.total.added} dalam ${periodLabel}`,
            series: summary.total.series,
            color: '#0f766e',
            iconClassName: 'bg-brand-50 text-brand',
        },
        {
            key: 'active',
            label: 'UMKM aktif',
            icon: UserGroupIcon,
            value: summary.active.value,
            note: `${summary.active.share}% dari total masih beroperasi`,
            series: summary.active.series,
            color: '#15803d',
            iconClassName: 'bg-green-50 text-success',
        },
        {
            key: 'verified',
            label: 'Data terverifikasi',
            icon: CheckmarkBadge01Icon,
            value: summary.verified.value,
            note: `${summary.verified.waiting} menunggu dicek`,
            series: summary.verified.series,
            color: '#0e7490',
            iconClassName: 'bg-cyan-50 text-cyan-700',
            highlight: summary.verified.waiting > 0,
        },
        {
            key: 'mentored',
            label: isHead ? 'Progres Pembinaan' : 'Sudah dibina',
            icon: TeachingIcon,
            value: isHead && coachingProgress ? coachingProgress.completed : summary.mentored.value,
            valueSuffix: isHead && coachingProgress ? (
                <span className="ml-1 text-sm font-semibold text-muted-foreground">
                    / {coachingProgress.total} sesi
                </span>
            ) : null,
            subValue: isHead && coachingProgress ? (
                <div className="mt-2.5 flex flex-wrap gap-1.5 text-[11px] font-semibold">
                    <span className="inline-flex items-center rounded-md bg-emerald-50 px-2 py-0.5 text-emerald-700">
                        {coachingProgress.completed} Selesai
                    </span>
                    <span className="inline-flex items-center rounded-md bg-sky-50 px-2 py-0.5 text-sky-700">
                        {coachingProgress.scheduled} Terjadwal
                    </span>
                    <span className="inline-flex items-center rounded-md bg-rose-50 px-2 py-0.5 text-rose-700">
                        {coachingProgress.cancelled} Batal
                    </span>
                </div>
            ) : null,
            note: isHead && coachingProgress
                ? 'Klik untuk membuka riwayat pembinaan desa'
                : `${summary.mentored.share}% dari UMKM terverifikasi`,
            series: summary.mentored.series,
            color: '#d97706',
            iconClassName: 'bg-sun-50 text-sun',
            href: isHead ? (safeRoutes.pimpinanCoachingHistory || '/pimpinan/riwayat-pembinaan') : undefined,
        },
    ];

    return (
        <div className="grid grid-cols-2 gap-3 sm:gap-4 xl:grid-cols-4">
            {cards.map((card, index) => {
                const CardElement = card.href ? 'a' : 'div';
                const elementProps = card.href
                    ? { href: card.href, title: 'Buka riwayat pembinaan desa' }
                    : {};

                return (
                    <motion.article
                        key={card.key}
                        initial={{ opacity: 0, y: 12 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ duration: 0.5, delay: 0.05 * index, ease: [0.22, 1, 0.36, 1] }}
                        className={cn(
                            'group relative flex flex-col justify-between overflow-hidden rounded-2xl border border-border bg-white p-4 shadow-xs transition-all hover:shadow-md sm:p-5',
                            card.href && 'cursor-pointer hover:border-brand-300 hover:bg-brand-50/20',
                        )}
                    >
                        <CardElement {...elementProps} className="flex h-full flex-col justify-between gap-3 sm:gap-4">
                            <div>
                                <div className="flex items-start justify-between gap-3">
                                    <span className={cn('grid size-9 place-items-center rounded-xl sm:size-11', card.iconClassName)}>
                                        <Icon icon={card.icon} size={20} />
                                    </span>
                                    <span className="hidden flex-1 justify-end sm:flex">
                                        <Sparkline values={card.series} color={card.color} />
                                    </span>
                                </div>
                                <div className="mt-3">
                                    <div className="flex items-center justify-between">
                                        <p className="text-xs font-medium text-muted-foreground sm:text-sm">{card.label}</p>
                                        {card.href && (
                                            <span className="text-xs font-bold text-brand transition-transform group-hover:translate-x-0.5">
                                                →
                                            </span>
                                        )}
                                    </div>
                                    <div className="mt-0.5 flex items-baseline sm:mt-1">
                                        <AnimatedNumber value={card.value} className="block text-2xl font-extrabold tracking-tight text-ink sm:text-3xl" />
                                        {card.valueSuffix}
                                    </div>
                                    {card.subValue}
                                </div>
                            </div>
                            <p
                                className={cn(
                                    'text-[11px] leading-snug font-medium sm:text-xs',
                                    card.highlight ? 'w-fit rounded-md bg-sun-50 px-2 py-1 text-sun-700' : 'text-muted-foreground',
                                    card.href && 'text-brand font-semibold group-hover:underline',
                                )}
                            >
                                {card.note}
                            </p>
                        </CardElement>
                    </motion.article>
                );
            })}
        </div>
    );
}
