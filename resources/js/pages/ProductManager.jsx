import { useEffect, useRef, useState } from 'react';
import { HugeiconsIcon } from '@hugeicons/react';
import { PlusSignIcon, Search01Icon } from '@hugeicons/core-free-icons';
import PageBackground from '@/components/umkm/PageBackground';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { EditIconButton } from '@/components/ui/edit-icon-button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { apiFetch } from '@/lib/api';

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
 * UC-05a, pelaku UMKM kelola produk usahanya sendiri.
 *
 * Form tambah/ubah produk tampil sebagai popup.
 */
export default function ProductManager() {
    const listUrl = '/umkm/produk/data';
    const createUrl = '/umkm/produk';
    const itemUrl = (id) => `/umkm/produk/${id}`;

    const [products, setProducts] = useState([]);
    const [isLoading, setIsLoading] = useState(true);
    const [errorMessage, setErrorMessage] = useState('');
    const [query, setQuery] = useState('');

    const [isModalOpen, setIsModalOpen] = useState(false);
    const [formData, setFormData] = useState(buildEmptyForm);
    const [formError, setFormError] = useState('');
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

    function openCreateModal() {
        setEditingId(null);
        setFormData(buildEmptyForm());
        setPhotoPreview(null);
        setFormError('');
        setIsModalOpen(true);
    }

    function openEditModal(product) {
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
        setFormError('');
        setIsModalOpen(true);
    }

    function closeModal() {
        if (isSubmitting) {
            return;
        }

        setIsModalOpen(false);
        setEditingId(null);
        setFormData(buildEmptyForm());
        setPhotoPreview(null);
        setFormError('');
    }

    async function handleSubmit(event) {
        event.preventDefault();

        if (!formData.name.trim() || !formData.category.trim()) {
            setFormError('Nama dan kategori produk wajib diisi.');
            return;
        }

        setIsSubmitting(true);
        setFormError('');

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

            setIsSubmitting(false);
            setIsModalOpen(false);
            setEditingId(null);
            setFormData(buildEmptyForm());
            setPhotoPreview(null);
        } catch (error) {
            setFormError(error.message);
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

    const keyword = query.trim().toLowerCase();
    const visibleProducts = keyword
        ? products.filter((product) => `${product.name} ${product.category}`.toLowerCase().includes(keyword))
        : products;

    const content = (
        <div className="mx-auto max-w-3xl">
            <header className="mb-8">
                <p className="text-sm font-semibold text-primary">Kelola Produk</p>
                <h1 className="mt-1 text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">
                    Produk Usahamu
                </h1>
                <p className="mt-2 max-w-xl text-sm leading-relaxed text-muted-foreground">
                    Tambahkan produk yang kamu jual supaya pembeli dan petugas bisa lihat usahamu lebih lengkap.
                </p>
            </header>

            {errorMessage && (
                <div className="mb-6 rounded-xl border border-destructive/30 bg-destructive/10 px-4 py-3 text-sm font-medium text-destructive">
                    {errorMessage}
                </div>
            )}

            <Card>
                <CardHeader className="gap-3">
                    <div className="flex items-center justify-between gap-3">
                        <CardTitle className="text-base">Daftar Produk</CardTitle>
                        <Button type="button" size="sm" onClick={openCreateModal}>
                            <HugeiconsIcon icon={PlusSignIcon} size={16} aria-hidden="true" />
                            Tambah Produk
                        </Button>
                    </div>

                    <div className="relative">
                        <HugeiconsIcon
                            icon={Search01Icon}
                            size={16}
                            className="pointer-events-none absolute top-1/2 left-4 -translate-y-1/2 text-muted-foreground"
                            aria-hidden="true"
                        />
                        <Input
                            type="search"
                            value={query}
                            onChange={(event) => setQuery(event.target.value)}
                            placeholder="Cari nama atau kategori produk"
                            className="pl-10"
                            aria-label="Cari produk"
                        />
                    </div>
                </CardHeader>

                <CardContent className="flex flex-col gap-3">
                    {isLoading && <p className="text-sm text-muted-foreground">Memuat data...</p>}

                    {!isLoading && products.length === 0 && (
                        <p className="text-sm text-muted-foreground">
                            Belum ada produk. Klik tombol Tambah Produk untuk mulai.
                        </p>
                    )}

                    {!isLoading && products.length > 0 && visibleProducts.length === 0 && (
                        <p className="text-sm text-muted-foreground">Tidak ada produk yang cocok dengan pencarianmu.</p>
                    )}

                    {visibleProducts.map((product) => (
                        <div
                            key={product.id}
                            className="flex items-center justify-between gap-3 rounded-xl border border-border bg-white px-4 py-3"
                        >
                            <div className="flex min-w-0 items-center gap-3">
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
                                <div className="min-w-0">
                                    <p className="truncate text-sm font-semibold text-ink">{product.name}</p>
                                    <p className="truncate text-xs text-muted-foreground">
                                        {product.category} &middot; {formatRupiah(product.price)}
                                        {product.unit ? ` / ${product.unit}` : ''}
                                    </p>
                                </div>
                            </div>

                            <div className="flex shrink-0 items-center gap-2">
                                <Badge variant={product.is_active ? 'success' : 'outline'}>
                                    {product.is_active ? 'Aktif' : 'Nonaktif'}
                                </Badge>
                                <EditIconButton
                                    label={`Edit produk ${product.name}`}
                                    onClick={() => openEditModal(product)}
                                />
                                <Button type="button" size="sm" variant="outline" onClick={() => handleToggle(product)}>
                                    {product.is_active ? 'Nonaktifkan' : 'Aktifkan'}
                                </Button>
                            </div>
                        </div>
                    ))}
                </CardContent>
            </Card>

            {isModalOpen && (
                <div
                    className="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-slate-900/50 p-4 backdrop-blur-sm sm:p-6"
                    role="dialog"
                    aria-modal="true"
                    aria-labelledby="product-modal-title"
                    onMouseDown={(event) => {
                        if (event.target === event.currentTarget && !isSubmitting) {
                            closeModal();
                        }
                    }}
                >
                    <div className="my-auto flex max-h-[90vh] w-full max-w-2xl flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl">
                        {/* Header popup */}
                        <div className="flex shrink-0 items-center justify-between border-b border-slate-200 px-6 py-5">
                            <div>
                                <h2
                                    id="product-modal-title"
                                    className="text-lg font-bold text-ink"
                                >
                                    {editingId ? 'Edit Produk' : 'Tambah Produk'}
                                </h2>
                                <p className="mt-1 text-sm text-muted-foreground">
                                    {editingId
                                        ? 'Perbarui informasi produk UMKM.'
                                        : 'Lengkapi informasi produk UMKM yang ingin ditampilkan.'}
                                </p>
                            </div>

                            <button
                                type="button"
                                onClick={closeModal}
                                disabled={isSubmitting}
                                aria-label="Tutup popup"
                                className="grid size-9 shrink-0 place-items-center rounded-lg text-slate-500 transition-colors hover:bg-slate-100 hover:text-slate-700 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <span aria-hidden="true" className="text-2xl leading-none">
                                    &times;
                                </span>
                            </button>
                        </div>

                        {/* Isi formulir */}
                        <form
                            onSubmit={handleSubmit}
                            className="flex min-h-0 flex-col overflow-y-auto"
                        >
                            <div className="grid gap-x-5 gap-y-5 p-6 sm:grid-cols-2">
                                {formError && (
                                    <div className="rounded-xl border border-destructive/30 bg-destructive/10 px-4 py-3 text-sm font-medium text-destructive sm:col-span-2">
                                        {formError}
                                    </div>
                                )}

                                <div className="flex flex-col gap-2">
                                    <Label className="text-xs font-semibold uppercase tracking-wide text-slate-600">
                                        Nama Produk
                                    </Label>
                                    <Input
                                        value={formData.name}
                                        onChange={(event) => updateField('name', event.target.value)}
                                        placeholder="Contoh: Keripik Singkong Original"
                                    />
                                </div>

                                <div className="flex flex-col gap-2">
                                    <Label className="text-xs font-semibold uppercase tracking-wide text-slate-600">
                                        Kategori
                                    </Label>
                                    <Input
                                        value={formData.category}
                                        onChange={(event) => updateField('category', event.target.value)}
                                        placeholder="Contoh: Makanan Ringan"
                                    />
                                </div>

                                <div className="flex flex-col gap-2">
                                    <Label className="text-xs font-semibold uppercase tracking-wide text-slate-600">
                                        Harga (Opsional)
                                    </Label>
                                    <div className="relative">
                                        <span className="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-sm text-muted-foreground">
                                            Rp
                                        </span>
                                        <Input
                                            type="text"
                                            inputMode="numeric"
                                            value={formData.price}
                                            onChange={(event) =>
                                                updateField('price', event.target.value.replace(/\D/g, ''))
                                            }
                                            placeholder="15000"
                                            className="pl-10"
                                        />
                                    </div>
                                </div>

                                <div className="flex flex-col gap-2">
                                    <Label className="text-xs font-semibold uppercase tracking-wide text-slate-600">
                                        Satuan (Opsional)
                                    </Label>
                                    <Select
                                        value={formData.unitChoice}
                                        onChange={(event) => updateField('unitChoice', event.target.value)}
                                    >
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
                                        />
                                    )}
                                </div>

                                <div className="flex flex-col gap-2 sm:col-span-2">
                                    <Label className="text-xs font-semibold uppercase tracking-wide text-slate-600">
                                        Deskripsi (Opsional)
                                    </Label>
                                    <Textarea
                                        rows={3}
                                        value={formData.description}
                                        onChange={(event) => updateField('description', event.target.value)}
                                        placeholder="Ceritakan sedikit tentang produk ini"
                                    />
                                </div>

                                <div className="flex flex-col gap-2 sm:col-span-2">
                                    <Label className="text-xs font-semibold uppercase tracking-wide text-slate-600">
                                        Foto Produk (Opsional)
                                    </Label>

                                    <button
                                        type="button"
                                        onClick={() => fileInputRef.current?.click()}
                                        className="flex items-center gap-4 rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4 text-left transition-colors hover:border-primary/50 hover:bg-slate-100"
                                    >
                                        {photoPreview ? (
                                            <img
                                                src={photoPreview}
                                                alt="Pratinjau produk"
                                                className="size-16 shrink-0 rounded-lg object-cover"
                                            />
                                        ) : (
                                            <div className="flex size-16 shrink-0 items-center justify-center rounded-lg bg-white text-xs text-muted-foreground">
                                                Tanpa foto
                                            </div>
                                        )}

                                        <div>
                                            <p className="text-sm font-semibold text-slate-700">
                                                {photoPreview ? 'Ganti foto produk' : 'Pilih foto produk'}
                                            </p>
                                            <p className="mt-1 text-xs text-muted-foreground">
                                                Klik di sini untuk memilih gambar.
                                            </p>
                                        </div>
                                    </button>

                                    <input
                                        ref={fileInputRef}
                                        type="file"
                                        accept="image/*"
                                        onChange={handlePhotoChange}
                                        className="hidden"
                                    />
                                </div>
                            </div>

                            {/* Footer popup */}
                            <div className="flex shrink-0 flex-col-reverse gap-3 border-t border-slate-200 bg-slate-50/70 px-6 py-4 sm:flex-row sm:justify-end">
                                <Button
                                    type="button"
                                    variant="outline"
                                    onClick={closeModal}
                                    disabled={isSubmitting}
                                    className="sm:min-w-28"
                                >
                                    Batal
                                </Button>

                                <Button
                                    type="submit"
                                    disabled={isSubmitting}
                                    className="sm:min-w-36"
                                >
                                    {isSubmitting
                                        ? 'Menyimpan...'
                                        : editingId
                                          ? 'Simpan Perubahan'
                                          : 'Tambah Produk'}
                                </Button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

        </div>
    );

    return (
        <div className="relative">
            <PageBackground />
            {content}
        </div>
    );
}
