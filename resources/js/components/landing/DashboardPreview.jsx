import { motion } from 'motion/react';
import {
    Analytics01Icon,
    Calendar03Icon,
    CheckmarkCircle02Icon,
    DashboardSquare01Icon,
    DatabaseIcon,
    File02Icon,
    Idea01Icon,
    LegalDocument01Icon,
    Notification01Icon,
    Search01Icon,
    Store04Icon,
    Task01Icon,
    UserGroupIcon,
} from '@hugeicons/core-free-icons';
import CountUp from '@/components/reactbits/CountUp';
import { Icon } from '@/components/landing/shared';
import { cn } from '@/lib/utils';

const sidebarItems = [
    { label: 'Dashboard', icon: DashboardSquare01Icon, active: true },
    { label: 'Data UMKM', icon: Store04Icon },
    { label: 'Verifikasi', icon: CheckmarkCircle02Icon },
    { label: 'Kendala', icon: Analytics01Icon },
    { label: 'Program', icon: Idea01Icon },
    { label: 'Pembinaan', icon: Task01Icon },
    { label: 'Laporan', icon: File02Icon },
];

const stats = [
    { label: 'Total UMKM', value: 128, icon: DatabaseIcon, tone: 'bg-brand-50 text-brand' },
    { label: 'Terverifikasi', value: 97, icon: CheckmarkCircle02Icon, tone: 'bg-green-50 text-success' },
    { label: 'Belum punya NIB', value: 41, icon: LegalDocument01Icon, tone: 'bg-sun-50 text-sun-700' },
    { label: 'Sudah dibina', value: 52, icon: UserGroupIcon, tone: 'bg-slate-100 text-ink' },
];

const obstacles = [
    { label: 'Modal', value: 68, color: 'bg-brand' },
    { label: 'Pemasaran', value: 54, color: 'bg-brand-500' },
    { label: 'Digitalisasi', value: 41, color: 'bg-brand-400' },
    { label: 'Legalitas', value: 29, color: 'bg-sun' },
    { label: 'Produksi', value: 18, color: 'bg-sun-400' },
];

const followUps = [
    { name: 'Keripik Tempe Bu Sri', program: 'Pelatihan pemasaran digital', status: 'Selesai', tone: 'bg-green-50 text-success' },
    { name: 'Batik Tulis Lestari', program: 'Pendampingan NIB', status: 'Proses', tone: 'bg-sun-50 text-sun-700' },
    { name: 'Kopi Lereng Wilis', program: 'Akses KUR mikro', status: 'Dijadwalkan', tone: 'bg-brand-50 text-brand' },
];

