import { useState } from 'react';
import { HugeiconsIcon } from '@hugeicons/react';
import {
    CheckmarkCircle02Icon,
    Factory01Icon,
    LegalDocument01Icon,
    Megaphone01Icon,
    Money03Icon,
    SmartPhone01Icon,
} from '@hugeicons/core-free-icons';
import PageBackground from '@/components/umkm/PageBackground';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { cn } from '@/lib/utils';

// Urutan dan isi pernyataan ini mengikuti hasil analisis dari Pak Subhi.
const CATEGORIES = [
    {
        key: 'modal',
        title: 'Modal',
        description: 'Soal keuangan dan permodalan usaha.',
        icon: Money03Icon,
        indicators: [
            'Usaha saya membutuhkan tambahan modal untuk operasional atau pengembangan.',
            'Saya mengalami kesulitan dalam melakukan pencatatan / pembukuan keuangan usaha.',
            'Saya kesulitan dalam mengajukan pinjaman ke bank, koperasi, atau lembaga keuangan.',
            'Arus kas (cash flow) usaha sering terganggu akibat keterlambatan pembayaran pelanggan atau penjualan yang tidak stabil.',
        ],
    },
    {
        key: 'pemasaran',
        title: 'Pemasaran',
        description: 'Soal menjual dan memperkenalkan produk ke lebih banyak orang.',
        icon: Megaphone01Icon,
        indicators: [
            'Wilayah/jangkauan penjualan produk saya saat ini masih sangat terbatas.',
            'Desain logo, label, atau kemasan produk saya belum menarik / belum standar komersial.',
            'Saya kesulitan dalam menentukan strategi harga dan promosi yang efektif.',
            'Produk saya sulit bersaing dengan produk sejenis dari pesaing lain.',
        ],
    },
    {
        key: 'digitalisasi',
        title: 'Digitalisasi',
        description: 'Soal pemanfaatan teknologi dan media digital.',
        icon: SmartPhone01Icon,
        indicators: [
            'Saya belum atau jarang memanfaatkan media sosial (Instagram, TikTok, Facebook, dll.) untuk promosi.',
            'Saya mengalami kesulitan dalam berjualan melalui e-commerce / marketplace (Shopee, Tokopedia, GoFood, dll.).',
            'Saya belum memanfaatkan pembayaran digital (QRIS, transfer bank) dalam transaksi harian.',
            'Saya kesulitan membuat konten promosi foto/video produk yang menarik secara mandiri.',
        ],
    },
    {
        key: 'legalitas',
        title: 'Legalitas',
        description: 'Soal perizinan dan kelengkapan dokumen resmi.',
        icon: LegalDocument01Icon,
        indicators: [
            'Usaha saya belum memiliki Nomor Induk Berusaha (NIB).',
            'Produk saya belum memiliki sertifikasi perizinan yang dibutuhkan (Sertifikat Halal, P-IRT, BPOM, dll.).',
            'Saya merasa proses atau persyaratan pengurusan izin usaha membingungkan / rumit.',
            'Kurangnya informasi dan pendampingan mengenai regulasi legalitas bagi UMKM.',
        ],
    },
    {
        key: 'produksi',
        title: 'Produksi',
        description: 'Soal proses pembuatan, bahan baku, dan kapasitas produksi.',
        icon: Factory01Icon,
        indicators: [
            'Peralatan dan teknologi produksi yang saya gunakan saat ini masih sangat terbatas / manual.',
            'Saya sering mengalami kesulitan dalam mendapatkan bahan baku dengan harga terjangkau dan stabil.',
            'Kapasitas produksi usaha saya belum mampu memenuhi pesanan dalam jumlah besar.',
            'Saya mengalami kendala dalam menjaga konsistensi kualitas dan ketahanan produk.',
        ],
    },
];

const LIKERT_OPTIONS = [
    { value: 1, label: 'Sangat Tidak Setuju', hint: 'Tidak mengalami kendala ini' },
    { value: 2, label: 'Tidak Setuju', hint: 'Kendala ringan' },
    { value: 3, label: 'Setuju', hint: 'Cukup mengalami kendala' },
    { value: 4, label: 'Sangat Setuju', hint: 'Kendala sangat berat' },
];

function buildInitialAnswers() {
    return Object.fromEntries(
        CATEGORIES.map((category) => [category.key, { mengalami: null, skors: [null, null, null, null], catatan: '' }]),
    );
}

// Sesuai rumus dari Pak Subhi: skor total 4 pernyataan (tiap 1-4) per bidang.
function getUrgencyLevel(total) {
    if (total <= 7) return { label: 'Rendah', badgeClass: 'bg-success/10 text-success' };
    if (total <= 11) return { label: 'Sedang', badgeClass: 'bg-sun-50 text-sun-700' };
    return { label: 'Tinggi', badgeClass: 'bg-destructive/10 text-destructive' };
}

