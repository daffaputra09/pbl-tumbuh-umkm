import { useState } from 'react';
import FormSection from '@/components/umkm/FormSection';
import PageBackground from '@/components/umkm/PageBackground';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { apiFetch } from '@/lib/api';
import { cn } from '@/lib/utils';
import { Store04Icon } from '@hugeicons/core-free-icons';

const CURRENT_YEAR = new Date().getFullYear();

function buildInitialState(business) {
    return {
        businessTypeId: business?.business_type_id ? String(business.business_type_id) : '',
        businessName: business?.business_name ?? '',
        ownerName: business?.owner_name ?? '',
        phone: business?.phone ?? '',
        email: business?.email ?? '',
        address: business?.address ?? '',
        hamlet: business?.hamlet ?? '',
        rt: business?.rt ?? '',
        rw: business?.rw ?? '',
        establishedYear: business?.established_year ? String(business.established_year) : '',
        employeeCount:
            business?.employee_count !== undefined && business?.employee_count !== null
                ? String(business.employee_count)
                : '',
        description: business?.description ?? '',
    };
}

/**
 * Form profil usaha. Dipakai untuk dua hal sekaligus lewat page-props `mode`:
 * - mode="self"    -> UC-04a, pelaku UMKM mengisi/mengedit profil usahanya sendiri.
 * - mode="officer" -> UC-04b, petugas mendata UMKM yang belum punya akun.
 * Form dan validasinya sama persis, bedanya cuma endpoint tujuan submit dan
 * apa yang terjadi setelah sukses (lihat handleSubmit).
 */
