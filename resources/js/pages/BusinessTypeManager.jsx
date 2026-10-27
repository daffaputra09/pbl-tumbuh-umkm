import { useEffect, useState } from 'react';
import { HugeiconsIcon } from '@hugeicons/react';
import { PlusSignIcon, Search01Icon } from '@hugeicons/core-free-icons';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { EditIconButton } from '@/components/ui/edit-icon-button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { apiFetch } from '@/lib/api';

export default function BusinessTypeManager() {
    const [types, setTypes] = useState([]);
    const [isLoading, setIsLoading] = useState(true);
    const [errorMessage, setErrorMessage] = useState('');
    const [query, setQuery] = useState('');

    const [isModalOpen, setIsModalOpen] = useState(false);
    const [editingId, setEditingId] = useState(null);
    const [name, setName] = useState('');
    const [formError, setFormError] = useState('');
    const [isSubmitting, setIsSubmitting] = useState(false);

    useEffect(() => {
        loadTypes();
    }, []);

    async function loadTypes() {
        setIsLoading(true);
        setErrorMessage('');

        try {
            const data = await apiFetch('/api/business-types');
            setTypes(data);
        } catch (error) {
            setErrorMessage(error.message);
        } finally {
            setIsLoading(false);
        }
    }

    function openCreateModal() {
        setEditingId(null);
        setName('');
        setFormError('');
        setIsModalOpen(true);
    }

    function openEditModal(type) {
        setEditingId(type.id);
        setName(type.name);
        setFormError('');
        setIsModalOpen(true);
    }

    function closeModal() {
        if (isSubmitting) {
            return;
        }

        setIsModalOpen(false);
        setEditingId(null);
        setName('');
        setFormError('');
    }

    async function handleSubmit(event) {
        event.preventDefault();

        if (!name.trim()) {
            setFormError('Nama jenis usaha wajib diisi.');
            return;
        }

        setIsSubmitting(true);
        setFormError('');

        try {
            if (editingId) {
                const updated = await apiFetch(`/api/business-types/${editingId}`, {
                    method: 'PUT',
                    body: JSON.stringify({ name: name.trim() }),
                });
                setTypes((prev) => prev.map((type) => (type.id === editingId ? updated : type)));
            } else {
                const created = await apiFetch('/api/business-types', {
                    method: 'POST',
                    body: JSON.stringify({ name: name.trim() }),
                });
                setTypes((prev) => [...prev, created].sort((a, b) => a.name.localeCompare(b.name)));
            }

            setIsSubmitting(false);
            setIsModalOpen(false);
            setEditingId(null);
            setName('');
        } catch (error) {
            setFormError(error.message);
            setIsSubmitting(false);
        }
    }

    async function handleToggle(id) {
        setErrorMessage('');

        try {
            const updated = await apiFetch(`/api/business-types/${id}/toggle`, { method: 'PATCH' });
            setTypes((prev) => prev.map((type) => (type.id === id ? updated : type)));
        } catch (error) {
            setErrorMessage(error.message);
        }
    }

    const keyword = query.trim().toLowerCase();
    const visibleTypes = keyword
        ? types.filter((type) => `${type.name} ${type.slug}`.toLowerCase().includes(keyword))
        : types;

    return (
        <div className="mx-auto max-w-3xl">
            <header className="mb-8">
                <p className="text-sm font-semibold text-primary">Panel Petugas</p>
                <h1 className="mt-1 text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Kelola Jenis Usaha</h1>
                <p className="mt-2 text-sm leading-relaxed text-muted-foreground">
                    Daftar jenis usaha yang muncul di dropdown pada form Profil UMKM.
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
                        <CardTitle className="text-base">Daftar Jenis Usaha</CardTitle>
                        <Button type="button" size="sm" onClick={openCreateModal}>
                            <HugeiconsIcon icon={PlusSignIcon} size={16} aria-hidden="true" />
                            Tambah Jenis Usaha
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
                            placeholder="Cari jenis usaha"
                            className="pl-10"
                            aria-label="Cari jenis usaha"
                        />
                    </div>
                </CardHeader>

                <CardContent className="flex flex-col gap-2">
                    {isLoading && <p className="text-sm text-muted-foreground">Memuat data...</p>}

                    {!isLoading && types.length === 0 && (
                        <p className="text-sm text-muted-foreground">
                            Belum ada jenis usaha. Klik tombol Tambah Jenis Usaha untuk mulai.
                        </p>
                    )}

                    {!isLoading && types.length > 0 && visibleTypes.length === 0 && (
                        <p className="text-sm text-muted-foreground">Tidak ada jenis usaha yang cocok dengan pencarianmu.</p>
                    )}

                    {visibleTypes.map((type) => (
                        <div
                            key={type.id}
                            className="flex items-center justify-between gap-3 rounded-xl border border-border bg-white px-4 py-3"
                        >
                            <div className="min-w-0">
                                <p className="truncate text-sm font-semibold text-ink">{type.name}</p>
                                <p className="truncate text-xs text-muted-foreground">{type.slug}</p>
                            </div>

                            <div className="flex shrink-0 items-center gap-2">
                                <Badge variant={type.is_active ? 'success' : 'outline'}>
                                    {type.is_active ? 'Aktif' : 'Nonaktif'}
                                </Badge>
                                <EditIconButton label={`Edit ${type.name}`} onClick={() => openEditModal(type)} />
                                <Button type="button" size="sm" variant="outline" onClick={() => handleToggle(type.id)}>
                                    {type.is_active ? 'Nonaktifkan' : 'Aktifkan'}
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
                    aria-labelledby="business-type-modal-title"
                    onMouseDown={(event) => {
                        if (event.target === event.currentTarget) {
                            closeModal();
                        }
                    }}
                >
                    <div className="my-auto flex max-h-[90vh] w-full max-w-lg flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl">
                        {/* Header */}
                        <div className="flex items-center justify-between gap-3 border-b border-slate-200 px-6 py-5">
                            <h2
                                id="business-type-modal-title"
                                className="text-lg font-bold text-ink"
                            >
                                {editingId ? 'Edit Jenis Usaha' : 'Tambah Jenis Usaha'}
                            </h2>

                            <button
                                type="button"
                                onClick={closeModal}
                                disabled={isSubmitting}
                                aria-label="Tutup"
                                className="grid size-8 shrink-0 place-items-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <span aria-hidden="true" className="text-2xl leading-none">
                                    &times;
                                </span>
                            </button>
                        </div>

                        {/* Form */}
                        <form
                            onSubmit={handleSubmit}
                            className="flex min-h-0 flex-col overflow-y-auto"
                        >
                            <div className="flex flex-col gap-5 p-6">
                                {formError && (
                                    <div className="rounded-xl border border-destructive/30 bg-destructive/10 px-4 py-3 text-sm font-medium text-destructive">
                                        {formError}
                                    </div>
                                )}

                                <div className="flex flex-col">
                                    <Label className="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-600">
                                        Nama Jenis Usaha
                                    </Label>

                                    <Input
                                        value={name}
                                        onChange={(event) => setName(event.target.value)}
                                        placeholder="Contoh: Perikanan"
                                        autoFocus
                                    />

                                    <p className="mt-2 text-xs text-muted-foreground">
                                        Kode (slug) dibuat otomatis dari nama.
                                    </p>
                                </div>
                            </div>

                            {/* Footer */}
                            <div className="flex justify-end gap-3 border-t border-slate-200 bg-slate-50/70 px-6 py-4">
                                <Button
                                    type="button"
                                    variant="outline"
                                    onClick={closeModal}
                                    disabled={isSubmitting}
                                >
                                    Batal
                                </Button>

                                <Button type="submit" disabled={isSubmitting}>
                                    {isSubmitting
                                        ? 'Menyimpan...'
                                        : editingId
                                            ? 'Simpan Perubahan'
                                            : 'Tambah'}
                                </Button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

        </div>
    );
}
