import { motion } from 'motion/react';
import { HugeiconsIcon } from '@hugeicons/react';
import { Plant02Icon } from '@hugeicons/core-free-icons';
import { cn } from '@/lib/utils';

export function Icon({ icon, size = 20, strokeWidth = 1.8, className }) {
    return <HugeiconsIcon icon={icon} size={size} strokeWidth={strokeWidth} className={className} aria-hidden="true" />;
}

export function Reveal({ children, delay = 0, className, as = 'div' }) {
    const MotionTag = motion[as];

    return (
        <MotionTag
            className={className}
            initial={{ opacity: 0, y: 16 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true, amount: 0.2 }}
            transition={{ duration: 0.6, delay, ease: [0.22, 1, 0.36, 1] }}
        >
            {children}
        </MotionTag>
    );
}

/**
 * Garis bawah amber bergaya coretan tangan, motif yang sama dengan judul hero.
 */
export function Underline({ children, className }) {
    return (
        <span className={cn('relative inline-block whitespace-nowrap', className)}>
            {children}
            <svg aria-hidden viewBox="0 0 300 20" preserveAspectRatio="none" className="absolute -bottom-1.5 left-0 h-2.5 w-full text-sun sm:h-3">
                <motion.path
                    d="M3 14 C 60 4, 140 4, 200 10 S 280 16, 297 6"
                    fill="none"
                    stroke="currentColor"
                    strokeWidth="5"
                    strokeLinecap="round"
                    initial={{ pathLength: 0 }}
                    whileInView={{ pathLength: 1 }}
                    viewport={{ once: true, amount: 1 }}
                    transition={{ duration: 0.8, delay: 0.3, ease: 'easeInOut' }}
                />
            </svg>
        </span>
    );
}

export function SectionTitle({ children, className, light = false }) {
    return (
        <h2
            className={cn(
                'text-[1.75rem] leading-[1.15] font-extrabold tracking-tight text-balance sm:text-4xl lg:text-[2.75rem]',
                light ? 'text-white' : 'text-ink',
                className,
            )}
        >
            {children}
        </h2>
    );
}

export function Logo({ className, light = false }) {
    return (
        <a href="#beranda" className={cn('group flex items-center gap-2.5', className)} aria-label="Tumbuh UMKM, kembali ke atas">
            <span className="relative grid size-9 place-items-center overflow-hidden rounded-xl bg-gradient-to-br from-brand-500 to-brand-hover text-white shadow-[0_6px_16px_-6px_rgb(15_118_110/0.9)] transition-transform duration-300 group-hover:-rotate-6">
                <span className="absolute inset-x-0 top-0 h-1/2 bg-white/15" />
                <Icon icon={Plant02Icon} size={20} strokeWidth={2} className="relative" />
            </span>
            <span className={cn('text-[17px] font-extrabold tracking-tight', light ? 'text-white' : 'text-ink')}>
                Tumbuh<span className={light ? 'text-brand-300' : 'text-brand'}>UMKM</span>
            </span>
        </a>
    );
}

export function SampleLabel({ className }) {
    return (
        <span className={cn('rounded-md bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-600', className)}>Contoh tampilan</span>
    );
}

export const navigationLinks = [
    { label: 'Masalah', href: '#masalah' },
    { label: 'Alur', href: '#cara-kerja' },
    { label: 'Analisis', href: '#profiling' },
    { label: 'Fitur', href: '#fitur' },
    { label: 'Pengguna', href: '#pengguna' },
    { label: 'FAQ', href: '#faq' },
];
