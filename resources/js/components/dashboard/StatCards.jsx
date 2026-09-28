import { useId } from 'react';
import { motion } from 'motion/react';
import { CheckmarkBadge01Icon, Store01Icon, TeachingIcon, UserGroupIcon } from '@hugeicons/core-free-icons';
import { Icon } from '@/components/landing/shared';
import { AnimatedNumber } from '@/components/dashboard/Panel';
import { cn } from '@/lib/utils';

function Sparkline({ values, color }) {
    const gradientId = useId();
    const width = 120;
    const height = 36;
    const max = Math.max(...values, 1);
    const min = Math.min(...values, 0);
    const range = max - min || 1;
    const step = values.length > 1 ? width / (values.length - 1) : width;
    const points = values.map((value, index) => [index * step, height - 3 - ((value - min) / range) * (height - 6)]);
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

export default function StatCards({ summary, periodLabel }) {
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
            label: 'Sudah dibina',
            icon: TeachingIcon,
            value: summary.mentored.value,
            note: `${summary.mentored.share}% dari UMKM terverifikasi`,
            series: summary.mentored.series,
            color: '#d97706',
            iconClassName: 'bg-sun-50 text-sun',
        },
    ];

    return (
        <div className="grid grid-cols-2 gap-3 sm:gap-4 xl:grid-cols-4">
            {cards.map((card, index) => (
                <motion.article
                    key={card.key}
                    initial={{ opacity: 0, y: 12 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.5, delay: 0.05 * index, ease: [0.22, 1, 0.36, 1] }}
                    className="group relative flex flex-col gap-3 overflow-hidden rounded-2xl border border-border bg-white p-4 shadow-xs transition-shadow hover:shadow-md sm:gap-4 sm:p-5"
                >
                    <div className="flex items-start justify-between gap-3">
                        <span className={cn('grid size-9 place-items-center rounded-xl sm:size-11', card.iconClassName)}>
                            <Icon icon={card.icon} size={20} />
                        </span>
                        <span className="hidden flex-1 justify-end sm:flex">
                            <Sparkline values={card.series} color={card.color} />
                        </span>
                    </div>
                    <div>
                        <p className="text-xs font-medium text-muted-foreground sm:text-sm">{card.label}</p>
                        <AnimatedNumber value={card.value} className="mt-0.5 block text-2xl font-extrabold tracking-tight text-ink sm:mt-1 sm:text-3xl" />
                    </div>
                    <p
                        className={cn(
                            'text-[11px] leading-snug font-medium sm:text-xs',
                            card.highlight ? 'w-fit rounded-md bg-sun-50 px-2 py-1 text-sun-700' : 'text-muted-foreground',
                        )}
                    >
                        {card.note}
                    </p>
                </motion.article>
            ))}
        </div>
    );
}
