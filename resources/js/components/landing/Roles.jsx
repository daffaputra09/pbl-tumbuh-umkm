import { motion } from 'motion/react';
import {
    Alert02Icon,
    CheckmarkCircle02Icon,
    Clock01Icon,
    CrownIcon,
    Idea01Icon,
    Tag01Icon,
    User02Icon,
    UserCheck01Icon,
    ViewIcon,
} from '@hugeicons/core-free-icons';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Icon, Reveal, SampleLabel, SectionTitle, Underline } from '@/components/landing/shared';
import { cn } from '@/lib/utils';

const roles = [
    {
        value: 'umkm',
        label: 'Pemilik usaha',
        icon: User02Icon,
        title: 'Isi data sekali, perbarui kapan saja',
        description:
            'Pemilik usaha mengisi profil dan kendalanya sendiri, lalu bisa mengecek apakah data sudah diperiksa petugas. Hasil analisis ditulis dengan bahasa sehari-hari.',
        tasks: ['Mengisi dan memperbarui data usaha', 'Menjawab pertanyaan soal kendala', 'Melihat status pemeriksaan data', 'Melihat program yang disarankan'],
        visual: OwnerVisual,
    },
    {
        value: 'petugas',
        label: 'Petugas desa',
        icon: UserCheck01Icon,
        title: 'Pekerjaan harian petugas ada di satu layar',
        description:
            'Petugas memeriksa data yang masuk, mengelola daftar program, melihat hasil pengelompokan, dan mencatat setiap pembinaan yang sudah dilakukan.',
        tasks: ['Memeriksa dan memperbaiki data UMKM', 'Mengelola produk, legalitas, dan program', 'Melihat hasil pengelompokan', 'Mencatat tindak lanjut pembinaan'],
        visual: OfficerVisual,
    },
    {
        value: 'kepala',
        label: 'Kepala desa',
        icon: CrownIcon,
        title: 'Lihat ringkasannya, putuskan langkahnya',
        description:
            'Kepala desa tidak perlu mengurus data harian. Cukup buka dashboard untuk melihat kondisi UMKM, lalu setujui tindak lanjut yang diajukan petugas.',
        tasks: ['Membaca dashboard dan laporan', 'Melihat kendala yang paling banyak dialami', 'Memantau perkembangan pembinaan', 'Menyetujui tindak lanjut'],
        visual: HeadVisual,
    },
];

export default function Roles() {
    return (
        <section id="pengguna" className="relative border-t bg-white py-20 sm:py-28">
            <div className="mx-auto max-w-6xl px-5">
                <Reveal className="mx-auto max-w-2xl text-center">
                    <SectionTitle>
                        Tiga pengguna, masing-masing dengan <Underline>tugasnya</Underline>
                    </SectionTitle>
                </Reveal>

                <Reveal delay={0.1}>
                    <Tabs defaultValue="umkm" className="mt-10 items-center gap-8">
                        <TabsList className="w-full sm:w-auto">
                            {roles.map((role) => (
                                <TabsTrigger
                                    key={role.value}
                                    value={role.value}
                                    className="flex-1 px-2 text-[13px] sm:flex-none sm:px-5 sm:text-sm [&_svg]:hidden sm:[&_svg]:block"
                                >
                                    <Icon icon={role.icon} />
                                    {role.label}
                                </TabsTrigger>
                            ))}
                        </TabsList>

                        {roles.map((role) => (
                            <TabsContent key={role.value} value={role.value} className="w-full">
                                <motion.div
                                    initial={{ opacity: 0, y: 12 }}
                                    animate={{ opacity: 1, y: 0 }}
                                    transition={{ duration: 0.35, ease: [0.22, 1, 0.36, 1] }}
                                    className="grid items-center gap-10 lg:grid-cols-2 lg:gap-16"
                                >
                                    <div className="flex flex-col gap-5">
                                        <h3 className="text-2xl leading-tight font-extrabold tracking-tight text-balance text-ink sm:text-3xl">{role.title}</h3>
                                        <p className="leading-relaxed text-muted-foreground">{role.description}</p>
                                        <ul className="flex flex-col divide-y divide-slate-200 border-y border-slate-200">
                                            {role.tasks.map((task) => (
                                                <li key={task} className="py-3 text-[15px] font-medium text-ink">
                                                    {task}
                                                </li>
                                            ))}
                                        </ul>
                                    </div>
                                    <div className="rounded-3xl bg-canvas p-5 ring-1 ring-slate-200 sm:p-8">
                                        <role.visual />
                                    </div>
                                </motion.div>
                            </TabsContent>
                        ))}
                    </Tabs>
                </Reveal>
            </div>
        </section>
    );
}

function Panel({ children, className }) {
    return <div className={cn('rounded-2xl bg-white p-4 ring-1 ring-slate-200', className)}>{children}</div>;
}

function VisualHeader({ title }) {
    return (
        <div className="mb-4 flex items-center justify-between gap-3">
            <p className="text-sm font-bold text-ink">{title}</p>
            <SampleLabel />
        </div>
    );
}

