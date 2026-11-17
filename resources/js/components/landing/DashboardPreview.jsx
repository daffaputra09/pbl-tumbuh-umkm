import { motion } from 'motion/react';
import {
    Analytics01Icon,
    CheckListIcon,
    CheckmarkBadge01Icon,
    Alert02Icon,
    Clock01Icon,
    DashboardSquare01Icon,
    Factory01Icon,
    FileExportIcon,
    GiftIcon,
    LegalDocument01Icon,
    Megaphone01Icon,
    Money03Icon,
    Plant02Icon,
    SmartPhone01Icon,
    Store01Icon,
    TeachingIcon,
    UserGroupIcon,
} from '@hugeicons/core-free-icons';
import CountUp from '@/components/reactbits/CountUp';
import { Icon, SampleLabel } from '@/components/landing/shared';
import { cn } from '@/lib/utils';

const sidebarGroups = [
    {
        title: 'Utama',
        items: [{ label: 'Dashboard', icon: DashboardSquare01Icon, active: true }],
    },
    {
        title: 'Pengelolaan',
        items: [
            { label: 'Data UMKM', icon: Store01Icon },
            { label: 'Program bantuan', icon: GiftIcon },
        ],
    },
    {
        title: 'Tindak lanjut',
        items: [{ label: 'Ajukan tindak lanjut', icon: TeachingIcon }],
    },
    {
        title: 'Analisis',
        items: [
            { label: 'Pengelompokan', icon: Analytics01Icon },
            { label: 'Laporan', icon: FileExportIcon },
        ],
    },
];

const stats = [
    { label: 'UMKM terdaftar', value: 148, note: '+12 dalam 12 bulan terakhir', icon: Store01Icon, tone: 'bg-brand-50 text-brand' },
    { label: 'UMKM aktif', value: 127, note: '86% dari total masih beroperasi', icon: UserGroupIcon, tone: 'bg-green-50 text-success' },
    { label: 'Data terverifikasi', value: 109, note: '3 menunggu dicek', icon: CheckmarkBadge01Icon, tone: 'bg-cyan-50 text-cyan-700', highlight: true },
    { label: 'Sudah dibina', value: 64, note: '59% dari UMKM terverifikasi', icon: TeachingIcon, tone: 'bg-sun-50 text-sun' },
];

const obstacles = [
    { label: 'Modal', icon: Money03Icon, high: 16, moderate: 5, low: 3 },
    { label: 'Pemasaran', icon: Megaphone01Icon, high: 11, moderate: 8, low: 5 },
    { label: 'Digitalisasi', icon: SmartPhone01Icon, high: 8, moderate: 9, low: 7 },
    { label: 'Legalitas', icon: LegalDocument01Icon, high: 5, moderate: 7, low: 12 },
    { label: 'Produksi', icon: Factory01Icon, high: 3, moderate: 6, low: 15 },
];

function greeting(date) {
    const hour = date.getHours();

    if (hour < 11) {
        return 'Selamat pagi';
    }

    if (hour < 15) {
        return 'Selamat siang';
    }

    return hour < 18 ? 'Selamat sore' : 'Selamat malam';
}

const ASSESSED = 24;

const queue = [
    { name: 'Keripik Tempe Bu Sri', detail: 'Menunggu dicek · Krajan', days: '3 hari', overdue: false },
    { name: 'Batik Tulis Bu Lestari', detail: 'Menunggu dicek · Sumbersari', days: '9 hari', overdue: true },
    { name: 'Madu Hutan Pak Darto', detail: 'Perlu perbaikan · Karanganyar', days: '2 hari', overdue: false, revision: true },
];

