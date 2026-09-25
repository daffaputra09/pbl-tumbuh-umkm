import { motion } from 'motion/react';
import {
    Analytics01Icon,
    DatabaseIcon,
    DocumentValidationIcon,
    Idea01Icon,
    Shield01Icon,
    Tick02Icon,
} from '@hugeicons/core-free-icons';
import { Icon, Reveal, SampleLabel, SectionTitle, Underline } from '@/components/landing/shared';
import { cn } from '@/lib/utils';

const supportingFeatures = [
    {
        title: 'Data UMKM di satu tempat',
        description: 'Profil usaha, produk, dan legalitas tersimpan bersama. Petugas tidak perlu lagi membuka banyak berkas untuk satu usaha.',
        icon: DatabaseIcon,
    },
    {
        title: 'Verifikasi oleh petugas',
        description: 'Data yang diisi warga diperiksa dulu sebelum dipakai untuk analisis, jadi hasilnya bisa dipertanggungjawabkan.',
        icon: DocumentValidationIcon,
    },
    {
        title: 'Rekomendasi program',
        description: 'Setiap profil usaha menampilkan program bantuan atau pembinaan yang paling sesuai dengan kebutuhannya.',
        icon: Idea01Icon,
    },
    {
        title: 'Dashboard dan laporan',
        description: 'Kepala desa bisa melihat jumlah UMKM, sebaran kendala, dan perkembangan pembinaan, lalu mengunduh laporannya.',
        icon: Analytics01Icon,
    },
    {
        title: 'Akses sesuai peran',
        description: 'Pemilik usaha hanya bisa melihat dan mengubah data usahanya sendiri. Pengelolaan data desa ada di tangan petugas.',
        icon: Shield01Icon,
    },
];

const timeline = [
    { stage: 'Direkomendasikan', title: 'Pelatihan foto produk', note: 'Cocok dengan kebutuhan pemasaran', done: true },
    { stage: 'Disetujui kepala desa', title: 'Dijadwalkan di balai desa', note: 'Bersama UMKM lain di kelompok yang sama', done: true },
    { stage: 'Selesai', title: 'Pelatihan sudah diikuti', note: 'Petugas mencatat hasil dan catatan lanjutan', done: true },
    { stage: 'Berikutnya', title: 'Pendampingan berjualan di marketplace', note: 'Menunggu jadwal', done: false },
];

export default function Features() {
    return (
        <section id="fitur" className="relative py-20 sm:py-28">
            <div className="mx-auto max-w-6xl px-5">
                <div className="grid items-start gap-12 lg:grid-cols-2 lg:gap-16">
                    <Reveal className="flex flex-col gap-5 lg:sticky lg:top-32">
                        <SectionTitle>
                            Setiap pembinaan <Underline>tercatat</Underline>, siapa pun petugasnya
                        </SectionTitle>
                        <p className="text-base leading-relaxed text-muted-foreground sm:text-lg">
                            Rekomendasi program bisa langsung ditindaklanjuti. Petugas mencatat jadwal, persetujuan kepala desa, dan hasil
                            pembinaan di profil usaha.
                        </p>
                        <p className="text-base leading-relaxed text-muted-foreground sm:text-lg">
                            Saat petugas berganti atau program baru datang, desa tetap tahu usaha mana yang sudah dibina dan apa langkah berikutnya.
                        </p>
                    </Reveal>

                    <Reveal delay={0.1}>
                        <div className="rounded-3xl bg-white p-6 ring-1 ring-slate-200 sm:p-8">
                            <div className="mb-6 flex items-center justify-between gap-3">
                                <p className="font-bold text-ink">Riwayat pembinaan</p>
                                <SampleLabel />
                            </div>
                            <ol className="relative flex flex-col gap-6 pl-8">
                                <span aria-hidden className="absolute top-2 bottom-2 left-[0.6875rem] w-px bg-slate-200" />
                                {timeline.map((item, index) => (
                                    <motion.li
                                        key={item.title}
                                        initial={{ opacity: 0, y: 8 }}
                                        whileInView={{ opacity: 1, y: 0 }}
                                        viewport={{ once: true }}
                                        transition={{ duration: 0.4, delay: 0.2 + index * 0.12 }}
                                        className="relative"
                                    >
                                        <span
                                            className={cn(
                                                'absolute top-0.5 -left-8 grid size-[1.375rem] place-items-center rounded-full ring-4 ring-white',
                                                item.done ? 'bg-brand text-white' : 'border-2 border-dashed border-slate-300 bg-white',
                                            )}
                                        >
                                            {item.done && <Icon icon={Tick02Icon} size={12} strokeWidth={3} />}
                                        </span>
                                        <p className={cn('text-xs font-semibold', item.done ? 'text-brand' : 'text-sun-700')}>{item.stage}</p>
                                        <p className="mt-0.5 font-bold text-ink">{item.title}</p>
                                        <p className="text-sm text-muted-foreground">{item.note}</p>
                                    </motion.li>
                                ))}
                            </ol>
                        </div>
                    </Reveal>
                </div>

                <div className="mt-20 grid gap-x-12 gap-y-10 border-t border-slate-200 pt-12 sm:grid-cols-2 lg:grid-cols-3">
                    {supportingFeatures.map((feature, index) => (
                        <Reveal key={feature.title} delay={(index % 3) * 0.08} className="flex flex-col gap-3">
                            <Icon icon={feature.icon} size={24} className="text-brand" />
                            <h3 className="text-lg font-bold text-ink">{feature.title}</h3>
                            <p className="text-[15px] leading-relaxed text-muted-foreground">{feature.description}</p>
                        </Reveal>
                    ))}
                </div>
            </div>
        </section>
    );
}
