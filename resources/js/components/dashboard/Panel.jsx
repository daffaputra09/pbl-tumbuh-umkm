import { useEffect, useRef } from 'react';
import { animate, useReducedMotion } from 'motion/react';
import { SearchRemoveIcon } from '@hugeicons/core-free-icons';
import { Icon } from '@/components/landing/shared';
import { cn } from '@/lib/utils';

export function Panel({ id, title, description, action, children, className, bodyClassName }) {
    return (
        <section
            id={id}
            className={cn('flex min-w-0 scroll-mt-24 flex-col rounded-2xl border border-border bg-white shadow-xs', className)}
        >
            <header className="flex flex-wrap items-start justify-between gap-3 px-5 pt-5 sm:px-6 sm:pt-6">
                <div className="min-w-0">
                    <h2 className="text-base font-bold text-ink sm:text-lg">{title}</h2>
                    {description && <p className="mt-0.5 text-sm leading-relaxed text-muted-foreground">{description}</p>}
                </div>
                {action}
            </header>
            <div className={cn('flex-1 px-5 pt-4 pb-5 sm:px-6 sm:pb-6', bodyClassName)}>{children}</div>
        </section>
    );
}

export function Legend({ items, className }) {
    return (
        <ul className={cn('flex flex-wrap gap-x-4 gap-y-1.5 text-xs text-muted-foreground', className)}>
            {items.map((item) => (
                <li key={item.label} className="flex items-center gap-1.5">
                    <span className={cn('size-2.5 rounded-full', item.className)} style={item.color ? { backgroundColor: item.color } : undefined} />
                    {item.label}
                </li>
            ))}
        </ul>
    );
}

export function ChartPlaceholder({ className }) {
    return <div className={cn('h-64 animate-pulse rounded-xl bg-slate-100 sm:h-72', className)} aria-label="Memuat grafik" />;
}

export function EmptyState({ message = 'Tidak ada data untuk filter yang dipilih.', className }) {
    return (
        <div className={cn('flex h-full min-h-40 flex-col items-center justify-center gap-2 rounded-xl bg-slate-50 p-6 text-center', className)}>
            <span className="grid size-10 place-items-center rounded-full bg-white text-slate-400 shadow-xs">
                <Icon icon={SearchRemoveIcon} size={20} />
            </span>
            <p className="max-w-60 text-sm text-muted-foreground">{message}</p>
        </div>
    );
}

/**
 * Angka yang bergerak halus dari nilai sebelumnya setiap kali filter berubah.
 */
export function AnimatedNumber({ value, suffix = '', className }) {
    const ref = useRef(null);
    const previousValue = useRef(0);
    const prefersReducedMotion = useReducedMotion();

    useEffect(() => {
        const element = ref.current;
        const format = (current) => `${Math.round(current).toLocaleString('id-ID')}${suffix}`;

        if (!element) {
            return;
        }

        if (prefersReducedMotion) {
            element.textContent = format(value);
            previousValue.current = value;

            return;
        }

        const controls = animate(previousValue.current, value, {
            duration: 0.9,
            ease: [0.16, 1, 0.3, 1],
            onUpdate: (current) => {
                element.textContent = format(current);
            },
        });
        previousValue.current = value;

        return () => controls.stop();
    }, [value, suffix, prefersReducedMotion]);

    return (
        <span ref={ref} className={cn('tabular-nums', className)}>
            0{suffix}
        </span>
    );
}

export function SegmentedControl({ options, value, onChange, label, className }) {
    return (
        <div role="radiogroup" aria-label={label} className={cn('inline-flex rounded-xl bg-slate-100 p-1', className)}>
            {options.map((option) => (
                <button
                    key={option.value}
                    type="button"
                    role="radio"
                    aria-checked={value === option.value}
                    onClick={() => onChange(option.value)}
                    className={cn(
                        'rounded-lg px-3 py-1.5 text-xs font-semibold whitespace-nowrap transition-colors focus-visible:ring-2 focus-visible:ring-ring/40 focus-visible:outline-none',
                        value === option.value ? 'bg-white text-brand-hover shadow-xs' : 'text-slate-500 hover:text-ink',
                    )}
                >
                    {option.label}
                </button>
            ))}
        </div>
    );
}