export default function DashboardPreview() {
    const now = new Date();
    const dateLabel = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });

    return (
        <div className="overflow-hidden rounded-[1.25rem] border border-border bg-background text-left shadow-[0_40px_80px_-30px_rgb(15_23_42/0.35)] ring-8 ring-white/60">
            <div className="flex">
                <aside className="hidden w-52 shrink-0 flex-col border-r border-border bg-white md:flex">
                    <div className="flex h-14 items-center gap-2 px-4">
                        <span className="grid size-8 place-items-center rounded-xl bg-brand text-white">
                            <Icon icon={Plant02Icon} size={16} strokeWidth={2} />
                        </span>
                        <span className="text-[15px] font-extrabold tracking-tight text-ink">
                            Tumbuh<span className="text-brand">UMKM</span>
                        </span>
                    </div>
                    <nav className="flex flex-col px-2.5 pb-4" aria-hidden="true">
                        {sidebarGroups.map((group) => (
                            <div key={group.title} className="mt-3 first:mt-1">
                                <p className="px-2.5 pb-1.5 text-[10px] font-bold tracking-wider text-slate-400 uppercase">{group.title}</p>
                                <ul className="flex flex-col gap-0.5">
                                    {group.items.map((item) => (
                                        <li key={item.label}>
                                            <span
                                                className={cn(
                                                    'flex items-center gap-2.5 rounded-xl px-2.5 py-2 text-[11px] font-semibold',
                                                    item.active ? 'bg-brand text-white' : 'text-slate-600',
                                                )}
                                            >
                                                <Icon icon={item.icon} size={15} />
                                                {item.label}
                                            </span>
                                        </li>
                                    ))}
                                </ul>
                            </div>
                        ))}
                    </nav>
                </aside>

                <div className="min-w-0 flex-1">
                    <header className="flex h-14 items-center gap-3 border-b border-border bg-white px-3 sm:px-4">
                        <div className="min-w-0 flex-1">
                            <p className="truncate text-[10px] font-medium text-slate-500">
                                <span className="hidden sm:inline">Tumbuh UMKM / </span>Dashboard Petugas
                            </p>
                            <p className="truncate text-xs font-bold text-ink sm:text-sm">Kampung Rejoso</p>
                        </div>
                        <SampleLabel />
                        <span className="grid size-8 place-items-center rounded-xl text-slate-600">
                            <Icon icon={CheckListIcon} size={16} />
                        </span>
                        <span className="flex items-center gap-2 border-l border-border pl-2.5">
                            <span className="grid size-8 place-items-center rounded-full bg-brand-100 text-[10px] font-bold text-brand-hover">PD</span>
                            <span className="hidden leading-tight sm:block">
                                <span className="block text-[11px] font-semibold text-ink">Petugas Desa</span>
                                <span className="block text-[10px] text-slate-500">Petugas</span>
                            </span>
                        </span>
                    </header>

                    <div className="flex flex-col gap-3 p-3 sm:p-4">
                        <section className="relative isolate overflow-hidden rounded-2xl bg-gradient-to-br from-brand-900 via-brand-800 to-brand-600 px-4 py-4 text-white sm:px-5">
                            <p className="text-[11px] text-brand-100">{dateLabel}</p>
                            <p className="mt-1 text-sm font-extrabold tracking-tight sm:text-base">{greeting(now)}, Petugas Desa</p>
                            <p className="mt-1 text-[11px] leading-relaxed text-brand-50/90 sm:text-xs">
                                <strong className="font-bold text-sun-200">3 data</strong> menunggu verifikasi dan{' '}
                                <strong className="font-bold text-sun-200">12 UMKM</strong> berkebutuhan tinggi belum dibina.
                            </p>
                            <div className="mt-3 flex flex-wrap gap-2">
                                <span className="inline-flex h-8 items-center gap-1.5 rounded-full bg-white px-3 text-[11px] font-semibold text-brand-hover">
                                    <Icon icon={CheckListIcon} size={14} />
                                    Cek antrean
                                </span>
                                <span className="inline-flex h-8 items-center gap-1.5 rounded-full border border-white/30 bg-white/5 px-3 text-[11px] font-semibold text-white">
                                    <Icon icon={FileExportIcon} size={14} />
                                    Cetak ringkasan
                                </span>
                            </div>
                        </section>

                        <div className="grid grid-cols-2 gap-2 xl:grid-cols-4">
                            {stats.map((stat, index) => (
                                <article key={stat.label} className="flex flex-col justify-between gap-2 rounded-2xl border border-border bg-white p-3 shadow-xs">
                                    <div className="flex items-start justify-between gap-2">
                                        <span className={cn('grid size-8 place-items-center rounded-xl', stat.tone)}>
                                            <Icon icon={stat.icon} size={16} />
                                        </span>
                                    </div>
                                    <div>
                                        <p className="text-[11px] font-medium text-muted-foreground">{stat.label}</p>
                                        <CountUp to={stat.value} delay={0.3 + index * 0.08} className="text-xl font-extrabold tracking-tight text-ink tabular-nums" />
                                    </div>
                                    <p className={cn('text-[10px] leading-snug font-medium', stat.highlight ? 'w-fit rounded-md bg-sun-50 px-1.5 py-0.5 text-sun-700' : 'text-muted-foreground')}>
                                        {stat.note}
                                    </p>
                                </article>
                            ))}
                        </div>

                        <div className="grid gap-2 lg:grid-cols-5">
                            <section className="rounded-2xl border border-border bg-white p-3.5 shadow-xs lg:col-span-3">
                                <div className="flex flex-wrap items-start justify-between gap-2">
                                    <div>
                                        <p className="text-xs font-bold text-ink sm:text-sm">Status kendala per bidang</p>
                                        <p className="mt-0.5 text-[10px] text-muted-foreground sm:text-[11px]">Skor asesmen terkini, dari kebutuhan tinggi terbanyak.</p>
                                    </div>
                                    <span className="inline-flex rounded-xl bg-slate-100 p-0.5 text-[10px] font-semibold">
                                        <span className="rounded-lg bg-white px-2 py-1 text-brand-hover shadow-xs">Jumlah</span>
                                        <span className="px-2 py-1 text-slate-500">Persen</span>
                                    </span>
                                </div>
                                <ul className="mt-3 flex flex-wrap gap-x-3 gap-y-1 text-[10px] text-muted-foreground">
                                    <li className="flex items-center gap-1.5"><span className="size-2 rounded-full bg-sun-700" />Kebutuhan tinggi</li>
                                    <li className="flex items-center gap-1.5"><span className="size-2 rounded-full bg-sun-400" />Kebutuhan sedang</li>
                                    <li className="flex items-center gap-1.5"><span className="size-2 rounded-full bg-brand-200" />Kebutuhan rendah</li>
                                </ul>
                                <ul className="mt-3 flex flex-col gap-2.5">
                                    {obstacles.map((obstacle, index) => (
                                        <li key={obstacle.label} className="grid grid-cols-[auto_1fr] items-center gap-2">
                                            <span className="grid size-7 place-items-center rounded-lg bg-brand-50 text-brand">
                                                <Icon icon={obstacle.icon} size={14} />
                                            </span>
                                            <div className="min-w-0">
                                                <div className="flex items-baseline justify-between gap-2">
                                                    <p className="truncate text-[11px] font-semibold text-ink">{obstacle.label}</p>
                                                    <p className="shrink-0 text-[10px] text-muted-foreground">
                                                        <span className="font-bold text-sun-700 tabular-nums">{obstacle.high}</span> tinggi
                                                    </p>
                                                </div>
                                                <div className="mt-1 flex h-2 overflow-hidden rounded-full bg-slate-100" aria-hidden="true">
                                                    {[
                                                        ['high', obstacle.high, 'bg-sun-700'],
                                                        ['moderate', obstacle.moderate, 'bg-sun-400'],
                                                        ['low', obstacle.low, 'bg-brand-200'],
                                                    ].map(([key, value, color]) => (
                                                        <motion.span
                                                            key={key}
                                                            className={cn('h-full first:rounded-l-full last:rounded-r-full', color)}
                                                            initial={{ width: 0 }}
                                                            whileInView={{ width: `${(value / ASSESSED) * 100}%` }}
                                                            viewport={{ once: true }}
                                                            transition={{ duration: 0.8, delay: 0.2 + index * 0.05, ease: [0.22, 1, 0.36, 1] }}
                                                        />
                                                    ))}
                                                </div>
                                            </div>
                                        </li>
                                    ))}
                                </ul>
                            </section>

                            <section className="rounded-2xl border border-border bg-white p-3.5 shadow-xs lg:col-span-2">
                                <div className="flex items-start justify-between gap-2">
                                    <div>
                                        <p className="text-xs font-bold text-ink sm:text-sm">Antrean verifikasi</p>
                                        <p className="mt-0.5 text-[10px] text-muted-foreground sm:text-[11px]">Data paling lama menunggu tampil di atas.</p>
                                    </div>
                                    <span className="shrink-0 text-[10px] font-semibold text-brand">Buka verifikasi (3)</span>
                                </div>
                                <ul className="mt-3 flex flex-col divide-y divide-border">
                                    {queue.map((item) => (
                                        <li key={item.name} className="flex items-center gap-2.5 py-2.5 first:pt-0 last:pb-0">
                                            <span className={cn('grid size-8 shrink-0 place-items-center rounded-xl', item.revision ? 'bg-rose-50 text-rose-600' : 'bg-sun-50 text-sun')}>
                                                <Icon icon={item.revision ? Alert02Icon : Clock01Icon} size={15} />
                                            </span>
                                            <div className="min-w-0 flex-1">
                                                <p className="truncate text-[11px] font-semibold text-ink">{item.name}</p>
                                                <p className="truncate text-[10px] text-muted-foreground">{item.detail}</p>
                                            </div>
                                            <span className={cn('shrink-0 rounded-md px-1.5 py-0.5 text-[10px] font-semibold tabular-nums', item.overdue ? 'bg-rose-50 text-rose-700' : 'bg-slate-100 text-slate-600')}>
                                                {item.days}
                                            </span>
                                        </li>
                                    ))}
                                </ul>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