export default function UmkmProfileForm({ business = null, businessTypes = [], mode = 'self' }) {
    const [formData, setFormData] = useState(() => buildInitialState(business));
    const [errors, setErrors] = useState({});
    const [submitStatus, setSubmitStatus] = useState('idle');
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [serverError, setServerError] = useState('');

    const isOfficerMode = mode === 'officer';
    const submitUrl = isOfficerMode ? '/petugas/umkm/pendataan-umkm' : '/umkm/profil';

    function updateField(field, value) {
        setFormData((prev) => ({ ...prev, [field]: value }));
        setErrors((prev) => (prev[field] ? { ...prev, [field]: undefined } : prev));
    }

    function validate() {
        const nextErrors = {};

        if (!formData.businessTypeId) {
            nextErrors.businessTypeId = 'Pilih jenis usaha.';
        }

        if (!formData.businessName.trim()) {
            nextErrors.businessName = 'Nama usaha wajib diisi.';
        }

        if (!formData.ownerName.trim()) {
            nextErrors.ownerName = 'Nama pemilik wajib diisi.';
        }

        const phone = formData.phone.trim();
        if (!phone) {
            nextErrors.phone = 'Nomor telepon/WhatsApp wajib diisi.';
        } else if (!/^(\+62|0)8[0-9]{8,11}$/.test(phone)) {
            nextErrors.phone = 'Format nomor tidak valid, gunakan awalan 08 atau +62.';
        }

        const email = formData.email.trim();
        if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            nextErrors.email = 'Format email tidak valid.';
        }

        if (!formData.address.trim()) {
            nextErrors.address = 'Alamat wajib diisi.';
        }

        if (!formData.hamlet.trim()) {
            nextErrors.hamlet = 'Dusun wajib diisi.';
        }

        const year = Number(formData.establishedYear);
        if (!formData.establishedYear.trim()) {
            nextErrors.establishedYear = 'Tahun berdiri wajib diisi.';
        } else if (!Number.isInteger(year) || year < 1900 || year > CURRENT_YEAR) {
            nextErrors.establishedYear = `Masukkan tahun yang wajar (1900-${CURRENT_YEAR}).`;
        }

        const employeeCount = Number(formData.employeeCount);
        if (!formData.employeeCount.trim()) {
            nextErrors.employeeCount = 'Jumlah pekerja wajib diisi.';
        } else if (!Number.isInteger(employeeCount) || employeeCount < 0) {
            nextErrors.employeeCount = 'Masukkan angka yang valid (0 atau lebih).';
        }

        setErrors(nextErrors);
        return Object.keys(nextErrors).length === 0;
    }

    async function handleSubmit(event) {
        event.preventDefault();
        setSubmitStatus('idle');
        setServerError('');

        if (!validate()) {
            return;
        }

        setIsSubmitting(true);

        try {
            await apiFetch(submitUrl, {
                method: 'POST',
                body: JSON.stringify({
                    business_type_id: Number(formData.businessTypeId),
                    business_name: formData.businessName.trim(),
                    owner_name: formData.ownerName.trim(),
                    phone: formData.phone.trim(),
                    email: formData.email.trim() || null,
                    address: formData.address.trim(),
                    hamlet: formData.hamlet.trim(),
                    rt: formData.rt.trim() || null,
                    rw: formData.rw.trim() || null,
                    established_year: Number(formData.establishedYear),
                    employee_count: Number(formData.employeeCount),
                    description: formData.description.trim() || null,
                }),
            });

            if (isOfficerMode) {
                // Petugas mendata banyak UMKM berurutan, jadi form dikosongkan
                // lagi supaya langsung bisa input yang berikutnya.
                setFormData(buildInitialState(null));
                setSubmitStatus('success');
            } else {
                // Pelaku UMKM cuma isi sekali (atau update), lanjut ke dashboard.
                window.location.href = '/umkm/dashboard';
            }
        } catch (error) {
            setServerError(error.message);
        } finally {
            setIsSubmitting(false);
        }
    }

    return (
        <div className="relative isolate overflow-hidden">
            <PageBackground />

            <div className="mx-auto max-w-3xl">
                <header className="mb-8">
                    <p className="text-sm font-semibold text-primary">
                        {isOfficerMode ? 'Pendataan UMKM oleh Petugas' : 'Pendaftaran & Pembaruan Data'}
                    </p>
                    <h1 className="mt-1 text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">
                        {isOfficerMode ? 'Data Usaha UMKM' : 'Profil UMKM'}
                    </h1>
                    <p className="mt-2 max-w-xl text-sm leading-relaxed text-muted-foreground">
                        {isOfficerMode
                            ? 'Isi data usaha untuk UMKM yang belum punya akun sendiri. Pemiliknya bisa membuat akun belakangan untuk mengelola datanya sendiri.'
                            : 'Lengkapi data usaha di bawah ini. Data yang Anda isi akan dipakai petugas desa untuk memahami kebutuhan usaha Anda dan menentukan program pembinaan yang sesuai.'}
                    </p>
                </header>

                {submitStatus === 'success' && isOfficerMode && (
                    <div className="mb-6 rounded-xl border border-success/30 bg-success/10 px-4 py-3 text-sm text-success">
                        Data usaha berhasil disimpan. Silakan lanjutkan input UMKM berikutnya kalau masih ada.
                    </div>
                )}

                {serverError && (
                    <div className="mb-6 rounded-xl border border-destructive/30 bg-destructive/10 px-4 py-3 text-sm font-medium text-destructive">
                        {serverError}
                    </div>
                )}

                <form onSubmit={handleSubmit} noValidate className="flex flex-col gap-6">
                    <FormSection
                        icon={Store04Icon}
                        title="Data Identitas Usaha"
                        description="Informasi dasar mengenai usaha dan pemiliknya."
                    >
                        <Field label="Jenis Usaha" required error={errors.businessTypeId}>
                            <Select
                                value={formData.businessTypeId}
                                onChange={(event) => updateField('businessTypeId', event.target.value)}
                                aria-invalid={Boolean(errors.businessTypeId)}
                            >
                                <option value="" disabled>
                                    Pilih jenis usaha
                                </option>
                                {businessTypes.map((type) => (
                                    <option key={type.id} value={type.id}>
                                        {type.name}
                                    </option>
                                ))}
                            </Select>
                        </Field>

                        <Field label="Nama Usaha" required error={errors.businessName}>
                            <Input
                                value={formData.businessName}
                                onChange={(event) => updateField('businessName', event.target.value)}
                                placeholder="Contoh: Warung Ibu Sari"
                                aria-invalid={Boolean(errors.businessName)}
                            />
                        </Field>

                        <Field label="Nama Pemilik" required error={errors.ownerName}>
                            <Input
                                value={formData.ownerName}
                                onChange={(event) => updateField('ownerName', event.target.value)}
                                placeholder="Nama lengkap pemilik usaha"
                                aria-invalid={Boolean(errors.ownerName)}
                            />
                        </Field>

                        <Field label="Nomor Telepon/WhatsApp" required error={errors.phone}>
                            <Input
                                type="tel"
                                value={formData.phone}
                                onChange={(event) => updateField('phone', event.target.value)}
                                placeholder="08xxxxxxxxxx"
                                aria-invalid={Boolean(errors.phone)}
                            />
                        </Field>

                        <Field label="Email (opsional)" error={errors.email}>
                            <Input
                                type="email"
                                value={formData.email}
                                onChange={(event) => updateField('email', event.target.value)}
                                placeholder="nama@email.com"
                                aria-invalid={Boolean(errors.email)}
                            />
                        </Field>

                        <Field label="Alamat" required error={errors.address} className="sm:col-span-2">
                            <Textarea
                                rows={2}
                                value={formData.address}
                                onChange={(event) => updateField('address', event.target.value)}
                                placeholder="Alamat lengkap tempat usaha beroperasi"
                                aria-invalid={Boolean(errors.address)}
                            />
                        </Field>

                        <Field label="Dusun" required error={errors.hamlet}>
                            <Input
                                value={formData.hamlet}
                                onChange={(event) => updateField('hamlet', event.target.value)}
                                placeholder="Nama dusun"
                                aria-invalid={Boolean(errors.hamlet)}
                            />
                        </Field>

                        <div className="grid grid-cols-2 gap-4">
                            <Field label="RT (opsional)">
                                <Input value={formData.rt} onChange={(event) => updateField('rt', event.target.value)} placeholder="001" />
                            </Field>
                            <Field label="RW (opsional)">
                                <Input value={formData.rw} onChange={(event) => updateField('rw', event.target.value)} placeholder="002" />
                            </Field>
                        </div>

                        <Field label="Tahun Berdiri" required error={errors.establishedYear}>
                            <Input
                                type="number"
                                value={formData.establishedYear}
                                onChange={(event) => updateField('establishedYear', event.target.value)}
                                placeholder="Contoh: 2019"
                                aria-invalid={Boolean(errors.establishedYear)}
                            />
                        </Field>

                        <Field label="Jumlah Pekerja" required error={errors.employeeCount}>
                            <Input
                                type="number"
                                min="0"
                                value={formData.employeeCount}
                                onChange={(event) => updateField('employeeCount', event.target.value)}
                                placeholder="Contoh: 3"
                                aria-invalid={Boolean(errors.employeeCount)}
                            />
                        </Field>

                        <Field label="Deskripsi Usaha (opsional)" className="sm:col-span-2">
                            <Textarea
                                rows={3}
                                value={formData.description}
                                onChange={(event) => updateField('description', event.target.value)}
                                placeholder="Ceritakan sedikit tentang usaha ini"
                            />
                        </Field>
                    </FormSection>

                    <div className="flex justify-end">
                        <Button type="submit" disabled={isSubmitting}>
                            {isSubmitting ? 'Menyimpan...' : isOfficerMode ? 'Simpan Data UMKM' : 'Simpan Profil Usaha'}
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    );
}

function Field({ label, required = false, error, className, children }) {
    return (
        <div className={cn('flex flex-col', className)}>
            <Label className="mb-1.5">
                {label}
                {required && <span className="ml-0.5 text-destructive">*</span>}
            </Label>
            {children}
            {error && <p className="mt-1.5 text-xs font-medium text-destructive">{error}</p>}
        </div>
    );
}