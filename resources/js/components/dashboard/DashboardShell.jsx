import { useEffect, useState } from 'react';
import { AnimatePresence, motion } from 'motion/react';
import {
    Analytics01Icon,
    Cancel01Icon,
    CheckListIcon,
    DashboardSquare01Icon,
    FileExportIcon,
    GiftIcon,
    Home01Icon,
    Menu01Icon,
    Notification01Icon,
    Plant02Icon,
    Store01Icon,
    Tag01Icon,
    TeachingIcon,
} from '@hugeicons/core-free-icons';
import { Icon } from '@/components/landing/shared';
import { cn } from '@/lib/utils';

const NAVIGATION = [
    {
        title: 'Utama',
        items: [{ label: 'Dashboard', icon: DashboardSquare01Icon, href: '/dashboard', isActive: true }],
    },
    {
        title: 'Pengelolaan',
        items: [
            { label: 'Data UMKM', icon: Store01Icon },
            { label: 'Verifikasi Data', icon: CheckListIcon },
            { label: 'Riwayat Pembinaan', icon: TeachingIcon },
            { label: 'Kategori Kendala', icon: Tag01Icon },
        ],
    },
    {
        title: 'Analisis',
        items: [
            { label: 'Smart Profiling', icon: Analytics01Icon },
            { label: 'Rekomendasi Bantuan', icon: GiftIcon },
            { label: 'Laporan', icon: FileExportIcon },
        ],
    },
];

const ROLE_OPTIONS = [
    { key: 'petugas', label: 'Petugas' },
    { key: 'kepala-desa', label: 'Kepala Desa' },
];

export const ROLE_LABELS = {
    petugas: 'Petugas Desa',
    'kepala-desa': 'Kepala Desa',
};

function Brand() {
    return (
        <a href="/" className="group flex items-center gap-2.5" aria-label="Tumbuh UMKM, ke halaman utama">
            <span className="relative grid size-9 place-items-center overflow-hidden rounded-xl bg-gradient-to-br from-brand-500 to-brand-hover text-white shadow-[0_6px_16px_-6px_rgb(15_118_110/0.9)] transition-transform duration-300 group-hover:-rotate-6">
                <span className="absolute inset-x-0 top-0 h-1/2 bg-white/15" />
                <Icon icon={Plant02Icon} size={20} strokeWidth={2} className="relative" />
            </span>
            <span className="text-[17px] font-extrabold tracking-tight text-ink">
                Tumbuh<span className="text-brand">UMKM</span>
            </span>
        </a>
    );
}

function SidebarContent({ role, village }) {
    return (
        <div className="flex h-full flex-col">
            <div className="flex h-16 shrink-0 items-center px-5">
                <Brand />
            </div>

            <nav className="flex-1 overflow-y-auto px-3 pt-2 pb-6" aria-label="Menu dashboard">
                {NAVIGATION.map((group) => (
                    <div key={group.title} className="mt-5 first:mt-2">
                        <p className="px-3 pb-2 text-[11px] font-bold tracking-wider text-slate-400 uppercase">{group.title}</p>
                        <ul className="flex flex-col gap-0.5">
                            {group.items.map((item) => (
                                <li key={item.label}>
                                    {item.href ? (
                                        <a
                                            href={item.href}
                                            aria-current={item.isActive ? 'page' : undefined}
                                            className={cn(
                                                'flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition-colors',
                                                item.isActive
                                                    ? 'bg-brand text-white shadow-[0_8px_20px_-10px_rgb(15_118_110/0.9)]'
                                                    : 'text-slate-600 hover:bg-brand-50 hover:text-brand-hover',
                                            )}
                                        >
                                            <Icon icon={item.icon} size={19} />
                                            {item.label}
                                        </a>
                                    ) : (
                                        <span
                                            className="flex cursor-not-allowed items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-400"
                                            title="Halaman ini sedang dikerjakan"
                                        >
                                            <Icon icon={item.icon} size={19} />
                                            <span className="flex-1">{item.label}</span>
                                            {/* <span className="rounded-md bg-slate-100 px-1.5 py-0.5 text-[10px] font-semibold text-slate-500">Segera</span> */}
                                        </span>
                                    )}
                                </li>
                            ))}
                        </ul>
                    </div>
                ))}
            </nav>

            <div className="border-t border-border p-4">
                <p className="mb-2 text-[11px] font-bold tracking-wider text-slate-400 uppercase">Lihat sebagai</p>
                <div className="grid grid-cols-2 gap-1 rounded-xl bg-slate-100 p-1">
                    {ROLE_OPTIONS.map((option) => (
                        <a
                            key={option.key}
                            href={`/dashboard?peran=${option.key}`}
                            aria-current={role === option.key ? 'true' : undefined}
                            className={cn(
                                'rounded-lg px-2 py-1.5 text-center text-xs font-semibold transition-colors',
                                role === option.key ? 'bg-white text-brand-hover shadow-xs' : 'text-slate-500 hover:text-ink',
                            )}
                        >
                            {option.label}
                        </a>
                    ))}
                </div>
                <a
                    href="/"
                    className="mt-3 flex items-center gap-2 rounded-lg px-1 py-1 text-xs font-medium text-slate-500 transition-colors hover:text-brand-hover"
                >
                    <Icon icon={Home01Icon} size={15} />
                    {village.name}, {village.district}
                </a>
            </div>
        </div>
    );
}

