import { useEffect, useRef, useState } from 'react';
import PageBackground from '@/components/umkm/PageBackground';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { apiFetch } from '@/lib/api';
import { cn } from '@/lib/utils';

const UNIT_OPTIONS = ['Pcs', 'Kg', 'Gram', 'Liter', 'Porsi', 'Lusin', 'Lainnya'];

function buildEmptyForm() {
    return {
        name: '',
        category: '',
        description: '',
        price: '',
        unitChoice: '',
        customUnit: '',
        photoFile: null,
    };
}

function formatRupiah(value) {
    if (value === null || value === undefined || value === '') {
        return '-';
    }

    return `Rp${Number(value).toLocaleString('id-ID')}`;
}

/**
 * Dipakai untuk dua hal, dibedakan lewat page-props `mode`:
 * - mode="self"    -> UC-05a, pelaku UMKM kelola produk usahanya sendiri.
 * - mode="officer" -> UC-05a, petugas mendampingi kelola produk UMKM tertentu.
 */
export default function ProductManager({ business, mode = 'self' }) {
    const isOfficerMode = mode === 'officer';

    const listUrl = isOfficerMode ? `/petugas/umkm/${business.id}/produk/data` : '/umkm/produk/data';
    const createUrl = isOfficerMode ? `/petugas/umkm/${business.id}/produk` : '/umkm/produk';
    const itemUrl = (id) => (isOfficerMode ? `/petugas/umkm/${business.id}/produk/${id}` : `/umkm/produk/${id}`);

    const [products, setProducts] = useState([]);
    const [isLoading, setIsLoading] = useState(true);
    const [errorMessage, setErrorMessage] = useState('');

    const [formData, setFormData] = useState(buildEmptyForm);
    const [photoPreview, setPhotoPreview] = useState(null);
    const [editingId, setEditingId] = useState(null);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const fileInputRef = useRef(null);

    useEffect(() => {
        loadProducts();
    }, []);

    useEffect(
        () => () => {
            if (photoPreview && photoPreview.startsWith('blob:')) {
                URL.revokeObjectURL(photoPreview);
            }
        },
        [photoPreview],
    );

    async function loadProducts() {
        setIsLoading(true);
        setErrorMessage('');

        try {
            const data = await apiFetch(listUrl);
            setProducts(data);
        } catch (error) {
            setErrorMessage(error.message);
        } finally {
            setIsLoading(false);
        }
    }

    function updateField(field, value) {
        setFormData((prev) => ({ ...prev, [field]: value }));
    }

    function handlePhotoChange(event) {
        const file = event.target.files?.[0] ?? null;
        updateField('photoFile', file);
        setPhotoPreview(file ? URL.createObjectURL(file) : null);
    }

    function startEdit(product) {
        const isKnownUnit = UNIT_OPTIONS.slice(0, -1).includes(product.unit);

        setEditingId(product.id);
        setFormData({
            name: product.name,
            category: product.category,
            description: product.description ?? '',
            price: product.price !== null ? String(product.price) : '',
            unitChoice: product.unit ? (isKnownUnit ? product.unit : 'Lainnya') : '',
            customUnit: product.unit && !isKnownUnit ? product.unit : '',
            photoFile: null,
        });
        setPhotoPreview(product.photo_path ? `/storage/${product.photo_path}` : null);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function cancelEdit() {
        setEditingId(null);
        setFormData(buildEmptyForm());
        setPhotoPreview(null);
        if (fileInputRef.current) {
            fileInputRef.current.value = '';
        }
    }

    async function handleSubmit(event) {
        event.preventDefault();

        if (!formData.name.trim() || !formData.category.trim()) {
            setErrorMessage('Nama dan kategori produk wajib diisi.');
            return;
        }

        setIsSubmitting(true);
        setErrorMessage('');

        const finalUnit = formData.unitChoice === 'Lainnya' ? formData.customUnit.trim() : formData.unitChoice;

        const payload = new FormData();
        payload.append('name', formData.name.trim());
        payload.append('category', formData.category.trim());
        if (formData.description.trim()) payload.append('description', formData.description.trim());
        if (formData.price !== '') payload.append('price', formData.price);
        if (finalUnit) payload.append('unit', finalUnit);
        if (formData.photoFile) payload.append('photo', formData.photoFile);

        try {
            if (editingId) {
                payload.append('_method', 'PUT');
                const updated = await apiFetch(itemUrl(editingId), { method: 'POST', body: payload });
                setProducts((prev) => prev.map((item) => (item.id === editingId ? updated : item)));
            } else {
                const created = await apiFetch(createUrl, { method: 'POST', body: payload });
                setProducts((prev) => [...prev, created].sort((a, b) => a.name.localeCompare(b.name)));
            }

            cancelEdit();
        } catch (error) {
            setErrorMessage(error.message);
        } finally {
            setIsSubmitting(false);
        }
    }

    async function handleToggle(product) {
        setErrorMessage('');

        try {
            const updated = await apiFetch(`${itemUrl(product.id)}/toggle`, { method: 'PATCH' });
            setProducts((prev) => prev.map((item) => (item.id === product.id ? updated : item)));
        } catch (error) {
            setErrorMessage(error.message);
        }
    }

    const content = (
        <div className="mx-auto max-w-3xl">
            <header className="mb-8">
                <p className="text-sm font-semibold text-primary">
                    {isOfficerMode ? 'Pendampingan Produk' : 'Kelola Produk'}
                </p>
                <h1 className="mt-1 text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">
                    {isOfficerMode ? `Produk ${business.name}` : 'Produk Usahamu'}
                </h1>
                <p className="mt-2 max-w-xl text-sm leading-relaxed text-muted-foreground">
                    {isOfficerMode
                        ? `Tambah atau ubah produk milik ${business.name} atas nama petugas.`
                        : 'Tambahkan produk yang kamu jual supaya pembeli dan petugas bisa lihat usahamu lebih lengkap.'}
                </p>
            </header>

            {errorMessage && (
                <div className="mb-6 rounded-xl border border-destructive/30 bg-destructive/10 px-4 py-3 text-sm font-medium text-destructive">
                    {errorMessage}
                </div>
            )}

            <Card className="mb-6">
                <CardHeader>
                    <CardTitle className="text-base">{editingId ? 'Edit Produk' : 'Tambah Produk'}</CardTitle>
                </CardHeader>
                <CardContent>
                    <form onSubmit={handleSubmit} className="grid gap-4 sm:grid-cols-2">
                        <div className="flex flex-col">
                            <Label className="mb-1.5">Nama Produk</Label>
                            <Input
                                value={formData.name}
                                onChange={(event) => updateField('name', event.target.value)}
                                placeholder="Contoh: Keripik Singkong Original"
                            />
                        </div>

                        <div className="flex flex-col">
                            <Label className="mb-1.5">Kategori</Label>
                            <Input
                                value={formData.category}
                                onChange={(event) => updateField('category', event.target.value)}
                                placeholder="Contoh: Makanan Ringan"
                            />
                        </div>

                        <div className="flex flex-col">
                            <Label className="mb-1.5">Harga (opsional)</Label>
                            <div className="relative">
                                <span className="pointer-events-none absolute top-1/2 left-4 -translate-y-1/2 text-sm text-muted-foreground">
                                    Rp
                                </span>
                                <Input
                                    type="text"
                                    inputMode="numeric"
                                    value={formData.price}
                                    onChange={(event) => updateField('price', event.target.value.replace(/\D/g, ''))}
                                    placeholder="15000"
                                    className="pl-10"
                                />
                            </div>
                        </div>

                        <div className="flex flex-col">
                            <Label className="mb-1.5">Satuan (opsional)</Label>
                            <Select value={formData.unitChoice} onChange={(event) => updateField('unitChoice', event.target.value)}>
                                <option value="">Pilih satuan</option>
                                {UNIT_OPTIONS.map((unit) => (
                                    <option key={unit} value={unit}>
                                        {unit}
                                    </option>
                                ))}
                            </Select>
                            {formData.unitChoice === 'Lainnya' && (
                                <Input
                                    value={formData.customUnit}
                                    onChange={(event) => updateField('customUnit', event.target.value)}
                                    placeholder="Tulis satuan sendiri"
                                    className="mt-2"
                                />
                            )}
                        </div>

                        <div className="flex flex-col sm:col-span-2">
                            <Label className="mb-1.5">Deskripsi (opsional)</Label>
                            <Textarea
                                rows={2}
                                value={formData.description}
                                onChange={(event) => updateField('description', event.target.value)}
                                placeholder="Ceritakan sedikit tentang produk ini"
                            />
                        </div>

                        <div className="flex flex-col sm:col-span-2">
                            <Label className="mb-1.5">Foto (opsional)</Label>
                            <button
                                type="button"
                                onClick={() => fileInputRef.current?.click()}
                                className="flex items-center gap-4 rounded-xl border border-dashed border-border bg-white p-4 text-left transition-colors hover:border-primary/50"
                            >
                                {photoPreview ? (
                                    <img src={photoPreview} alt="Pratinjau produk" className="size-16 shrink-0 rounded-lg object-cover" />
                                ) : (
                                    <div className="flex size-16 shrink-0 items-center justify-center rounded-lg bg-muted text-[10px] text-muted-foreground">
                                        Belum ada
                                    </div>
                                )}
                                <span className="text-sm font-medium text-primary">
                                    {photoPreview ? 'Ganti foto' : 'Klik untuk pilih foto'}
                                </span>
                            </button>
                            <input
                                ref={fileInputRef}
                                type="file"
                                accept="image/*"
                                onChange={handlePhotoChange}
                                className="hidden"
                            />
                        </div>

                        <div className="flex gap-2 sm:col-span-2">
                            <Button type="submit" disabled={isSubmitting}>
                                {isSubmitting ? 'Menyimpan...' : editingId ? 'Simpan Perubahan' : 'Tambah Produk'}
                            </Button>
                            {editingId && (
                                <Button type="button" variant="outline" onClick={cancelEdit}>
                                    Batal
                                </Button>
                            )}
                        </div>
                    </form>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle className="text-base">Daftar Produk</CardTitle>
                </CardHeader>
                <CardContent className="flex flex-col gap-3">
                    {isLoading && <p className="text-sm text-muted-foreground">Memuat data...</p>}

                    {!isLoading && products.length === 0 && (
                        <p className="text-sm text-muted-foreground">Belum ada produk, tambahkan lewat form di atas.</p>
                    )}

                    {products.map((product) => (
                        <div
                            key={product.id}
                            className="flex items-center justify-between gap-3 rounded-xl border border-border bg-white px-4 py-3"
                        >
                            <div className="flex items-center gap-3">
                                {product.photo_path ? (
                                    <img
                                        src={`/storage/${product.photo_path}`}
                                        alt={product.name}
                                        className="size-12 shrink-0 rounded-lg object-cover"
                                    />
                                ) : (
                                    <div className="flex size-12 shrink-0 items-center justify-center rounded-lg bg-muted text-[10px] text-muted-foreground">
                                        Tanpa foto
                                    </div>
                                )}
                                <div>
                                    <p className="text-sm font-semibold text-ink">{product.name}</p>
                                    <p className="text-xs text-muted-foreground">
                                        {product.category} &middot; {formatRupiah(product.price)}
                                        {product.unit ? ` / ${product.unit}` : ''}
                                    </p>
                                </div>
                            </div>

                            <div className="flex shrink-0 items-center gap-2">
                                <Badge variant={product.is_active ? 'success' : 'outline'}>
                                    {product.is_active ? 'Aktif' : 'Nonaktif'}
                                </Badge>
                                <Button type="button" size="sm" variant="outline" onClick={() => startEdit(product)}>
                                    Edit
                                </Button>
                                <Button type="button" size="sm" variant="outline" onClick={() => handleToggle(product)}>
                                    {product.is_active ? 'Nonaktifkan' : 'Aktifkan'}
                                </Button>
                            </div>
                        </div>
                    ))}
                </CardContent>
            </Card>
        </div>
    );

    if (isOfficerMode) {
        return <div className="relative isolate overflow-hidden">{content}</div>;
    }

    return (
        <div className="relative isolate overflow-hidden">
            <PageBackground />
            {content}
        </div>
    );
}
