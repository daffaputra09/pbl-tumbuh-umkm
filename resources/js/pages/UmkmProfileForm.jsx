import { useState } from 'react';
import { HugeiconsIcon } from '@hugeicons/react';
import { CheckmarkCircle02Icon, Factory01Icon, LegalDocument01Icon, Store04Icon } from '@hugeicons/core-free-icons';
import FormSection from '@/components/umkm/FormSection';
import PageBackground from '@/components/umkm/PageBackground';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { cn } from '@/lib/utils';

const CATEGORY_OPTIONS = [
    { value: 'makanan-minuman', label: 'Makanan & Minuman' },
    { value: 'kerajinan', label: 'Kerajinan Tangan' },
    { value: 'fashion', label: 'Fashion & Tekstil' },
    { value: 'pertanian', label: 'Pertanian & Perkebunan' },
    { value: 'jasa', label: 'Jasa' },
    { value: 'lainnya', label: 'Lainnya' },
];

const HALAL_OPTIONS = [
    { value: 'sudah', label: 'Sudah Ada' },
    { value: 'proses', label: 'Dalam Proses' },
    { value: 'belum', label: 'Belum Ada' },
];

const DESKRIPSI_MAX_LENGTH = 300;

const initialFormState = {
    namaUsaha: '',
    namaPemilik: '',
    telepon: '',
    alamat: '',
    kategoriProduk: '',
    kisaranHarga: '',
    deskripsiProduk: '',
    punyaNib: false,
    nomorNib: '',
    punyaNpwp: false,
    nomorNpwp: '',
    statusHalal: '',
};