function OwnerVisual() {
    return (
        <div className="mx-auto flex max-w-sm flex-col gap-3">
            <VisualHeader title="Dashboard Usaha" />
            <Panel>
                <div className="flex items-center gap-3">
                    <span className="grid size-10 place-items-center rounded-xl bg-success/10 text-success">
                        <Icon icon={CheckmarkCircle02Icon} size={20} />
                    </span>
                    <p className="text-sm font-medium text-muted-foreground">Status Verifikasi</p>
                </div>
                <p className="mt-3 text-base font-bold text-ink">Terverifikasi</p>
                <p className="mt-1 text-sm leading-relaxed text-muted-foreground">Data usaha kamu sudah lengkap dan aktif.</p>
            </Panel>
            <Panel>
                <div className="flex items-start justify-between gap-2">
                    <span className="grid size-10 place-items-center rounded-xl bg-brand-50 text-primary">
                        <Icon icon={Idea01Icon} size={20} />
                    </span>
                    <span className="rounded-full bg-muted px-2.5 py-0.5 text-[11px] font-semibold text-muted-foreground">Pemasaran</span>
                </div>
                <p className="mt-3 text-sm font-medium text-muted-foreground">Program yang Cocok</p>
                <p className="mt-1 text-base font-bold text-ink">Pelatihan pemasaran lewat media sosial</p>
                <p className="mt-0.5 text-xs font-medium text-primary">Desa</p>
            </Panel>
            <Panel className="flex items-center gap-3">
                <span className="grid size-10 shrink-0 place-items-center rounded-xl bg-sun-50 text-sun-700">
                    <Icon icon={Tag01Icon} size={20} />
                </span>
                <div>
                    <p className="text-sm font-medium text-muted-foreground">Kendala Utama</p>
                    <p className="text-base font-bold text-ink">Pemasaran</p>
                </div>
            </Panel>
        </div>
    );
}

function OfficerVisual() {
    const queue = [
        { name: 'Keripik Tempe Bu Sri', detail: 'Menunggu dicek · Krajan', days: '3 hari', revision: false, overdue: false },
        { name: 'Batik Tulis Bu Lestari', detail: 'Menunggu dicek · Sumbersari', days: '9 hari', revision: false, overdue: true },
        { name: 'Madu Hutan Pak Darto', detail: 'Perlu perbaikan · Karanganyar', days: '2 hari', revision: true, overdue: false },
    ];

    return (
        <div className="mx-auto max-w-sm">
            <VisualHeader title="Antrean verifikasi" />
            <Panel className="flex flex-col divide-y divide-border p-0">
                {queue.map((item) => (
                    <div key={item.name} className="flex items-center gap-3 px-4 py-3">
                        <span className={cn('grid size-9 shrink-0 place-items-center rounded-xl', item.revision ? 'bg-rose-50 text-rose-600' : 'bg-sun-50 text-sun')}>
                            <Icon icon={item.revision ? Alert02Icon : Clock01Icon} size={18} />
                        </span>
                        <div className="min-w-0 flex-1">
                            <p className="truncate text-sm font-semibold text-ink">{item.name}</p>
                            <p className="text-xs text-muted-foreground">{item.detail}</p>
                        </div>
                        <span className={cn('shrink-0 rounded-md px-2 py-0.5 text-xs font-semibold tabular-nums', item.overdue ? 'bg-rose-50 text-rose-700' : 'bg-slate-100 text-slate-600')}>
                            {item.days}
                        </span>
                    </div>
                ))}
            </Panel>
        </div>
    );
}

function HeadVisual() {
    return (
        <div className="mx-auto flex max-w-sm flex-col gap-3">
            <VisualHeader title="Persetujuan tindak lanjut" />
            <Panel>
                <div className="flex items-center justify-between gap-2">
                    <p className="text-sm font-semibold text-ink">Keripik Tempe Bu Sri</p>
                    <span className="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-semibold text-amber-800">Menunggu</span>
                </div>
                <p className="mt-2 text-sm font-medium text-primary">Pelatihan pengemasan produk</p>
                <p className="mt-1 text-xs text-muted-foreground">Rencana: foto produk dan kemasan di balai desa.</p>
                <p className="mt-2 text-xs text-slate-500">Diajukan oleh Petugas Desa</p>
                <div className="mt-4 flex flex-wrap items-center justify-end gap-2">
                    <span className="inline-flex items-center gap-1.5 rounded-full border border-border bg-white px-3 py-1.5 text-xs font-semibold text-ink">
                        <Icon icon={ViewIcon} size={14} />
                        Tinjau Pengajuan
                    </span>
                    <span className="inline-flex items-center rounded-full border border-red-200 bg-white px-3 py-1.5 text-xs font-semibold text-red-600">Tolak</span>
                    <span className="inline-flex items-center rounded-full bg-success px-3 py-1.5 text-xs font-semibold text-white">Setujui</span>
                </div>
            </Panel>
        </div>
    );
}
