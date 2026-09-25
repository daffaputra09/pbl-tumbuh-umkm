import { motion } from 'motion/react';
import { CheckmarkCircle02Icon, CrownIcon, Idea01Icon, User02Icon, UserCheck01Icon } from '@hugeicons/core-free-icons';
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
            <VisualHeader title="Halaman usaha saya" />
            <Panel className="flex items-center gap-3">
                <Icon icon={CheckmarkCircle02Icon} size={22} className="text-success" />
                <div>
                    <p className="text-xs text-slate-600">Status data</p>
                    <p className="text-sm font-bold text-ink">Sudah diperiksa petugas</p>
                </div>
            </Panel>
            <Panel className="flex items-center gap-3">
                <Icon icon={Idea01Icon} size={22} className="text-sun-700" />
                <div>
                    <p className="text-xs text-slate-600">Program untuk usaha Anda</p>
                    <p className="text-sm font-bold text-ink">Pelatihan pemasaran lewat media sosial</p>
                </div>
            </Panel>
        </div>
    );
}

function OfficerVisual() {
    const queue = ['Usaha jamu tradisional', 'Usaha madu hutan', 'Usaha sambal rumahan'];

    return (
        <div className="mx-auto max-w-sm">
            <VisualHeader title="Menunggu diperiksa" />
            <Panel className="flex flex-col divide-y divide-slate-100 p-0">
                {queue.map((name) => (
                    <div key={name} className="flex items-center justify-between gap-3 px-4 py-3">
                        <p className="truncate text-sm font-semibold text-ink">{name}</p>
                        <span className="shrink-0 rounded-md bg-brand-50 px-2.5 py-1 text-xs font-semibold text-brand-hover">Periksa</span>
                    </div>
                ))}
            </Panel>
        </div>
    );
}

function HeadVisual() {
    return (
        <div className="mx-auto flex max-w-sm flex-col gap-3">
            <VisualHeader title="Perlu persetujuan" />
            <Panel>
                <p className="text-xs text-slate-600">Diajukan oleh petugas desa</p>
                <p className="mt-1 font-bold text-ink">Pelatihan pengemasan produk</p>
                <p className="mt-1 text-sm text-muted-foreground">Untuk UMKM dengan kebutuhan tinggi di bidang pemasaran.</p>
                <div className="mt-4 flex gap-2">
                    <span className="rounded-lg bg-brand px-3 py-1.5 text-xs font-semibold text-white">Setujui</span>
                    <span className="rounded-lg px-3 py-1.5 text-xs font-semibold text-slate-600 ring-1 ring-slate-200">Minta revisi</span>
                </div>
            </Panel>
        </div>
    );
}