export default function DashboardShell({ role, village, pendingCount, children }) {
    const [isMenuOpen, setIsMenuOpen] = useState(false);
    const initials = role === 'kepala-desa' ? 'KD' : 'PD';

    useEffect(() => {
        if (!isMenuOpen) {
            return;
        }

        const closeOnEscape = (event) => event.key === 'Escape' && setIsMenuOpen(false);
        document.addEventListener('keydown', closeOnEscape);
        document.body.style.overflow = 'hidden';

        return () => {
            document.removeEventListener('keydown', closeOnEscape);
            document.body.style.overflow = '';
        };
    }, [isMenuOpen]);

    return (
        <div className="min-h-screen bg-background lg:pl-64">
            <aside className="fixed inset-y-0 left-0 z-30 hidden w-64 border-r border-border bg-white lg:block print:hidden">
                <SidebarContent role={role} village={village} />
            </aside>

            <AnimatePresence>
                {isMenuOpen && (
                    <div className="fixed inset-0 z-50 lg:hidden" role="dialog" aria-modal="true" aria-label="Menu navigasi">
                        <motion.button
                            type="button"
                            aria-label="Tutup menu"
                            className="absolute inset-0 bg-ink/40 backdrop-blur-[2px]"
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            exit={{ opacity: 0 }}
                            onClick={() => setIsMenuOpen(false)}
                        />
                        <motion.aside
                            className="absolute inset-y-0 left-0 w-[min(18rem,85vw)] bg-white shadow-2xl"
                            initial={{ x: '-100%' }}
                            animate={{ x: 0 }}
                            exit={{ x: '-100%' }}
                            transition={{ type: 'spring', stiffness: 380, damping: 38 }}
                        >
                            <button
                                type="button"
                                onClick={() => setIsMenuOpen(false)}
                                className="absolute top-3.5 right-3 grid size-9 place-items-center rounded-lg text-slate-500 hover:bg-slate-100"
                                aria-label="Tutup menu"
                            >
                                <Icon icon={Cancel01Icon} size={20} />
                            </button>
                            <SidebarContent role={role} village={village} />
                        </motion.aside>
                    </div>
                )}
            </AnimatePresence>

            <header className="sticky top-0 z-20 border-b border-border bg-white/85 backdrop-blur-md print:hidden">
                <div className="mx-auto flex h-16 max-w-[1400px] items-center gap-3 px-4 sm:px-6 lg:px-8">
                    <button
                        type="button"
                        onClick={() => setIsMenuOpen(true)}
                        className="-ml-1 grid size-10 place-items-center rounded-xl text-ink hover:bg-slate-100 lg:hidden"
                        aria-label="Buka menu"
                    >
                        <Icon icon={Menu01Icon} size={22} />
                    </button>

                    <div className="min-w-0 flex-1">
                        <p className="truncate text-xs font-medium text-muted-foreground">
                            <span className="hidden sm:inline">Tumbuh UMKM / </span>Dashboard Utama
                        </p>
                        <p className="truncate text-sm font-bold text-ink sm:text-base">{village.name}</p>
                    </div>

                    <a
                        href="#antrean-verifikasi"
                        className="relative grid size-10 place-items-center rounded-xl text-slate-600 transition-colors hover:bg-brand-50 hover:text-brand-hover"
                        aria-label={`${pendingCount} data menunggu verifikasi`}
                    >
                        <Icon icon={Notification01Icon} size={21} />
                        {pendingCount > 0 && (
                            <span className="absolute top-1.5 right-1.5 grid min-w-4.5 place-items-center rounded-full bg-sun px-1 text-[10px] leading-4.5 font-bold text-white ring-2 ring-white">
                                {pendingCount > 99 ? '99+' : pendingCount}
                            </span>
                        )}
                    </a>

                    <div className="flex items-center gap-2.5 border-l border-border pl-3">
                        <span className="grid size-9 place-items-center rounded-full bg-brand-100 text-xs font-bold text-brand-hover">{initials}</span>
                        <span className="hidden leading-tight md:block">
                            <span className="block text-sm font-semibold text-ink">{ROLE_LABELS[role]}</span>
                            <span className="block text-xs text-muted-foreground">{village.name}</span>
                        </span>
                    </div>
                </div>
            </header>

            <main className="mx-auto max-w-[1400px] px-4 py-5 sm:px-6 sm:py-6 lg:px-8 lg:py-8">{children}</main>
        </div>
    );
}
