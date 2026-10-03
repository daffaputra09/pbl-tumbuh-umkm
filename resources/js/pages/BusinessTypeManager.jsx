import { useEffect, useState } from 'react';
import { HugeiconsIcon } from '@hugeicons/react';
import { Store04Icon } from '@hugeicons/core-free-icons';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { apiFetch } from '@/lib/api';
import { cn } from '@/lib/utils';

export default function BusinessTypeManager() {
    const [types, setTypes] = useState([]);
    const [isLoading, setIsLoading] = useState(true);
    const [errorMessage, setErrorMessage] = useState('');

    const [newName, setNewName] = useState('');
    const [isSubmitting, setIsSubmitting] = useState(false);

    const [editingId, setEditingId] = useState(null);
    const [editingName, setEditingName] = useState('');

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

    async function handleAdd(event) {
        event.preventDefault();

        if (!newName.trim()) {
            return;
        }

        setIsSubmitting(true);
        setErrorMessage('');

        try {
            const created = await apiFetch('/api/business-types', {
                method: 'POST',
                body: JSON.stringify({ name: newName.trim() }),
            });
            setTypes((prev) => [...prev, created].sort((a, b) => a.name.localeCompare(b.name)));
            setNewName('');
        } catch (error) {
            setErrorMessage(error.message);
        } finally {
            setIsSubmitting(false);
        }
    }

    function startEdit(type) {
        setEditingId(type.id);
        setEditingName(type.name);
    }

    function cancelEdit() {
        setEditingId(null);
        setEditingName('');
    }

    async function handleSaveEdit(id) {
        if (!editingName.trim()) {
            return;
        }

        setErrorMessage('');

        try {
            const updated = await apiFetch(`/api/business-types/${id}`, {
                method: 'PUT',
                body: JSON.stringify({ name: editingName.trim() }),
            });
            setTypes((prev) => prev.map((type) => (type.id === id ? updated : type)));
            cancelEdit();
        } catch (error) {
            setErrorMessage(error.message);
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

    return (
        <div className="min-h-screen bg-background py-10 sm:py-14">
            <div className="mx-auto max-w-2xl px-5">
                <header className="mb-8">
                    <p className="text-sm font-semibold text-primary">Panel Petugas</p>
                    <h1 className="mt-1 text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Kelola Jenis Usaha</h1>
                    <p className="mt-2 text-sm leading-relaxed text-muted-foreground">
                        Daftar jenis usaha yang muncul di dropdown kategori produk pada form Profil UMKM.
                    </p>
                </header>

                {errorMessage && (
                    <div className="mb-6 rounded-xl border border-destructive/30 bg-destructive/10 px-4 py-3 text-sm font-medium text-destructive">
                        {errorMessage}
                    </div>
                )}

                <Card className="mb-6">
                    <CardHeader className="flex-row items-center gap-3 space-y-0">
                        <span className="flex size-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-primary">
                            <HugeiconsIcon icon={Store04Icon} size={20} strokeWidth={1.8} aria-hidden="true" />
                        </span>
                        <CardTitle>Tambah Jenis Usaha</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={handleAdd} className="flex flex-col gap-3 sm:flex-row">
                            <Input
                                value={newName}
                                onChange={(event) => setNewName(event.target.value)}
                                placeholder="Contoh: Perikanan"
                                className="flex-1"
                            />
                            <Button type="submit" disabled={isSubmitting}>
                                {isSubmitting ? 'Menyimpan...' : 'Tambah'}
                            </Button>
                        </form>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle className="text-base">Daftar Jenis Usaha</CardTitle>
                    </CardHeader>
                    <CardContent className="flex flex-col gap-2">
                        {isLoading && <p className="text-sm text-muted-foreground">Memuat data...</p>}

                        {!isLoading && types.length === 0 && (
                            <p className="text-sm text-muted-foreground">Belum ada jenis usaha, tambahkan lewat form di atas.</p>
                        )}

                        {types.map((type) => (
                            <div
                                key={type.id}
                                className="flex items-center justify-between gap-3 rounded-xl border border-border bg-white px-4 py-3"
                            >
                                {editingId === type.id ? (
                                    <Input
                                        value={editingName}
                                        onChange={(event) => setEditingName(event.target.value)}
                                        className="h-9 flex-1"
                                        autoFocus
                                    />
                                ) : (
                                    <div>
                                        <p className="text-sm font-semibold text-ink">{type.name}</p>
                                        <p className="text-xs text-muted-foreground">{type.slug}</p>
                                    </div>
                                )}

                                <div className="flex shrink-0 items-center gap-2">
                                    <span
                                        className={cn(
                                            'rounded-full px-2.5 py-0.5 text-[11px] font-semibold',
                                            type.is_active ? 'bg-success/10 text-success' : 'bg-muted text-muted-foreground',
                                        )}
                                    >
                                        {type.is_active ? 'Aktif' : 'Nonaktif'}
                                    </span>

                                    {editingId === type.id ? (
                                        <>
                                            <Button type="button" size="sm" onClick={() => handleSaveEdit(type.id)}>
                                                Simpan
                                            </Button>
                                            <Button type="button" size="sm" variant="outline" onClick={cancelEdit}>
                                                Batal
                                            </Button>
                                        </>
                                    ) : (
                                        <>
                                            <Button type="button" size="sm" variant="outline" onClick={() => startEdit(type)}>
                                                Edit
                                            </Button>
                                            <Button type="button" size="sm" variant="outline" onClick={() => handleToggle(type.id)}>
                                                {type.is_active ? 'Nonaktifkan' : 'Aktifkan'}
                                            </Button>
                                        </>
                                    )}
                                </div>
                            </div>
                        ))}
                    </CardContent>
                </Card>
            </div>
        </div>
    );
}
