import { motion } from 'motion/react';
import {
    Analytics01Icon,
    Calendar01Icon,
    CheckmarkCircle02Icon,
    Clock01Icon,
    DatabaseIcon,
    DocumentValidationIcon,
    Idea01Icon,
    Shield01Icon,
    Tag01Icon,
    User02Icon,
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

const sessions = [
    {
        status: 'Terlaksana',
        tone: 'border-emerald-200 bg-emerald-50 text-emerald-700',
        icon: CheckmarkCircle02Icon,
        whenLabel: 'Pelaksanaan',
        when: '12 Nov 2026',
        program: 'Pemasaran',
        title: 'Pelatihan foto produk',
        note: 'Peserta sudah bisa memotret produk untuk katalog WhatsApp.',
        mentor: 'Petugas Desa',
    },
    {
        status: 'Dijadwalkan',
        tone: 'border-sky-200 bg-sky-50 text-sky-700',
        icon: Clock01Icon,
        whenLabel: 'Jadwal',
        when: '24 Nov 2026',
        program: 'Digitalisasi',
        title: 'Pendampingan berjualan di marketplace',
        note: 'Disetujui kepala desa, menunggu hari pelaksanaan di balai desa.',
        mentor: 'Petugas Desa',
    },
    {
        status: 'Menunggu Persetujuan',
        tone: 'border-amber-200 bg-amber-50 text-amber-800',
        icon: Clock01Icon,
        whenLabel: 'Rencana',
        when: '2 Des 2026',
        program: 'Modal',
        title: 'Pendampingan akses KUR mikro',
        note: 'Diajukan petugas, belum diputuskan kepala desa.',
        mentor: 'Petugas Desa',
    },
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
                        <div className="flex flex-col gap-3">
                            <div className="flex items-center justify-between gap-3 px-1">
                                <p className="font-bold text-ink">Riwayat pembinaan</p>
                                <SampleLabel />
                            </div>
                            {sessions.map((session, index) => (
                                <motion.article
                                    key={session.title}
                                    initial={{ opacity: 0, y: 8 }}
                                    whileInView={{ opacity: 1, y: 0 }}
                                    viewport={{ once: true }}
                                    transition={{ duration: 0.4, delay: 0.15 + index * 0.08 }}
                                    className="rounded-2xl border border-border bg-white p-4 shadow-xs sm:p-5"
                                >
                                    <div className="flex flex-wrap items-center gap-2">
                                        <span className={cn('inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-xs font-semibold', session.tone)}>
                                            <Icon icon={session.icon} size={14} />
                                            {session.status}
                                        </span>
                                        <span className="inline-flex items-center gap-1.5 text-xs text-slate-500">
                                            <Icon icon={Calendar01Icon} size={14} className="text-slate-400" />
                                            {session.whenLabel}: <strong className="font-semibold text-ink">{session.when}</strong>
                                        </span>
                                        <span className="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">
                                            <Icon icon={Tag01Icon} size={13} className="text-slate-400" />
                                            {session.program}
                                        </span>
                                    </div>
                                    <h3 className="mt-3 text-base font-bold text-ink">{session.title}</h3>
                                    <p className="mt-1 text-sm leading-relaxed text-slate-600">{session.note}</p>
                                    <p className="mt-3 inline-flex items-center gap-1 text-xs text-slate-500">
                                        <Icon icon={User02Icon} size={13} className="text-slate-400" />
                                        Pendamping: <span className="font-semibold text-slate-700">{session.mentor}</span>
                                    </p>
                                </motion.article>
                            ))}
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
