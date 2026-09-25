import { useState } from 'react';
import { AnimatePresence, motion } from 'motion/react';
import { ComputerIcon, CrownIcon, User02Icon, UserCheck01Icon } from '@hugeicons/core-free-icons';
import { Icon, Reveal, SectionTitle, Underline } from '@/components/landing/shared';
import { cn } from '@/lib/utils';

const actors = {
    umkm: { label: 'Pelaku UMKM', icon: User02Icon },
    petugas: { label: 'Petugas Desa', icon: UserCheck01Icon },
    sistem: { label: 'Sistem', icon: ComputerIcon },
    kepala: { label: 'Kepala Desa', icon: CrownIcon },
};

const steps = [
    {
        title: 'Mengisi profil usaha',
        actor: 'umkm',
        description: 'Pemilik usaha mengisi nama usaha, alamat, jenis usaha, produk yang dijual, dan dokumen legalitas yang sudah dimiliki.',
        result: 'Satu profil lengkap untuk setiap UMKM.',
    },
    {
        title: 'Memeriksa data',
        actor: 'petugas',
        description: 'Petugas desa mengecek data yang masuk. Kalau ada yang kurang atau keliru, data bisa diperbaiki sebelum dipakai.',
        result: 'Status data berubah menjadi terverifikasi.',
    },
    {
        title: 'Menceritakan kendala',
        actor: 'umkm',
        description: 'Pemilik usaha menjawab pertanyaan pilihan tentang modal, pemasaran, legalitas, produksi, dan penggunaan teknologi.',
        result: 'Gambaran kondisi usaha yang bisa dibandingkan.',
    },
    {
        title: 'Mengelompokkan UMKM',
        actor: 'sistem',
        description: 'Jawaban tadi diolah untuk melihat bidang mana yang paling dibutuhkan tiap usaha. Satu usaha boleh punya lebih dari satu kebutuhan.',
        result: 'Profil kebutuhan per usaha dan per kelompok.',
    },
    {
        title: 'Mencocokkan program',
        actor: 'sistem',
        description: 'Kebutuhan tiap usaha dicocokkan dengan program bantuan atau pembinaan yang sedang tersedia di desa.',
        result: 'Daftar program yang disarankan.',
    },
    {
        title: 'Menindaklanjuti',
        actor: 'petugas',
        description: 'Petugas menjadwalkan pembinaan, lalu mencatat apa yang sudah dilakukan dan bagaimana hasilnya.',
        result: 'Riwayat pembinaan yang tersimpan.',
    },
    {
        title: 'Memantau dan memutuskan',
        actor: 'kepala',
        description: 'Kepala desa melihat ringkasan kondisi UMKM dan perkembangan pembinaan, lalu menyetujui tindak lanjut bila diperlukan.',
        result: 'Keputusan desa yang berdasar data.',
    },
];

export default function HowItWorks() {
    const [activeIndex, setActiveIndex] = useState(0);
    const activeStep = steps[activeIndex];
    const activeActor = actors[activeStep.actor];

    return (
        <section id="cara-kerja" className="relative py-20 sm:py-28">
            <div className="mx-auto max-w-6xl px-5">
                <Reveal className="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between lg:gap-16">
                    <SectionTitle className="max-w-xl">
                        Tujuh langkah, dari formulir sampai <Underline>pembinaan</Underline>
                    </SectionTitle>
                    <p className="max-w-sm text-base leading-relaxed text-muted-foreground">
                        Setiap langkah dikerjakan oleh orang yang berbeda. Pilih satu langkah untuk melihat siapa yang mengerjakan dan apa hasilnya.
                    </p>
                </Reveal>

                <div className="mt-12 grid gap-8 lg:grid-cols-[1fr_1.1fr] lg:gap-12">
                    <Reveal as="ol" delay={0.1} className="flex flex-col">
                        {steps.map((step, index) => {
                            const actor = actors[step.actor];
                            const isActive = index === activeIndex;

                            return (
                                <li key={step.title} className="border-b border-slate-200 last:border-b-0">
                                    <button
                                        type="button"
                                        onClick={() => setActiveIndex(index)}
                                        aria-pressed={isActive}
                                        className={cn(
                                            'group flex w-full cursor-pointer items-center gap-4 rounded-xl px-3 py-4 text-left transition-colors outline-none focus-visible:ring-[3px] focus-visible:ring-ring/40 lg:py-3.5',
                                            isActive ? 'lg:bg-white lg:shadow-[0_1px_0_rgb(15_23_42/0.04),0_8px_24px_-12px_rgb(15_23_42/0.18)]' : 'hover:bg-white/60',
                                        )}
                                    >
                                        <span
                                            className={cn(
                                                'grid size-9 shrink-0 place-items-center rounded-lg text-sm font-bold tabular-nums transition-colors',
                                                isActive ? 'bg-brand text-white' : 'bg-brand-50 text-brand',
                                            )}
                                        >
                                            {index + 1}
                                        </span>
                                        <span className="min-w-0 flex-1">
                                            <span className="block font-bold text-ink">{step.title}</span>
                                            <span className="mt-0.5 flex items-center gap-1.5 text-sm text-muted-foreground">
                                                <Icon icon={actor.icon} size={14} />
                                                {actor.label}
                                            </span>
                                            <span className="mt-2 block text-sm leading-relaxed text-muted-foreground lg:hidden">{step.description}</span>
                                        </span>
                                    </button>
                                </li>
                            );
                        })}
                    </Reveal>

                    <div className="hidden lg:block">
                        <div className="sticky top-32 overflow-hidden rounded-3xl bg-brand-950 p-10 text-white">
                            <AnimatePresence mode="wait">
                                <motion.div
                                    key={activeIndex}
                                    initial={{ opacity: 0, x: 16 }}
                                    animate={{ opacity: 1, x: 0 }}
                                    exit={{ opacity: 0, x: -16 }}
                                    transition={{ duration: 0.3, ease: 'easeOut' }}
                                    className="flex min-h-[22rem] flex-col"
                                >
                                    <span className="text-sm font-semibold text-brand-300">
                                        Langkah {activeIndex + 1} dari {steps.length}
                                    </span>
                                    <h3 className="mt-3 text-3xl leading-tight font-extrabold tracking-tight">{activeStep.title}</h3>
                                    <span className="mt-5 inline-flex w-fit items-center gap-2 rounded-lg bg-white/10 px-3 py-1.5 text-sm font-semibold">
                                        <Icon icon={activeActor.icon} size={16} />
                                        Dikerjakan oleh {activeActor.label}
                                    </span>
                                    <p className="mt-6 text-lg leading-relaxed text-brand-100">{activeStep.description}</p>
                                    <div className="mt-auto border-t border-white/15 pt-5">
                                        <p className="text-sm text-brand-300">Hasilnya</p>
                                        <p className="mt-1 text-lg font-bold">{activeStep.result}</p>
                                    </div>
                                </motion.div>
                            </AnimatePresence>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}