export default function UmkmProfileForm() {
    const [formData, setFormData] = useState(initialFormState);
    const [errors, setErrors] = useState({});
    const [submitStatus, setSubmitStatus] = useState('idle');

    function updateField(field, value) {
        setFormData((prev) => ({ ...prev, [field]: value }));
        setErrors((prev) => (prev[field] ? { ...prev, [field]: undefined } : prev));
    }

    function validate() {
        const nextErrors = {};

        if (!formData.namaUsaha.trim()) {
            nextErrors.namaUsaha = 'Nama usaha wajib diisi.';
        }

        if (!formData.namaPemilik.trim()) {
            nextErrors.namaPemilik = 'Nama pemilik wajib diisi.';
        }

        const phone = formData.telepon.trim();
        if (!phone) {
            nextErrors.telepon = 'Nomor telepon/WhatsApp wajib diisi.';
        } else if (!/^(\+62|0)8[0-9]{8,11}$/.test(phone)) {
            nextErrors.telepon = 'Format nomor tidak valid, gunakan awalan 08 atau +62.';
        }

        if (!formData.alamat.trim()) {
            nextErrors.alamat = 'Alamat operasional wajib diisi.';
        }

        if (!formData.kategoriProduk) {
            nextErrors.kategoriProduk = 'Pilih salah satu kategori produk.';
        }

        if (!formData.kisaranHarga.trim()) {
            nextErrors.kisaranHarga = 'Kisaran harga wajib diisi.';
        }

        if (!formData.deskripsiProduk.trim()) {
            nextErrors.deskripsiProduk = 'Deskripsi produk wajib diisi.';
        } else if (formData.deskripsiProduk.trim().length < 20) {
            nextErrors.deskripsiProduk = 'Deskripsi minimal 20 karakter agar cukup jelas.';
        }

        if (formData.punyaNib && !formData.nomorNib.trim()) {
            nextErrors.nomorNib = 'Isi nomor NIB, atau batalkan centang jika belum punya.';
        }

        if (formData.punyaNpwp && !formData.nomorNpwp.trim()) {
            nextErrors.nomorNpwp = 'Isi nomor NPWP, atau batalkan centang jika belum punya.';
        }

        if (!formData.statusHalal) {
            nextErrors.statusHalal = 'Pilih status sertifikasi halal.';
        }

        setErrors(nextErrors);
        return Object.keys(nextErrors).length === 0;
    }

    function handleSubmit(event) {
        event.preventDefault();
        setSubmitStatus('idle');

        if (!validate()) {
            return;
        }

        // TODO: ganti bagian ini dengan pemanggilan endpoint API sesungguhnya, contoh:
        // const response = await fetch('/api/umkm/profil', {
        //     method: 'POST',
        //     headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        //     body: JSON.stringify(formData),
        // });
        console.log('Data profil UMKM siap dikirim ke server:', formData);
        setSubmitStatus('success');
    }

    function handleReset() {
        setFormData(initialFormState);
        setErrors({});
        setSubmitStatus('idle');
    }

    return (
        <div className="relative isolate overflow-hidden">
            <PageBackground />

            <div className="mx-auto max-w-3xl">
                <header className="mb-8">
                    <p className="text-sm font-semibold text-primary">Pendaftaran &amp; Pembaruan Data</p>
                    <h1 className="mt-1 text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Profil UMKM</h1>
                    <p className="mt-2 max-w-xl text-sm leading-relaxed text-muted-foreground">
                        Lengkapi data usaha di bawah ini. Data yang Anda isi akan dipakai petugas desa untuk memahami
                        kebutuhan usaha Anda dan menentukan program pembinaan yang sesuai.
                    </p>
                </header>

                {submitStatus === 'success' && (
                    <div className="mb-6 flex items-start gap-3 rounded-xl border border-success/30 bg-success/10 px-4 py-3 text-sm text-success">
                        <HugeiconsIcon icon={CheckmarkCircle02Icon} size={18} className="mt-0.5 shrink-0" aria-hidden="true" />
                        <p>Data profil usaha berhasil disimpan. Tim desa akan meninjau data Anda dalam waktu dekat.</p>
                    </div>
                )}

                <form onSubmit={handleSubmit} noValidate className="flex flex-col gap-6">
                    <FormSection
                        icon={Store04Icon}
                        title="Data Identitas Usaha"
                        description="Informasi dasar mengenai usaha dan pemiliknya."
                    >
                        <Field label="Nama Usaha" required error={errors.namaUsaha}>
                            <Input
                                value={formData.namaUsaha}
                                onChange={(event) => updateField('namaUsaha', event.target.value)}
                                placeholder="Contoh: Warung Ibu Sari"
                                aria-invalid={Boolean(errors.namaUsaha)}
                            />
                        </Field>

                        <Field label="Nama Pemilik" required error={errors.namaPemilik}>
                            <Input
                                value={formData.namaPemilik}
                                onChange={(event) => updateField('namaPemilik', event.target.value)}
                                placeholder="Nama lengkap pemilik usaha"
                                aria-invalid={Boolean(errors.namaPemilik)}
                            />
                        </Field>

                        <Field label="Nomor Telepon/WhatsApp" required error={errors.telepon}>
                            <Input
                                type="tel"
                                value={formData.telepon}
                                onChange={(event) => updateField('telepon', event.target.value)}
                                placeholder="08xxxxxxxxxx"
                                aria-invalid={Boolean(errors.telepon)}
                            />
                        </Field>

                        <Field label="Alamat Operasional" required error={errors.alamat} className="sm:col-span-2">
                            <Textarea
                                rows={2}
                                value={formData.alamat}
                                onChange={(event) => updateField('alamat', event.target.value)}
                                placeholder="Alamat lengkap tempat usaha beroperasi"
                                aria-invalid={Boolean(errors.alamat)}
                            />
                        </Field>
                    </FormSection>

                    <FormSection
                        icon={Factory01Icon}
                        title="Data Produk"
                        description="Ceritakan produk atau layanan yang Anda tawarkan."
                    >
                        <Field label="Kategori Produk" required error={errors.kategoriProduk}>
                            <Select
                                value={formData.kategoriProduk}
                                onChange={(event) => updateField('kategoriProduk', event.target.value)}
                                aria-invalid={Boolean(errors.kategoriProduk)}
                            >
                                <option value="" disabled>
                                    Pilih kategori
                                </option>
                                {CATEGORY_OPTIONS.map((option) => (
                                    <option key={option.value} value={option.value}>
                                        {option.label}
                                    </option>
                                ))}
                            </Select>
                        </Field>

                        <Field label="Kisaran Harga" required error={errors.kisaranHarga}>
                            <Input
                                value={formData.kisaranHarga}
                                onChange={(event) => updateField('kisaranHarga', event.target.value)}
                                placeholder="Contoh: Rp 15.000 - Rp 50.000"
                                aria-invalid={Boolean(errors.kisaranHarga)}
                            />
                        </Field>

                        <Field
                            label="Deskripsi Singkat Produk"
                            required
                            error={errors.deskripsiProduk}
                            hint={`${formData.deskripsiProduk.trim().length}/${DESKRIPSI_MAX_LENGTH} karakter`}
                            className="sm:col-span-2"
                        >
                            <Textarea
                                rows={3}
                                value={formData.deskripsiProduk}
                                onChange={(event) => updateField('deskripsiProduk', event.target.value.slice(0, DESKRIPSI_MAX_LENGTH))}
                                placeholder="Jelaskan produk unggulan, bahan baku, atau keunikan usaha Anda"
                                aria-invalid={Boolean(errors.deskripsiProduk)}
                            />
                        </Field>
                    </FormSection>

                    <FormSection
                        icon={LegalDocument01Icon}
                        title="Kelengkapan Legalitas"
                        description="Status legalitas membantu desa mengarahkan pendampingan perizinan yang tepat."
                    >
                        <div className="flex flex-col gap-3 sm:col-span-2">
                            <LegalityCheck
                                label="Usaha sudah memiliki NIB (Nomor Induk Berusaha)"
                                checked={formData.punyaNib}
                                onCheckedChange={(value) => updateField('punyaNib', value)}
                            >
                                {formData.punyaNib && (
                                    <Field label="Nomor NIB" error={errors.nomorNib} className="mt-3">
                                        <Input
                                            value={formData.nomorNib}
                                            onChange={(event) => updateField('nomorNib', event.target.value)}
                                            placeholder="Masukkan nomor NIB"
                                            aria-invalid={Boolean(errors.nomorNib)}
                                        />
                                    </Field>
                                )}
                            </LegalityCheck>

                            <LegalityCheck
                                label="Usaha sudah memiliki NPWP"
                                checked={formData.punyaNpwp}
                                onCheckedChange={(value) => updateField('punyaNpwp', value)}
                            >
                                {formData.punyaNpwp && (
                                    <Field label="Nomor NPWP" error={errors.nomorNpwp} className="mt-3">
                                        <Input
                                            value={formData.nomorNpwp}
                                            onChange={(event) => updateField('nomorNpwp', event.target.value)}
                                            placeholder="Masukkan nomor NPWP"
                                            aria-invalid={Boolean(errors.nomorNpwp)}
                                        />
                                    </Field>
                                )}
                            </LegalityCheck>
                        </div>

                        <Field label="Status Sertifikasi Halal" required error={errors.statusHalal} className="sm:col-span-2">
                            <div className="flex flex-wrap gap-2">
                                {HALAL_OPTIONS.map((option) => (
                                    <button
                                        key={option.value}
                                        type="button"
                                        onClick={() => updateField('statusHalal', option.value)}
                                        className={cn(
                                            'rounded-full border px-4 py-2 text-sm font-medium transition-colors',
                                            formData.statusHalal === option.value
                                                ? 'border-primary bg-primary text-white'
                                                : 'border-border bg-white text-ink hover:border-brand-200 hover:bg-brand-50',
                                        )}
                                    >
                                        {option.label}
                                    </button>
                                ))}
                            </div>
                        </Field>
                    </FormSection>

                    <div className="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                        <Button type="button" variant="outline" onClick={handleReset}>
                            Reset Form
                        </Button>
                        <Button type="submit">Simpan Profil Usaha</Button>
                    </div>
                </form>
            </div>
        </div>
    );
}

function Field({ label, required = false, error, hint, className, children }) {
    return (
        <div className={cn('flex flex-col', className)}>
            <div className="mb-1.5 flex items-baseline justify-between gap-2">
                <Label>
                    {label}
                    {required && <span className="ml-0.5 text-destructive">*</span>}
                </Label>
                {hint && <span className="text-xs text-muted-foreground">{hint}</span>}
            </div>
            {children}
            {error && <p className="mt-1.5 text-xs font-medium text-destructive">{error}</p>}
        </div>
    );
}

function LegalityCheck({ label, checked, onCheckedChange, children }) {
    return (
        <div className="rounded-xl border border-border bg-white p-4">
            <label className="flex cursor-pointer items-start gap-3">
                <Checkbox checked={checked} onChange={(event) => onCheckedChange(event.target.checked)} />
                <span className="text-sm font-medium text-ink">{label}</span>
            </label>
            {children}
        </div>
    );
}