export default function UmkmNeedsForm() {
    const [answers, setAnswers] = useState(buildInitialAnswers);
    const [categoryIndex, setCategoryIndex] = useState(0);
    const [phase, setPhase] = useState('gate'); // 'gate' | 'detail' | 'review'
    const [history, setHistory] = useState([]);
    const [validationError, setValidationError] = useState('');

    const category = CATEGORIES[categoryIndex];
    const isLastCategory = categoryIndex === CATEGORIES.length - 1;

    function goTo(nextIndex, nextPhase) {
        setHistory((prev) => [...prev, { categoryIndex, phase }]);
        setCategoryIndex(nextIndex);
        setPhase(nextPhase);
        setValidationError('');
    }

    function goBack() {
        setHistory((prev) => {
            if (prev.length === 0) return prev;
            const last = prev[prev.length - 1];
            setCategoryIndex(last.categoryIndex);
            setPhase(last.phase);
            return prev.slice(0, -1);
        });
        setValidationError('');
    }

    function handleGateAnswer(mengalami) {
        setAnswers((prev) => ({ ...prev, [category.key]: { ...prev[category.key], mengalami } }));

        if (mengalami === 'tidak') {
            goTo(isLastCategory ? categoryIndex : categoryIndex + 1, isLastCategory ? 'review' : 'gate');
        } else {
            goTo(categoryIndex, 'detail');
        }
    }

    function updateSkor(indicatorIndex, value) {
        setAnswers((prev) => {
            const skors = [...prev[category.key].skors];
            skors[indicatorIndex] = value;
            return { ...prev, [category.key]: { ...prev[category.key], skors } };
        });
        setValidationError('');
    }

    function updateCatatan(value) {
        setAnswers((prev) => ({ ...prev, [category.key]: { ...prev[category.key], catatan: value } }));
    }

    function handleDetailLanjut() {
        const belumLengkap = answers[category.key].skors.some((skor) => skor === null);

        if (belumLengkap) {
            setValidationError('Jawab semua pernyataan di atas dulu sebelum lanjut.');
            return;
        }

        goTo(isLastCategory ? categoryIndex : categoryIndex + 1, isLastCategory ? 'review' : 'gate');
    }

    function handleSubmit() {
        // TODO: ganti bagian ini dengan pemanggilan endpoint API sesungguhnya begitu
        // tabel assessments/assessment_answers dan Controller-nya siap, contoh:
        // await fetch('/api/assessments', { method: 'POST', ... });
        console.log('Jawaban kebutuhan & kendala siap dikirim:', answers);

        window.location.href = '/umkm/dashboard';
    }

    return (
        <div className="relative isolate min-h-screen overflow-hidden bg-background py-10 sm:py-14">
            <PageBackground />

            <div className="mx-auto max-w-2xl px-5">
                <header className="mb-6">
                    <p className="text-sm font-semibold text-primary">Kebutuhan &amp; Kendala Usaha</p>
                    <h1 className="mt-1 text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">
                        {phase === 'review' ? 'Ringkasan Jawabanmu' : 'Ceritakan Kesulitan Usahamu'}
                    </h1>
                    {phase !== 'review' && (
                        <p className="mt-2 text-sm leading-relaxed text-muted-foreground">
                            Bidang {categoryIndex + 1} dari {CATEGORIES.length}. Jawab sejujurnya, tidak ada jawaban benar
                            atau salah.
                        </p>
                    )}
                </header>

                {phase !== 'review' && (
                    <div className="mb-6 flex gap-1.5">
                        {CATEGORIES.map((item, index) => (
                            <span
                                key={item.key}
                                className={cn(
                                    'h-1.5 flex-1 rounded-full',
                                    index < categoryIndex ? 'bg-primary' : index === categoryIndex ? 'bg-primary/50' : 'bg-muted',
                                )}
                            />
                        ))}
                    </div>
                )}

                {phase === 'gate' && (
                    <GateScreen category={category} onAnswer={handleGateAnswer} onBack={history.length > 0 ? goBack : null} />
                )}

                {phase === 'detail' && (
                    <DetailScreen
                        category={category}
                        answer={answers[category.key]}
                        onChangeSkor={updateSkor}
                        onChangeCatatan={updateCatatan}
                        onLanjut={handleDetailLanjut}
                        onBack={goBack}
                        error={validationError}
                    />
                )}

                {phase === 'review' && <ReviewScreen answers={answers} onBack={goBack} onSubmit={handleSubmit} />}
            </div>
        </div>
    );
}

function GateScreen({ category, onAnswer, onBack }) {
    return (
        <Card>
            <CardHeader className="items-center text-center">
                <span className="flex size-14 items-center justify-center rounded-2xl bg-brand-50 text-primary">
                    <HugeiconsIcon icon={category.icon} size={28} strokeWidth={1.6} aria-hidden="true" />
                </span>
                <CardTitle className="mt-2 text-xl">{category.title}</CardTitle>
                <p className="text-sm text-muted-foreground">{category.description}</p>
            </CardHeader>
            <CardContent className="flex flex-col items-center gap-4">
                <p className="text-center text-base font-semibold text-ink">
                    Apakah kamu mengalami kendala di bidang {category.title.toLowerCase()}?
                </p>
                <div className="flex gap-3">
                    <Button type="button" variant="outline" onClick={() => onAnswer('tidak')}>
                        Tidak
                    </Button>
                    <Button type="button" onClick={() => onAnswer('ya')}>
                        Ya, ada
                    </Button>
                </div>
                {onBack && (
                    <button
                        type="button"
                        onClick={onBack}
                        className="text-xs font-medium text-muted-foreground underline underline-offset-2"
                    >
                        Kembali ke bidang sebelumnya
                    </button>
                )}
            </CardContent>
        </Card>
    );
}

