import { Reveal, SectionTitle, Underline } from '@/components/landing/shared';

const questions = [
    { question: 'Berapa UMKM di desa ini yang masih aktif?', answer: 'Ringkasan di dashboard' },
    { question: 'Usaha mana saja yang belum punya NIB?', answer: 'Daftar UMKM, disaring menurut legalitas' },
    { question: 'Berapa banyak yang kesulitan modal?', answer: 'Sebaran kendala per bidang' },
    { question: 'Masalah apa yang paling sering muncul?', answer: 'Hasil pengelompokan UMKM' },
    { question: 'Program apa yang cocok untuk usaha tertentu?', answer: 'Rekomendasi program di profil usaha' },
    { question: 'Pembinaan apa yang sudah pernah diberikan?', answer: 'Riwayat tindak lanjut' },
];

export default function Problem() {
    return (
        <section id="masalah" className="relative border-t bg-white py-20 sm:py-28">
            <div className="mx-auto grid max-w-6xl gap-10 px-5 lg:grid-cols-[5fr_7fr] lg:gap-20">
                <Reveal className="flex flex-col gap-5 lg:sticky lg:top-32 lg:self-start">
                    <SectionTitle>
                        Pertanyaan yang sering muncul di <Underline>rapat desa</Underline>
                    </SectionTitle>
                    <p className="max-w-md text-base leading-relaxed text-muted-foreground sm:text-lg">
                        Kalau data UMKM masih dicatat manual atau tersebar di beberapa tempat, pertanyaan seperti ini butuh waktu berhari-hari
                        untuk dijawab. Di Tumbuh UMKM, jawabannya sudah ada di layar.
                    </p>
                </Reveal>

                <Reveal delay={0.1}>
                    <ol className="border-t border-slate-200">
                        {questions.map((item, index) => (
                            <li
                                key={item.question}
                                className="grid grid-cols-[2.5rem_1fr] gap-x-4 gap-y-1 border-b border-slate-200 py-5 sm:grid-cols-[3rem_1fr_auto] sm:items-baseline sm:gap-x-6"
                            >
                                <span className="text-sm font-bold text-brand tabular-nums sm:row-span-1">{String(index + 1).padStart(2, '0')}</span>
                                <p className="text-lg leading-snug font-bold text-ink sm:text-xl">{item.question}</p>
                                <p className="col-start-2 text-sm text-muted-foreground sm:col-start-3 sm:max-w-[14rem] sm:text-right">{item.answer}</p>
                            </li>
                        ))}
                    </ol>
                </Reveal>
            </div>
        </section>
    );
}
