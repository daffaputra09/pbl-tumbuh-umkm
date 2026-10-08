import { useState } from 'react';
import { HugeiconsIcon } from '@hugeicons/react';
import {
    CheckmarkCircle02Icon,
    Factory01Icon,
    Idea01Icon,
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
import { apiFetch } from '@/lib/api';
import { cn } from '@/lib/utils';


const ICON_BY_SLUG = {
    modal: Money03Icon,
    pemasaran: Megaphone01Icon,
    digitalisasi: SmartPhone01Icon,
    legalitas: LegalDocument01Icon,
    produksi: Factory01Icon,
};

function iconForCategory(category) {
    return ICON_BY_SLUG[category.slug] ?? Idea01Icon;
}

function buildInitialState(categories) {
    return Object.fromEntries(
        categories.map((category) => [category.id, { mengalami: null, answers: {} }]),
    );
}


export default function UmkmNeedsForm({ categories = [] }) {
    const [state, setState] = useState(() => buildInitialState(categories));
    const [otherObstacle, setOtherObstacle] = useState('');
    const [categoryIndex, setCategoryIndex] = useState(0);
    const [phase, setPhase] = useState('gate'); // 'gate' | 'detail' | 'review'
    const [history, setHistory] = useState([]);
    const [validationError, setValidationError] = useState('');
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [serverError, setServerError] = useState('');

    if (categories.length === 0) {
        return (
            <div className="relative isolate overflow-hidden">
                <PageBackground />
                <div className="mx-auto max-w-2xl">
                    <Card>
                        <CardContent className="py-6 text-sm text-muted-foreground">
                            Daftar pertanyaan belum tersedia. Hubungi petugas desa, atau coba lagi nanti.
                        </CardContent>
                    </Card>
                </div>
            </div>
        );
    }

    const category = categories[categoryIndex];
    const isLastCategory = categoryIndex === categories.length - 1;

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

    function nextAfterCategory() {
        goTo(isLastCategory ? categoryIndex : categoryIndex + 1, isLastCategory ? 'review' : 'gate');
    }

    function handleGateAnswer(mengalami) {
        setState((prev) => ({ ...prev, [category.id]: { ...prev[category.id], mengalami } }));

        if (mengalami === 'tidak') {
            nextAfterCategory();
        } else {
            goTo(categoryIndex, 'detail');
        }
    }

    function selectOption(questionId, optionId) {
        setState((prev) => ({
            ...prev,
            [category.id]: {
                ...prev[category.id],
                answers: { ...prev[category.id].answers, [questionId]: optionId },
            },
        }));
        setValidationError('');
    }

    function handleDetailLanjut() {
        const belumLengkap = category.questions.some((question) => !state[category.id].answers[question.id]);

        if (belumLengkap) {
            setValidationError('Jawab semua pertanyaan di atas dulu sebelum lanjut.');
            return;
        }

        nextAfterCategory();
    }

    async function handleSubmit() {
        setServerError('');

        const answers = categories.flatMap((item) => {
            if (state[item.id].mengalami !== 'ya') return [];

            return item.questions.map((question) => ({
                questionId: question.id,
                optionId: state[item.id].answers[question.id],
            }));
        });

        if (answers.length === 0) {
            setServerError('Belum ada jawaban yang bisa dikirim. Pilih "Ya, ada" di minimal satu bidang dulu.');
            return;
        }

        setIsSubmitting(true);

        try {
            await apiFetch('/umkm/kebutuhan', {
                method: 'POST',
                body: JSON.stringify({
                    answers,
                    otherObstacle: otherObstacle.trim() || null,
                }),
            });

            window.location.href = '/umkm/dashboard';
        } catch (error) {
            setServerError(error.message);
            setIsSubmitting(false);
        }
    }

    return (
        <div className="relative isolate overflow-hidden">
            <PageBackground />

            <div className="mx-auto max-w-2xl">
                <header className="mb-6">
                    <p className="text-sm font-semibold text-primary">Kebutuhan &amp; Kendala Usaha</p>
                    <h1 className="mt-1 text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">
                        {phase === 'review' ? 'Ringkasan Jawabanmu' : 'Ceritakan Kesulitan Usahamu'}
                    </h1>
                    {phase !== 'review' && (
                        <p className="mt-2 text-sm leading-relaxed text-muted-foreground">
                            Bidang {categoryIndex + 1} dari {categories.length}. Jawab sejujurnya, tidak ada jawaban benar
                            atau salah.
                        </p>
                    )}
                </header>

                {phase !== 'review' && (
                    <div className="mb-6 flex gap-1.5">
                        {categories.map((item, index) => (
                            <span
                                key={item.id}
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
                        answers={state[category.id].answers}
                        onSelect={selectOption}
                        onLanjut={handleDetailLanjut}
                        onBack={goBack}
                        error={validationError}
                    />
                )}

                {phase === 'review' && (
                    <ReviewScreen
                        categories={categories}
                        state={state}
                        otherObstacle={otherObstacle}
                        onChangeOtherObstacle={setOtherObstacle}
                        onBack={goBack}
                        onSubmit={handleSubmit}
                        isSubmitting={isSubmitting}
                        error={serverError}
                    />
                )}
            </div>
        </div>
    );
}

function GateScreen({ category, onAnswer, onBack }) {
    return (
        <Card>
            <CardHeader className="items-center text-center">
                <span className="flex size-14 items-center justify-center rounded-2xl bg-brand-50 text-primary">
                    <HugeiconsIcon icon={iconForCategory(category)} size={28} strokeWidth={1.6} aria-hidden="true" />
                </span>
                <CardTitle className="mt-2 text-xl">{category.name}</CardTitle>
                {category.description && <p className="text-sm text-muted-foreground">{category.description}</p>}
            </CardHeader>
            <CardContent className="flex flex-col items-center gap-4">
                <p className="text-center text-base font-semibold text-ink">
                    Apakah kamu mengalami kendala di bidang {category.name.toLowerCase()}?
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

function DetailScreen({ category, answers, onSelect, onLanjut, onBack, error }) {
    return (
        <Card>
            <CardHeader>
                <CardTitle className="text-lg">{category.name}</CardTitle>
                <p className="text-sm text-muted-foreground">Jawab tiap pertanyaan di bawah ini sesuai kondisi usahamu.</p>
            </CardHeader>
            <CardContent className="flex flex-col gap-8 sm:gap-10">
                {category.questions.map((question, index) => (
                    <div key={question.id} className="flex flex-col gap-4">
                        <div>
                            <p className="text-sm leading-relaxed font-medium text-ink">
                                {index + 1}. {question.prompt}
                            </p>
                            {question.helpText && (
                                <p className="mt-1 text-xs leading-relaxed text-muted-foreground">{question.helpText}</p>
                            )}
                        </div>

                        {question.type === 'likert' ? (
                            <LikertScale
                                options={question.options}
                                selectedOptionId={answers[question.id]}
                                onSelect={(optionId) => onSelect(question.id, optionId)}
                            />
                        ) : (
                            <ChoiceList
                                options={question.options}
                                selectedOptionId={answers[question.id]}
                                onSelect={(optionId) => onSelect(question.id, optionId)}
                            />
                        )}
                    </div>
                ))}

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

// Tipe "likert": angka di lingkaran, label ujung kiri dari opsi pertama dan
// ujung kanan dari opsi terakhir (urutan sudah dari sort_order di server).
function LikertScale({ options, selectedOptionId, onSelect }) {
    const first = options[0];
    const last = options[options.length - 1];

    return (
        <div className="flex items-center gap-3 sm:gap-4">
            <span className="w-16 shrink-0 text-right text-[11px] leading-tight text-muted-foreground sm:w-24 sm:text-xs">
                {first?.label}
            </span>

            <div className="flex flex-1 items-center justify-center gap-3 sm:gap-6">
                {options.map((option, index) => {
                    const selected = selectedOptionId === option.id;

                    return (
                        <button
                            key={option.id}
                            type="button"
                            onClick={() => onSelect(option.id)}
                            className={cn(
                                'flex size-9 shrink-0 items-center justify-center rounded-full border-2 text-sm font-bold transition-colors sm:size-10',
                                selected
                                    ? 'border-primary bg-primary text-white'
                                    : 'border-border bg-white text-muted-foreground hover:border-primary/50',
                            )}
                        >
                            {option.value ?? index + 1}
                        </button>
                    );
                })}
            </div>

            <span className="w-16 shrink-0 text-[11px] leading-tight text-muted-foreground sm:w-24 sm:text-xs">
                {last?.label}
            </span>
        </div>
    );
}

// Tipe pilihan kalimat: tiap opsi ditampilkan sebagai kalimat utuh. Bobot
// (score) tiap opsi sengaja tidak ditampilkan, cuma dipakai di server.
function ChoiceList({ options, selectedOptionId, onSelect }) {
    return (
        <div className="flex flex-col gap-2">
            {options.map((option) => {
                const selected = selectedOptionId === option.id;

                return (
                    <button
                        key={option.id}
                        type="button"
                        onClick={() => onSelect(option.id)}
                        className={cn(
                            'rounded-xl border px-4 py-3 text-left text-sm leading-relaxed transition-colors',
                            selected
                                ? 'border-primary bg-brand-50 font-medium text-ink'
                                : 'border-border bg-white text-ink hover:border-brand-200 hover:bg-brand-50',
                        )}
                    >
                        {option.label}
                    </button>
                );
            })}
        </div>
    );
}

function ReviewScreen({ categories, state, otherObstacle, onChangeOtherObstacle, onBack, onSubmit, isSubmitting, error }) {
    return (
        <div className="flex flex-col gap-4">
            {categories.map((category) => {
                const answered = state[category.id].mengalami === 'ya';

                return (
                    <Card key={category.id}>
                        <CardContent className="flex items-center gap-3">
                            <HugeiconsIcon
                                icon={CheckmarkCircle02Icon}
                                size={18}
                                className={answered ? 'text-primary' : 'text-success'}
                                aria-hidden="true"
                            />
                            <p className="text-sm text-ink">
                                <span className="font-semibold">{category.name}:</span>{' '}
                                {answered
                                    ? `${category.questions.length} pertanyaan terjawab.`
                                    : 'Tidak ada kendala dilaporkan.'}
                            </p>
                        </CardContent>
                    </Card>
                );
            })}

            <Card>
                <CardContent className="flex flex-col">
                    <Label className="mb-1.5">Ada kendala lain di luar bidang di atas? (boleh dikosongkan)</Label>
                    <Textarea
                        rows={3}
                        value={otherObstacle}
                        onChange={(event) => onChangeOtherObstacle(event.target.value)}
                        placeholder="Ceritakan kendala lain yang kamu alami kalau ada"
                    />
                </CardContent>
            </Card>

            {error && <p className="text-sm font-medium text-destructive">{error}</p>}

            <div className="flex justify-between pt-2">
                <Button type="button" variant="outline" onClick={onBack} disabled={isSubmitting}>
                    Kembali
                </Button>
                <Button type="button" onClick={onSubmit} disabled={isSubmitting}>
                    {isSubmitting ? 'Mengirim...' : 'Kirim Jawaban'}
                </Button>
            </div>
        </div>
    );
}
