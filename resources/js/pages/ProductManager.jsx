import { useEffect, useState } from 'react';
import PageBackground from '@/components/umkm/PageBackground';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { apiFetch } from '@/lib/api';
import { cn } from '@/lib/utils';

function buildEmptyForm() {
    return {
        name: '',
        category: '',
        description: '',
        price: '',
        unit: '',
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
    const [editingId, setEditingId] = useState(null);
    const [isSubmitting, setIsSubmitting] = useState(false);

    useEffect(() => {
        loadProducts();
    }, []);

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

    function startEdit(product) {
        setEditingId(product.id);
        setFormData({
            name: product.name,
            category: product.category,
            description: product.description ?? '',
            price: product.price !== null ? String(product.price) : '',
            unit: product.unit ?? '',
            photoFile: null,
        });
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function cancelEdit() {
        setEditingId(null);
        setFormData(buildEmptyForm());
    }

    async function handleSubmit(event) {
        event.preventDefault();

        if (!formData.name.trim() || !formData.category.trim()) {
            setErrorMessage('Nama dan kategori produk wajib diisi.');
            return;
        }

        setIsSubmitting(true);
        setErrorMessage('');

        const payload = new FormData();
        payload.append('name', formData.name.trim());
        payload.append('category', formData.category.trim());
        if (formData.description.trim()) payload.append('description', formData.description.trim());
        if (formData.price !== '') payload.append('price', formData.price);
        if (formData.unit.trim()) payload.append('unit', formData.unit.trim());
        if (formData.photoFile) payload.append('photo', formData.photoFile);

        try {
            if (editingId) {
                // Laravel baca method PUT lewat _method kalau dikirim via FormData.
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
        <div className="mx-auto max-w-3xl px-5 py-10 sm:py-14">
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
                            <Input
                                type="number"
                                min="0"
                                value={formData.price}
                                onChange={(event) => updateField('price', event.target.value)}
                                placeholder="Contoh: 15000"
                            />
                        </div>

                        <div className="flex flex-col">
                            <Label className="mb-1.5">Satuan (opsional)</Label>
                            <Input
                                value={formData.unit}
                                onChange={(event) => updateField('unit', event.target.value)}
                                placeholder="Contoh: pcs, kg, porsi"
                            />
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
                            <input
                                type="file"
                                accept="image/*"
                                onChange={(event) => updateField('photoFile', event.target.files?.[0] ?? null)}
                                className="text-sm text-muted-foreground file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-primary"
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
        return <div className={cn('min-h-screen bg-background')}>{content}</div>;
    }

    return (
        <div className="relative isolate min-h-screen overflow-hidden bg-background">
            <PageBackground />
            {content}
        </div>
    );
}