export default function DashboardPreview() {
    return (
        <div className="overflow-hidden rounded-[1.25rem] border border-slate-200/80 bg-white shadow-[0_40px_80px_-30px_rgb(15_23_42/0.35)] ring-8 ring-white/60">
            <div className="flex items-center gap-3 border-b bg-slate-50/80 px-4 py-3">
                <div className="flex gap-1.5">
                    <span className="size-3 rounded-full bg-[#FF5F57]" />
                    <span className="size-3 rounded-full bg-[#FEBC2E]" />
                    <span className="size-3 rounded-full bg-[#28C840]" />
                </div>
                <div className="mx-auto flex h-7 w-full max-w-xs items-center justify-center gap-1.5 rounded-md border bg-white text-[11px] text-slate-500">
                    <span className="size-1.5 rounded-full bg-success" />
                    tumbuhumkm.desa.id/dashboard
                </div>
                <div className="w-12" />
            </div>

            <div className="flex text-left">
                <aside className="hidden w-48 shrink-0 flex-col gap-1 border-r bg-slate-50/50 p-3 md:flex">
                    <p className="px-2 pt-1 pb-2 text-[10px] font-semibold tracking-wider text-slate-400 uppercase">Menu Petugas</p>
                    {sidebarItems.map((item) => (
                        <div
                            key={item.label}
                            className={cn(
                                'flex items-center gap-2.5 rounded-lg px-2.5 py-2 text-xs font-medium',
                                item.active ? 'bg-brand text-white shadow-sm' : 'text-slate-600',
                            )}
                        >
                            <Icon icon={item.icon} size={15} />
                            {item.label}
                        </div>
                    ))}
                </aside>

                <div className="flex min-w-0 flex-1 flex-col gap-4 p-4 sm:p-5">
                    <div className="flex items-center justify-between gap-3">
                        <div>
                            <p className="text-[11px] font-medium text-slate-500">Selamat pagi, Petugas Desa</p>
                            <p className="text-sm font-bold text-ink sm:text-base">Ringkasan UMKM Desa Sukamaju</p>
                        </div>
                        <div className="flex items-center gap-2">
                            <span className="hidden h-8 items-center gap-1.5 rounded-lg border px-2.5 text-[11px] text-slate-500 sm:flex">
                                <Icon icon={Search01Icon} size={13} />
                                Cari UMKM
                            </span>
                            <span className="hidden h-8 items-center gap-1.5 rounded-lg border px-2.5 text-[11px] text-slate-500 sm:flex">
                                <Icon icon={Calendar03Icon} size={13} />
                                Sep 2026
                            </span>
                            <span className="relative grid size-8 place-items-center rounded-lg border text-slate-500">
                                <Icon icon={Notification01Icon} size={15} />
                                <span className="absolute top-1.5 right-1.5 size-1.5 rounded-full bg-sun" />
                            </span>
                        </div>
                    </div>

                    <div className="grid grid-cols-2 gap-2.5 lg:grid-cols-4">
                        {stats.map((stat, index) => (
                            <div key={stat.label} className="flex flex-col gap-2 rounded-xl border p-3">
                                <div className="flex items-center justify-between">
                                    <span className="text-[11px] font-medium text-slate-500">{stat.label}</span>
                                    <span className={cn('grid size-6 place-items-center rounded-md', stat.tone)}>
                                        <Icon icon={stat.icon} size={13} strokeWidth={2} />
                                    </span>
                                </div>
                                <CountUp to={stat.value} delay={0.4 + index * 0.1} className="text-xl font-extrabold tracking-tight text-ink sm:text-2xl" />
                            </div>
                        ))}
                    </div>

                    <div className="grid gap-2.5 lg:grid-cols-5">
                        <div className="rounded-xl border p-3.5 lg:col-span-3">
                            <div className="mb-3 flex items-center justify-between">
                                <p className="text-xs font-bold text-ink">Kendala paling banyak dialami</p>
                                <span className="text-[10px] text-slate-400">% dari total UMKM</span>
                            </div>
                            <div className="flex flex-col gap-2.5">
                                {obstacles.map((obstacle, index) => (
                                    <div key={obstacle.label} className="grid grid-cols-[76px_1fr_32px] items-center gap-2 text-[11px]">
                                        <span className="font-medium text-slate-600">{obstacle.label}</span>
                                        <div className="h-2 overflow-hidden rounded-full bg-slate-100">
                                            <motion.div
                                                className={cn('h-full rounded-full', obstacle.color)}
                                                initial={{ width: 0 }}
                                                whileInView={{ width: `${obstacle.value}%` }}
                                                viewport={{ once: true }}
                                                transition={{ duration: 1.2, delay: 0.5 + index * 0.1, ease: [0.22, 1, 0.36, 1] }}
                                            />
                                        </div>
                                        <span className="text-right font-semibold text-ink">{obstacle.value}%</span>
                                    </div>
                                ))}
                            </div>
                        </div>

                        <div className="rounded-xl border p-3.5 lg:col-span-2">
                            <p className="mb-3 text-xs font-bold text-ink">Tindak lanjut terbaru</p>
                            <div className="flex flex-col gap-2.5">
                                {followUps.map((item) => (
                                    <div key={item.name} className="flex items-center justify-between gap-2">
                                        <div className="min-w-0">
                                            <p className="truncate text-[11px] font-semibold text-ink">{item.name}</p>
                                            <p className="truncate text-[10px] text-slate-500">{item.program}</p>
                                        </div>
                                        <span className={cn('shrink-0 rounded-full px-2 py-0.5 text-[10px] font-semibold', item.tone)}>{item.status}</span>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