function DetailScreen({ category, answer, onChangeSkor, onChangeCatatan, onLanjut, onBack, error }) {
    return (
        <Card>
            <CardHeader>
                <CardTitle className="text-lg">{category.title}</CardTitle>
                <p className="text-sm text-muted-foreground">Pilih seberapa setuju kamu dengan tiap pernyataan di bawah ini.</p>
            </CardHeader>
            <CardContent className="flex flex-col gap-8 sm:gap-10">
                {category.indicators.map((statement, index) => (
                    <div key={statement} className="flex flex-col gap-4">
                        <p className="text-sm leading-relaxed font-medium text-ink">
                            {index + 1}. {statement}
                        </p>
                        <LikertScale value={answer.skors[index]} onChange={(value) => onChangeSkor(index, value)} />
                    </div>
                ))}

                <div className="flex flex-col">
                    <Label className="mb-1.5">Catatan tambahan (boleh dikosongkan)</Label>
                    <Textarea
                        rows={2}
                        value={answer.catatan}
                        onChange={(event) => onChangeCatatan(event.target.value)}
                        placeholder={`Ceritakan detail kendala ${category.title.toLowerCase()} lainnya kalau ada`}
                    />
                </div>

                {error && <p className="text-sm font-medium text-destructive">{error}</p>}

                <div className="flex justify-between">
                    <Button type="button" variant="outline" onClick={onBack}>
                        Kembali
                    </Button>
                    <Button type="button" onClick={onLanjut}>
                        Lanjut
                    </Button>
                </div>
            </CardContent>
        </Card>
    );
}

// Baris skala 1-4 dengan label di ujung kiri-kanan, gaya kuesioner Likert klasik.
function LikertScale({ value, onChange }) {
    return (
        <div className="flex items-center gap-3 sm:gap-4">
            <span className="w-16 shrink-0 text-right text-[11px] leading-tight text-muted-foreground sm:w-24 sm:text-xs">
                Sangat Tidak Setuju
            </span>

            <div className="flex flex-1 items-center justify-center gap-4 sm:gap-7">
                {LIKERT_OPTIONS.map((option) => {
                    const selected = value === option.value;
                    return (
                        <button
                            key={option.value}
                            type="button"
                            title={option.hint}
                            onClick={() => onChange(option.value)}
                            className={cn(
                                'flex size-9 shrink-0 items-center justify-center rounded-full border-2 text-sm font-bold transition-colors sm:size-10',
                                selected
                                    ? 'border-primary bg-primary text-white'
                                    : 'border-border bg-white text-muted-foreground hover:border-primary/50',
                            )}
                        >
                            {option.value}
                        </button>
                    );
                })}
            </div>

            <span className="w-16 shrink-0 text-[11px] leading-tight text-muted-foreground sm:w-24 sm:text-xs">
                Sangat Setuju
            </span>
        </div>
    );
}

function ReviewScreen({ answers, onBack, onSubmit }) {
    return (
        <div className="flex flex-col gap-4">
            {CATEGORIES.map((category) => {
                const answer = answers[category.key];

                if (answer.mengalami !== 'ya') {
                    return (
                        <Card key={category.key}>
                            <CardContent className="flex items-center gap-3">
                                <HugeiconsIcon icon={CheckmarkCircle02Icon} size={18} className="text-success" aria-hidden="true" />
                                <p className="text-sm text-ink">
                                    <span className="font-semibold">{category.title}:</span> Tidak ada kendala dilaporkan.
                                </p>
                            </CardContent>
                        </Card>
                    );
                }

                const total = answer.skors.reduce((sum, skor) => sum + (skor ?? 0), 0);
                const urgency = getUrgencyLevel(total);

                return (
                    <Card key={category.key}>
                        <CardContent className="flex flex-col gap-2">
                            <div className="flex items-center justify-between">
                                <p className="text-sm font-bold text-ink">{category.title}</p>
                                <span className={cn('rounded-full px-2.5 py-0.5 text-[11px] font-semibold', urgency.badgeClass)}>
                                    Kendala {urgency.label}
                                </span>
                            </div>
                            {answer.catatan && (
                                <p className="text-sm leading-relaxed text-muted-foreground">&ldquo;{answer.catatan}&rdquo;</p>
                            )}
                        </CardContent>
                    </Card>
                );
            })}

            <div className="flex justify-between pt-2">
                <Button type="button" variant="outline" onClick={onBack}>
                    Kembali
                </Button>
                <Button type="button" onClick={onSubmit}>
                    Kirim Jawaban
                </Button>
            </div>
        </div>
    );
}
