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
import FormSection from '@/components/umkm/FormSection';
import PageBackground from '@/components/umkm/PageBackground';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { cn } from '@/lib/utils';

const CATEGORIES = [
    {
        key: 'modal',
        title: 'Modal',
        description: 'Soal keuangan dan permodalan usaha.',
        icon: Money03Icon,
        options: [
            'Butuh tambahan modal usaha',
            'Kesulitan mengatur pembukuan atau catatan keuangan',
            'Sulit mengajukan pinjaman ke bank atau koperasi',
        ],
    },
    {
        key: 'pemasaran',
        title: 'Pemasaran',
        description: 'Soal menjual dan memperkenalkan produk ke lebih banyak orang.',
        icon: Megaphone01Icon,
        options: [
            'Jangkauan pembeli masih terbatas di sekitar rumah',
            'Belum paham cara promosi lewat internet',
            'Bingung menentukan harga jual yang pas',
        ],
    },
    {
        key: 'legalitas',
        title: 'Legalitas',
        description: 'Soal izin dan surat-surat resmi usaha.',
        icon: LegalDocument01Icon,
        options: [
            'Belum punya izin usaha (NIB)',
            'Bingung cara mengurus sertifikasi halal',
            'Belum punya NPWP untuk usaha',
        ],
    },
    {
        key: 'produksi',
        title: 'Produksi',
        description: 'Soal proses membuat dan mengemas produk.',
        icon: Factory01Icon,
        options: [
            'Peralatan usaha masih terbatas',
            'Bahan baku mahal atau susah dicari',
            'Kemasan produk masih kurang menarik',
        ],
    },
    {
        key: 'digitalisasi',
        title: 'Digitalisasi',
        description: 'Soal pemakaian teknologi untuk membantu usaha.',
        icon: SmartPhone01Icon,
        options: [
            'Belum menerima pembayaran lewat QRIS',
            'Belum punya akun media sosial untuk usaha',
            'Belum pernah coba jualan lewat marketplace online',
        ],
    },
];

function buildInitialState() {
    return Object.fromEntries(CATEGORIES.map((category) => [category.key, { pilihan: [], catatan: '' }]));
}

export default function UmkmNeedsForm() {
    const [formData, setFormData] = useState(buildInitialState);
    const [errorMessage, setErrorMessage] = useState('');
    const [submitStatus, setSubmitStatus] = useState('idle');

    function toggleOption(categoryKey, option) {
        setFormData((prev) => {
            const current = prev[categoryKey].pilihan;
            const next = current.includes(option) ? current.filter((item) => item !== option) : [...current, option];
            return { ...prev, [categoryKey]: { ...prev[categoryKey], pilihan: next } };
        });
        setErrorMessage('');
    }

    function updateCatatan(categoryKey, value) {
        setFormData((prev) => ({ ...prev, [categoryKey]: { ...prev[categoryKey], catatan: value } }));
    }

    function handleSubmit(event) {
        event.preventDefault();
        setSubmitStatus('idle');

        const totalDipilih = CATEGORIES.reduce((total, category) => total + formData[category.key].pilihan.length, 0);

        if (totalDipilih === 0) {
            setErrorMessage('Pilih minimal satu kendala di salah satu bidang di atas, supaya kami tahu apa yang perlu dibantu.');
            return;
        }

        // TODO: ganti bagian ini dengan pemanggilan endpoint API sesungguhnya, contoh:
        // const response = await fetch('/api/umkm/kebutuhan', {
        //     method: 'POST',
        //     headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        //     body: JSON.stringify(formData),
        // });
        console.log('Data kebutuhan & kendala UMKM siap dikirim ke server:', formData);
        setErrorMessage('');
        setSubmitStatus('success');
    }

    return (
        <div className="relative isolate min-h-screen overflow-hidden bg-background py-10 sm:py-14">
            <PageBackground />

            <div className="mx-auto max-w-3xl px-5">
                <header className="mb-8">
                    <p className="text-sm font-semibold text-primary">Kebutuhan &amp; Kendala Usaha</p>
                    <h1 className="mt-1 text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">
                        Ceritakan Kesulitan Usahamu
                    </h1>
                    <p className="mt-2 max-w-xl text-sm leading-relaxed text-muted-foreground">
                        Tidak perlu semua dicentang. Pilih saja bagian yang benar-benar kamu rasakan sebagai kendala,
                        supaya program bantuan yang diberikan nanti bisa lebih tepat sasaran.
                    </p>
                </header>

                {submitStatus === 'success' && (
                    <div className="mb-6 flex items-start gap-3 rounded-xl border border-success/30 bg-success/10 px-4 py-3 text-sm text-success">
                        <HugeiconsIcon icon={CheckmarkCircle02Icon} size={18} className="mt-0.5 shrink-0" aria-hidden="true" />
                        <p>Terima kasih, data kendala usahamu berhasil disimpan dan akan segera dianalisis.</p>
                    </div>
                )}

                {errorMessage && (
                    <div className="mb-6 rounded-xl border border-destructive/30 bg-destructive/10 px-4 py-3 text-sm font-medium text-destructive">
                        {errorMessage}
                    </div>
                )}

                <form onSubmit={handleSubmit} noValidate className="flex flex-col gap-6">
                    {CATEGORIES.map((category) => (
                        <FormSection
                            key={category.key}
                            icon={category.icon}
                            title={category.title}
                            description={category.description}
                        >
                            <div className="flex flex-col gap-3 sm:col-span-2">
                                {category.options.map((option) => {
                                    const checked = formData[category.key].pilihan.includes(option);

                                    return (
                                        <label
                                            key={option}
                                            className={cn(
                                                'flex cursor-pointer items-start gap-3 rounded-xl border bg-white p-4 transition-colors',
                                                checked ? 'border-primary/40 bg-brand-50' : 'border-border hover:border-brand-200',
                                            )}
                                        >
                                            <Checkbox
                                                checked={checked}
                                                onChange={() => toggleOption(category.key, option)}
                                            />
                                            <span className="text-sm font-medium text-ink">{option}</span>
                                        </label>
                                    );
                                })}

                                <div className="mt-1 flex flex-col">
                                    <Label className="mb-1.5">Catatan tambahan (boleh dikosongkan)</Label>
                                    <Textarea
                                        rows={2}
                                        value={formData[category.key].catatan}
                                        onChange={(event) => updateCatatan(category.key, event.target.value)}
                                        placeholder={`Ceritakan detail kendala lain seputar ${category.title.toLowerCase()} yang kamu alami`}
                                    />
                                </div>
                            </div>
                        </FormSection>
                    ))}

                    <div className="flex justify-end">
                        <Button type="submit" size="lg">
                            Simpan &amp; Analisis Kebutuhan
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    );
}