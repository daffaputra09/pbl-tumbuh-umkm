import { useEffect, useState } from 'react';
import { HugeiconsIcon } from '@hugeicons/react';
import {
    Alert02Icon,
    ArrowLeft02Icon,
    Cancel01Icon,
    CheckmarkCircle02Icon,
    PlusSignIcon,
    Search01Icon,
    Tag01Icon,
} from '@hugeicons/core-free-icons';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { apiFetch } from '@/lib/api';

export default function BusinessTypeManager() {
    const [types, setTypes] = useState([]);
    const [isLoading, setIsLoading] = useState(true);
    const [errorMessage, setErrorMessage] = useState('');
    const [successMessage, setSuccessMessage] = useState('');
    const [query, setQuery] = useState('');

    // Modal Add / Edit
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [editingId, setEditingId] = useState(null);
    const [name, setName] = useState('');
    const [formError, setFormError] = useState('');
    const [isSubmitting, setIsSubmitting] = useState(false);

    // Modal Delete Confirmation
    const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false);
    const [typeToDelete, setTypeToDelete] = useState(null);
    const [deleteError, setDeleteError] = useState('');
    const [isDeleting, setIsDeleting] = useState(false);

    // Toggle loading
    const [isTogglingId, setIsTogglingId] = useState(null);

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
                setSuccessMessage('Jenis usaha berhasil diperbarui.');
            } else {
                const created = await apiFetch('/api/business-types', {
                    method: 'POST',
                    body: JSON.stringify({ name: name.trim() }),
                });
                setTypes((prev) => [...prev, created].sort((a, b) => a.name.localeCompare(b.name)));
                setSuccessMessage('Jenis usaha berhasil ditambahkan.');
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
        setSuccessMessage('');
        setIsTogglingId(id);

        try {
            const updated = await apiFetch(`/api/business-types/${id}/toggle`, { method: 'PATCH' });
            setTypes((prev) => prev.map((type) => (type.id === id ? updated : type)));
            setSuccessMessage(`Status jenis usaha "${updated.name}" berhasil diubah menjadi ${updated.is_active ? 'aktif' : 'nonaktif'}.`);
        } catch (error) {
            setErrorMessage(error.message);
        } finally {
            setIsTogglingId(null);
        }
    }

    function openDeleteModal(type) {
        setTypeToDelete(type);
        setDeleteError('');
        setIsDeleteModalOpen(true);
    }

    function closeDeleteModal() {
        if (isDeleting) {
            return;
        }

        setIsDeleteModalOpen(false);
        setTypeToDelete(null);
        setDeleteError('');
    }

    async function handleDelete() {
        if (!typeToDelete) {
            return;
        }

        setIsDeleting(true);
        setDeleteError('');

        try {
            const response = await apiFetch(`/api/business-types/${typeToDelete.id}`, {
                method: 'DELETE',
            });
            setTypes((prev) => prev.filter((type) => type.id !== typeToDelete.id));
            setSuccessMessage(response?.message || 'Jenis usaha berhasil dihapus.');
            setIsDeleteModalOpen(false);
            setTypeToDelete(null);
        } catch (error) {
            setDeleteError(error.message);
        } finally {
            setIsDeleting(false);
        }
    }

    const keyword = query.trim().toLowerCase();
    const visibleTypes = keyword
        ? types.filter((type) => type.name.toLowerCase().includes(keyword))
        : types;

    const totalTypes = types.length;
    const activeTypes = types.filter((t) => t.is_active).length;
    const inactiveTypes = totalTypes - activeTypes;

    return (
        <div className="space-y-6">
            {/* Notification Banners */}
            {successMessage && (
                <div
                    role="status"
                    className="flex items-center justify-between gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800"
                >
                    <div className="flex items-center gap-2">
                        <HugeiconsIcon icon={CheckmarkCircle02Icon} size={20} className="shrink-0 text-emerald-600" aria-hidden="true" />
                        <span>{successMessage}</span>
                    </div>
                    <button
                        type="button"
                        onClick={() => setSuccessMessage('')}
                        aria-label="Tutup notifikasi"
                        className="rounded-lg p-1 text-emerald-600 hover:bg-emerald-100"
                    >
                        <HugeiconsIcon icon={Cancel01Icon} size={16} aria-hidden="true" />
                    </button>
                </div>
            )}

            {errorMessage && (
                <div
                    role="alert"
                    className="flex items-center justify-between gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-800"
                >
                    <div className="flex items-center gap-2">
                        <HugeiconsIcon icon={Alert02Icon} size={20} className="shrink-0 text-rose-600" aria-hidden="true" />
                        <span>{errorMessage}</span>
                    </div>
                    <button
                        type="button"
                        onClick={() => setErrorMessage('')}
                        aria-label="Tutup pesan kesalahan"
                        className="rounded-lg p-1 text-rose-600 hover:bg-rose-100"
                    >
                        <HugeiconsIcon icon={Cancel01Icon} size={16} aria-hidden="true" />
                    </button>
                </div>
            )}

            {/* Header */}
            <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <a
                        href="/dashboard"
                        className="mb-2 inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 transition-colors hover:text-brand"
                    >
                        <HugeiconsIcon icon={ArrowLeft02Icon} size={16} aria-hidden="true" />
                        Kembali ke Dashboard
                    </a>
                    <h1 className="text-2xl font-bold tracking-tight text-ink sm:text-3xl">Kelola Jenis Usaha</h1>
                    <p className="mt-1 max-w-3xl text-sm text-slate-600">
                        Kelola master data jenis usaha UMKM yang digunakan pada form Profil UMKM dan klasifikasi sektor usaha.
                    </p>
                </div>
                <div className="shrink-0">
                    <Button type="button" onClick={openCreateModal} className="gap-2">
                        <HugeiconsIcon icon={PlusSignIcon} size={18} aria-hidden="true" />
                        Tambah Jenis Usaha
                    </Button>
                </div>
            </div>

            {/* Stat Cards */}
            <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div className="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
                    <div className="flex items-center justify-between">
                        <span className="text-xs font-bold tracking-wider text-slate-400 uppercase">Total Jenis Usaha</span>
                        <span className="grid size-9 place-items-center rounded-xl bg-slate-100 text-slate-600">
                            <HugeiconsIcon icon={Tag01Icon} size={18} aria-hidden="true" />
                        </span>
                    </div>
                    <div className="mt-2 text-2xl font-bold text-ink">{totalTypes}</div>
                    <div className="mt-0.5 text-xs text-slate-500">Seluruh jenis usaha terdaftar</div>
                </div>

                <div className="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
                    <div className="flex items-center justify-between">
                        <span className="text-xs font-bold tracking-wider text-slate-400 uppercase">Jenis Usaha Aktif</span>
                        <span className="grid size-9 place-items-center rounded-xl bg-emerald-50 text-emerald-600">
                            <HugeiconsIcon icon={CheckmarkCircle02Icon} size={18} aria-hidden="true" />
                        </span>
                    </div>
                    <div className="mt-2 text-2xl font-bold text-emerald-700">{activeTypes}</div>
                    <div className="mt-0.5 text-xs text-slate-500">Tampil di formulir profil UMKM</div>
                </div>

                <div className="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
                    <div className="flex items-center justify-between">
                        <span className="text-xs font-bold tracking-wider text-slate-400 uppercase">Jenis Usaha Nonaktif</span>
                        <span className="grid size-9 place-items-center rounded-xl bg-slate-100 text-slate-400">
                            <HugeiconsIcon icon={Cancel01Icon} size={18} aria-hidden="true" />
                        </span>
                    </div>
                    <div className="mt-2 text-2xl font-bold text-slate-600">{inactiveTypes}</div>
                    <div className="mt-0.5 text-xs text-slate-500">Disembunyikan dari pilihan</div>
                </div>
            </div>

            {/* List & Search Container */}
            <Card>
                <CardHeader className="gap-3">
                    <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <CardTitle className="text-base font-bold text-ink">Daftar Jenis Usaha</CardTitle>
                            <p className="mt-0.5 text-xs text-muted-foreground">
                                {isLoading ? 'Memuat data...' : `Menampilkan ${visibleTypes.length} dari ${types.length} data`}
                            </p>
                        </div>
                        <div className="relative w-full sm:w-72">
                            <HugeiconsIcon
                                icon={Search01Icon}
                                size={16}
                                className="pointer-events-none absolute top-1/2 left-3.5 -translate-y-1/2 text-slate-400"
                                aria-hidden="true"
                            />
                            <Input
                                type="search"
                                value={query}
                                onChange={(event) => setQuery(event.target.value)}
                                placeholder="Cari jenis usaha..."
                                className="h-10 pl-9 text-sm"
                                aria-label="Cari jenis usaha"
                            />
                        </div>
                    </div>
                </CardHeader>

                <CardContent className="flex flex-col gap-2">
                    {isLoading && (
                        <div className="py-8 text-center text-sm text-muted-foreground">
                            Memuat data...
                        </div>
                    )}

                    {!isLoading && types.length === 0 && (
                        <div className="py-8 text-center">
                            <span className="mb-2 inline-grid size-12 place-items-center rounded-full bg-slate-100 text-slate-400">
                                <HugeiconsIcon icon={Tag01Icon} size={24} aria-hidden="true" />
                            </span>
                            <p className="text-sm font-semibold text-slate-600">Belum ada jenis usaha</p>
                            <p className="mt-1 text-xs text-muted-foreground">
                                Klik tombol Tambah Jenis Usaha untuk mulai membuat data.
                            </p>
                        </div>
                    )}

                    {!isLoading && types.length > 0 && visibleTypes.length === 0 && (
                        <div className="py-8 text-center">
                            <span className="mb-2 inline-grid size-12 place-items-center rounded-full bg-slate-100 text-slate-400">
                                <HugeiconsIcon icon={Search01Icon} size={24} aria-hidden="true" />
                            </span>
                            <p className="text-sm font-semibold text-slate-600">Tidak ada jenis usaha yang cocok dengan pencarianmu.</p>
                            <p className="mt-1 text-xs text-muted-foreground">
                                Coba cari dengan kata kunci lain atau kosongkan kolom pencarian.
                            </p>
                        </div>
                    )}

                    {!isLoading && visibleTypes.map((type) => (
                        <div
                            key={type.id}
                            className="flex items-center justify-between gap-3 rounded-xl border border-border bg-white px-4 py-3 transition-colors hover:bg-slate-50/50"
                        >
                            <div className="min-w-0">
                                <p className="truncate text-sm font-semibold text-ink">{type.name}</p>
                                <p className="truncate text-xs text-muted-foreground">
                                    {type.slug} &middot; {type.businesses_count ?? 0} UMKM
                                </p>
                            </div>

                            <div className="flex shrink-0 items-center gap-2">
                                <Badge variant={type.is_active ? 'success' : 'outline'}>
                                    {type.is_active ? 'Aktif' : 'Nonaktif'}
                                </Badge>
                                <Button
                                    type="button"
                                    size="sm"
                                    variant="outline"
                                    onClick={() => openEditModal(type)}
                                    aria-label={`Edit ${type.name}`}
                                    title={`Edit ${type.name}`}
                                >
                                    Edit
                                </Button>
                                <Button
                                    type="button"
                                    size="sm"
                                    variant="outline"
                                    onClick={() => handleToggle(type.id)}
                                    disabled={isTogglingId === type.id}
                                    aria-label={type.is_active ? `Nonaktifkan ${type.name}` : `Aktifkan ${type.name}`}
                                >
                                    {type.is_active ? 'Nonaktifkan' : 'Aktifkan'}
                                </Button>
                                <Button
                                    type="button"
                                    size="sm"
                                    variant="outline"
                                    className="border-rose-200 text-rose-600 hover:bg-rose-50 hover:text-rose-700"
                                    onClick={() => openDeleteModal(type)}
                                    aria-label={`Hapus ${type.name}`}
                                    title={`Hapus ${type.name}`}
                                >
                                    Hapus
                                </Button>
                            </div>
                        </div>
                    ))}
                </CardContent>
            </Card>

            {/* Modal Tambah / Edit */}
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
                                <HugeiconsIcon icon={Cancel01Icon} size={20} aria-hidden="true" />
                            </button>
                        </div>

                        {/* Form */}
                        <form
                            onSubmit={handleSubmit}
                            className="flex min-h-0 flex-col overflow-y-auto"
                        >
                            <div className="flex flex-col gap-5 p-6">
                                {formError && (
                                    <div
                                        role="alert"
                                        className="rounded-xl border border-destructive/30 bg-destructive/10 px-4 py-3 text-sm font-medium text-destructive"
                                    >
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

            {/* Modal Konfirmasi Hapus */}
            {isDeleteModalOpen && typeToDelete && (
                <div
                    className="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-slate-900/50 p-4 backdrop-blur-sm sm:p-6"
                    role="dialog"
                    aria-modal="true"
                    aria-labelledby="delete-confirm-modal-title"
                    onMouseDown={(event) => {
                        if (event.target === event.currentTarget) {
                            closeDeleteModal();
                        }
                    }}
                >
                    <div className="my-auto flex max-h-[90vh] w-full max-w-md flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl">
                        {/* Header */}
                        <div className="flex items-center justify-between gap-3 border-b border-slate-200 px-6 py-5">
                            <h2
                                id="delete-confirm-modal-title"
                                className="text-lg font-bold text-ink"
                            >
                                Hapus Jenis Usaha
                            </h2>

                            <button
                                type="button"
                                onClick={closeDeleteModal}
                                disabled={isDeleting}
                                aria-label="Tutup konfirmasi hapus"
                                className="grid size-8 shrink-0 place-items-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <HugeiconsIcon icon={Cancel01Icon} size={20} aria-hidden="true" />
                            </button>
                        </div>

                        {/* Content */}
                        <div className="flex flex-col gap-4 p-6">
                            {deleteError && (
                                <div
                                    role="alert"
                                    className="rounded-xl border border-destructive/30 bg-destructive/10 px-4 py-3 text-sm font-medium text-destructive"
                                >
                                    {deleteError}
                                </div>
                            )}

                            <p className="text-sm leading-relaxed text-slate-600">
                                Apakah Anda yakin ingin menghapus jenis usaha{' '}
                                <strong className="font-bold text-ink">&ldquo;{typeToDelete.name}&rdquo;</strong>?
                                Tindakan ini tidak dapat dibatalkan.
                            </p>

                            {typeToDelete.businesses_count > 0 && (
                                <div className="rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs font-medium text-amber-800">
                                    <div className="flex items-center gap-1.5 font-semibold text-amber-900">
                                        <HugeiconsIcon icon={Alert02Icon} size={15} className="shrink-0" aria-hidden="true" />
                                        Perhatian
                                    </div>
                                    <p className="mt-1">
                                        Jenis usaha ini masih digunakan oleh {typeToDelete.businesses_count} data UMKM.
                                        Sesuai aturan aplikasi, data tidak dapat dihapus sebelum relasinya dilepaskan.
                                    </p>
                                </div>
                            )}
                        </div>

                        {/* Footer */}
                        <div className="flex justify-end gap-3 border-t border-slate-200 bg-slate-50/70 px-6 py-4">
                            <Button
                                type="button"
                                variant="outline"
                                onClick={closeDeleteModal}
                                disabled={isDeleting}
                            >
                                Batal
                            </Button>

                            <Button
                                type="button"
                                variant="destructive"
                                onClick={handleDelete}
                                disabled={isDeleting}
                            >
                                {isDeleting ? 'Menghapus...' : 'Hapus'}
                            </Button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}
