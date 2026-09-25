import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '@/components/ui/accordion';
import { Reveal, SectionTitle } from '@/components/landing/shared';

const faqs = [
    {
        question: 'Saya tidak terbiasa memakai aplikasi. Apakah bisa mengisi sendiri?',
        answer: 'Bisa. Sebagian besar pertanyaan berupa pilihan, jadi Anda cukup memilih jawaban yang paling sesuai. Kalau tetap kesulitan, petugas desa bisa membantu mengisikan data usaha Anda.',
    },
    {
        question: 'Bagaimana sistem tahu kebutuhan usaha saya?',
        answer: 'Dari jawaban Anda di bagian kebutuhan dan kendala. Jawaban itu diolah bersama data usaha lain di desa, lalu dibandingkan untuk melihat bidang mana yang paling perlu dibantu.',
    },
    {
        question: 'Kalau usaha saya direkomendasikan ke suatu program, apakah pasti dapat bantuan?',
        answer: 'Belum tentu. Rekomendasi hanya menunjukkan program yang paling sesuai. Keputusan akhir tetap ada di petugas dan kepala desa, dengan mempertimbangkan kuota dan syarat program.',
    },
    {
        question: 'Siapa saja yang bisa melihat data usaha saya?',
        answer: 'Anda sendiri, petugas desa yang mengelola data, dan kepala desa dalam bentuk ringkasan. Pemilik usaha lain tidak bisa melihat data Anda.',
    },
    {
        question: 'Bagaimana kalau data usaha saya berubah, misalnya sudah punya NIB?',
        answer: 'Perbarui saja di halaman usaha Anda. Petugas akan memeriksa perubahan itu, dan rekomendasi program ikut menyesuaikan.',
    },
    {
        question: 'Apakah laporan untuk rapat desa bisa diunduh?',
        answer: 'Bisa. Petugas dan kepala desa dapat mengunduh ringkasan kondisi UMKM, sebaran kendala, dan riwayat pembinaan.',
    },
];

export default function Faq() {
    return (
        <section id="faq" className="relative py-20 sm:py-28">
            <div className="mx-auto max-w-3xl px-5">
                <Reveal>
                    <SectionTitle>Pertanyaan yang sering diajukan</SectionTitle>
                </Reveal>

                <Reveal delay={0.1}>
                    <Accordion defaultValue={[0]} className="mt-8 border-t border-slate-200">
                        {faqs.map((faq, index) => (
                            <AccordionItem key={faq.question} value={index} className="border-slate-200 last:border-b">
                                <AccordionTrigger>{faq.question}</AccordionTrigger>
                                <AccordionContent>{faq.answer}</AccordionContent>
                            </AccordionItem>
                        ))}
                    </Accordion>
                </Reveal>
            </div>
        </section>
    );
}
