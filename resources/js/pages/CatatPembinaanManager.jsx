import { useState, useMemo } from 'react';
import { HugeiconsIcon } from '@hugeicons/react';
import {
    TeachingIcon,
    File02Icon,
    Calendar01Icon,
    Tag01Icon,
    Cancel01Icon,
    Search01Icon,
    Tick01Icon,
    ViewIcon,
    Store01Icon,
    User02Icon,
    RotateLeft01Icon,
    CheckListIcon,
} from '@hugeicons/core-free-icons';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { cn } from '@/lib/utils';
import { motion, AnimatePresence } from 'motion/react';
import { apiFetch } from '@/lib/api';

function formatDate(dateStr) {
    if (!dateStr) return '-';
    try {
        const d = new Date(dateStr);
        if (isNaN(d.getTime())) return dateStr;
        return d.toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
        });
    } catch {
        return dateStr;
    }
}

export default function CatatPembinaanManager({
    queue: initialQueue = [],
    history: initialHistory = [],
    businesses = [],
}) {
    const [queue, setQueue] = useState(initialQueue || []);
    const [history, setHistory] = useState(initialHistory || []);
    const [activeTab, setActiveTab] = useState('queue'); // 'queue' | 'history'

    // Filters
    const [umkmFilter, setUmkmFilter] = useState('all');
    const [startDate, setStartDate] = useState('');
    const [endDate, setEndDate] = useState('');
    const [searchQuery, setSearchQuery] = useState('');

    // Modals
    const [selectedItem, setSelectedItem] = useState(null); // for recording form
    const [detailItem, setDetailItem] = useState(null); // for viewing history detail
    const [completingItem, setCompletingItem] = useState(null); // for completing a scheduled session
    const [cancellingItem, setCancellingItem] = useState(null); // for cancelling a scheduled session
    const [outcomeInput, setOutcomeInput] = useState('');
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [formData, setFormData] = useState({
        title: '',
        held_on: '',
        description: '',
        outcome: '',
    });
    const [successMessage, setSuccessMessage] = useState('');
    const [errorMessage, setErrorMessage] = useState('');

    // Combine distinct businesses for the dropdown filter
    const umkmOptions = useMemo(() => {
        const map = new Map();
        businesses.forEach((b) => {
            if (b.name) map.set(String(b.id || b.name), { id: b.id, name: b.name });
        });
        queue.forEach((q) => {
            if (q.umkm_name && !map.has(String(q.business_id || q.umkm_name))) {
                map.set(String(q.business_id || q.umkm_name), {
                    id: q.business_id,
                    name: q.umkm_name,
                });
            }
        });
        history.forEach((h) => {
            if (h.umkm_name && !map.has(String(h.business_id || h.umkm_name))) {
                map.set(String(h.business_id || h.umkm_name), {
                    id: h.business_id,
                    name: h.umkm_name,
                });
            }
        });
        return Array.from(map.values()).sort((a, b) => a.name.localeCompare(b.name));
    }, [businesses, queue, history]);

    const hasActiveFilter =
        umkmFilter !== 'all' || startDate !== '' || endDate !== '' || searchQuery.trim() !== '';

    function resetFilters() {
        setUmkmFilter('all');
        setStartDate('');
        setEndDate('');
        setSearchQuery('');
    }

    // Filtered queue items
    const filteredQueue = useMemo(() => {
        return queue.filter((item) => {
            if (umkmFilter !== 'all') {
                const matchId = String(item.business_id) === String(umkmFilter);
                const matchName = item.umkm_name === umkmFilter;
                if (!matchId && !matchName) return false;
            }

            if (startDate && item.planned_on && item.planned_on < startDate) {
                return false;
            }
            if (endDate && item.planned_on && item.planned_on > endDate) {
                return false;
            }

            if (searchQuery.trim()) {
                const q = searchQuery.toLowerCase();
                const match =
                    item.umkm_name?.toLowerCase().includes(q) ||
                    item.owner_name?.toLowerCase().includes(q) ||
                    item.program_name?.toLowerCase().includes(q) ||
                    item.planned_activity?.toLowerCase().includes(q);
                if (!match) return false;
            }

            return true;
        });
    }, [queue, umkmFilter, startDate, endDate, searchQuery]);

    // Filtered history items
    const filteredHistory = useMemo(() => {
        return history.filter((item) => {
            if (umkmFilter !== 'all') {
                const matchId = String(item.business_id) === String(umkmFilter);
                const matchName = item.umkm_name === umkmFilter;
                if (!matchId && !matchName) return false;
            }

            if (startDate && item.held_on && item.held_on < startDate) {
                return false;
            }
            if (endDate && item.held_on && item.held_on > endDate) {
                return false;
            }

            if (searchQuery.trim()) {
                const q = searchQuery.toLowerCase();
                const match =
                    item.umkm_name?.toLowerCase().includes(q) ||
                    item.owner_name?.toLowerCase().includes(q) ||
                    item.program_name?.toLowerCase().includes(q) ||
                    item.title?.toLowerCase().includes(q) ||
                    item.description?.toLowerCase().includes(q) ||
                    item.recorded_by_name?.toLowerCase().includes(q);
                if (!match) return false;
            }

            return true;
        });
    }, [history, umkmFilter, startDate, endDate, searchQuery]);

    // Unique count of businesses assisted
    const uniqueAssistedCount = useMemo(() => {
        const set = new Set();
        history.forEach((h) => {
            if (h.business_id) set.add(h.business_id);
            else if (h.umkm_name) set.add(h.umkm_name);
        });
        return set.size;
    }, [history]);

    function openModal(item) {
        setSelectedItem(item);
        setFormData({
            title: item.planned_activity || '',
            held_on: item.planned_on || new Date().toISOString().split('T')[0],
            description: '',
            outcome: '',
        });
    }

    function closeModal() {
        if (isSubmitting) return;
        setSelectedItem(null);
    }

    function handleInputChange(e) {
        setFormData({ ...formData, [e.target.name]: e.target.value });
    }

    async function handleSubmit(e) {
        e.preventDefault();
        setIsSubmitting(true);
        try {
            const sessionStatus = formData.outcome?.trim() ? 'completed' : 'scheduled';
            const res = await apiFetch('/petugas/catat-pembinaan', {
                method: 'POST',
                body: JSON.stringify({
                    follow_up_id: selectedItem.id,
                    title: formData.title,
                    held_on: formData.held_on,
                    description: formData.description,
                    outcome: formData.outcome,
                    status: sessionStatus,
                }),
            });

            const newSession = res?.session || {
                id: Date.now(),
                follow_up_id: selectedItem.id,
                business_id: selectedItem.business_id,
                umkm_name: selectedItem.umkm_name,
                owner_name: selectedItem.owner_name,
                program_name: selectedItem.program_name,
                title: formData.title,
                held_on: formData.held_on,
                description: formData.description,
                outcome: formData.outcome,
                status: sessionStatus,
                recorded_by_name: 'Petugas Desa',
                created_at: new Date().toISOString(),
            };

            setQueue((prev) => prev.filter((item) => item.id !== selectedItem.id));
            setHistory((prev) => [newSession, ...prev]);
            closeModal();

            setSuccessMessage(
                sessionStatus === 'completed'
                    ? `Pembinaan untuk "${selectedItem.umkm_name}" berhasil dicatat dan diselesaikan.`
                    : `Jadwal pembinaan untuk "${selectedItem.umkm_name}" berhasil disimpan (terjadwal).`
            );
            setErrorMessage('');
            setTimeout(() => setSuccessMessage(''), 6000);
        } catch (error) {
            setErrorMessage(error.message || 'Gagal mencatat data pembinaan.');
            setSuccessMessage('');
        } finally {
            setIsSubmitting(false);
        }
    }

    async function handleCompleteSession(e) {
        e.preventDefault();
        if (!outcomeInput.trim()) {
            setErrorMessage('Hasil (outcome) pembinaan wajib diisi saat menyelesaikan pembinaan.');
            return;
        }
        setIsSubmitting(true);
        try {
            const res = await apiFetch(`/petugas/catat-pembinaan/${completingItem.id}`, {
                method: 'PATCH',
                body: JSON.stringify({
                    status: 'completed',
                    outcome: outcomeInput,
                }),
            });

            const updatedSession = res?.session || {
                ...completingItem,
                status: 'completed',
                outcome: outcomeInput,
            };

            setHistory((prev) =>
                prev.map((item) => (item.id === completingItem.id ? { ...item, ...updatedSession } : item))
            );
            if (detailItem && detailItem.id === completingItem.id) {
                setDetailItem((prev) => ({ ...prev, ...updatedSession }));
            }
            setCompletingItem(null);
            setOutcomeInput('');
            setSuccessMessage(`Pembinaan "${completingItem.title}" berhasil ditandai selesai.`);
            setErrorMessage('');
            setTimeout(() => setSuccessMessage(''), 6000);
        } catch (error) {
            setErrorMessage(error.message || 'Gagal memperbarui status pembinaan.');
        } finally {
            setIsSubmitting(false);
        }
    }

    async function handleCancelSession() {
        setIsSubmitting(true);
        try {
            const res = await apiFetch(`/petugas/catat-pembinaan/${cancellingItem.id}`, {
                method: 'PATCH',
                body: JSON.stringify({
                    status: 'cancelled',
                }),
            });

            const updatedSession = res?.session || {
                ...cancellingItem,
                status: 'cancelled',
            };

            setHistory((prev) =>
                prev.map((item) => (item.id === cancellingItem.id ? { ...item, ...updatedSession } : item))
            );
            if (detailItem && detailItem.id === cancellingItem.id) {
                setDetailItem((prev) => ({ ...prev, ...updatedSession }));
            }
            setCancellingItem(null);
            setSuccessMessage(`Jadwal pembinaan "${cancellingItem.title}" berhasil dibatalkan.`);
            setErrorMessage('');
            setTimeout(() => setSuccessMessage(''), 6000);
        } catch (error) {
            setErrorMessage(error.message || 'Gagal membatalkan jadwal pembinaan.');
        } finally {
            setIsSubmitting(false);
        }
    }

    return (
        <div className="space-y-6">
            {/* Header */}
            <header>
                <div>
                    <h1 className="text-2xl font-bold tracking-tight text-ink sm:text-3xl">
                        Catat &amp; Riwayat Pembinaan
                    </h1>
                    <p className="mt-1 text-sm text-slate-600 max-w-3xl">
                        Kelola pencatatan pembinaan UMKM dari tindak lanjut yang telah disetujui, serta pantau seluruh riwayat pembinaan operasional petugas.
                    </p>
                </div>
            </header>

            {/* Notification Banner */}
            <AnimatePresence>
                {successMessage && (
                    <motion.div
                        initial={{ opacity: 0, y: -10 }}
                        animate={{ opacity: 1, y: 0 }}
                        exit={{ opacity: 0, y: -10 }}
                        className="flex items-center justify-between rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 shadow-xs"
                        role="status"
                    >
                        <div className="flex items-center gap-2.5">
                            <span className="flex size-6 shrink-0 items-center justify-center rounded-full bg-emerald-600 text-white">
                                <HugeiconsIcon icon={Tick01Icon} size={14} strokeWidth={2.5} />
                            </span>
                            <span>{successMessage}</span>
                        </div>
                        <button
                            type="button"
                            onClick={() => setSuccessMessage('')}
                            className="ml-3 shrink-0 rounded-lg p-1 text-emerald-700 hover:bg-emerald-100 transition-colors"
                            aria-label="Tutup notifikasi"
                        >
                            <HugeiconsIcon icon={Cancel01Icon} size={16} />
                        </button>
                    </motion.div>
                )}

                {errorMessage && (
                    <motion.div
                        initial={{ opacity: 0, y: -10 }}
                        animate={{ opacity: 1, y: 0 }}
                        exit={{ opacity: 0, y: -10 }}
                        className="flex items-center justify-between rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800 shadow-xs"
                        role="alert"
                    >
                        <div className="flex items-center gap-2.5">
                            <span className="flex size-6 shrink-0 items-center justify-center rounded-full bg-red-600 text-white">
                                <HugeiconsIcon icon={Cancel01Icon} size={14} strokeWidth={2.5} />
                            </span>
                            <span>{errorMessage}</span>
                        </div>
                        <button
                            type="button"
                            onClick={() => setErrorMessage('')}
                            className="ml-3 shrink-0 rounded-lg p-1 text-red-700 hover:bg-red-100 transition-colors"
                            aria-label="Tutup notifikasi"
                        >
                            <HugeiconsIcon icon={Cancel01Icon} size={16} />
                        </button>
                    </motion.div>
                )}
            </AnimatePresence>

            {/* Stat Cards */}
            <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <Card className="border-border/80 shadow-xs">
                    <CardContent className="flex items-center gap-3 p-4">
                        <span className="flex size-11 items-center justify-center rounded-xl bg-amber-50 text-amber-700">
                            <HugeiconsIcon icon={CheckListIcon} size={22} strokeWidth={1.8} />
                        </span>
                        <div>
                            <p className="text-xs font-medium text-slate-500">Antrean Perlu Dicatat</p>
                            <p className="text-2xl font-bold text-ink">{queue.length}</p>
                        </div>
                    </CardContent>
                </Card>

                <Card className="border-border/80 shadow-xs">
                    <CardContent className="flex items-center gap-3 p-4">
                        <span className="flex size-11 items-center justify-center rounded-xl bg-brand-50 text-primary">
                            <HugeiconsIcon icon={TeachingIcon} size={22} strokeWidth={1.8} />
                        </span>
                        <div>
                            <p className="text-xs font-medium text-slate-500">Total Pembinaan Selesai</p>
                            <p className="text-2xl font-bold text-ink">{history.filter((h) => h.status === 'completed').length}</p>
                        </div>
                    </CardContent>
                </Card>

                <Card className="border-border/80 shadow-xs">
                    <CardContent className="flex items-center gap-3 p-4">
                        <span className="flex size-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                            <HugeiconsIcon icon={Store01Icon} size={22} strokeWidth={1.8} />
                        </span>
                        <div>
                            <p className="text-xs font-medium text-slate-500">UMKM Terbina</p>
                            <p className="text-2xl font-bold text-ink">{uniqueAssistedCount}</p>
                        </div>
                    </CardContent>
                </Card>
            </div>

            {/* Navigation Tabs */}
            <div className="flex border-b border-border">
                <button
                    type="button"
                    onClick={() => setActiveTab('queue')}
                    className={cn(
                        'relative flex items-center gap-2 px-5 py-3 text-sm font-semibold transition-all border-b-2',
                        activeTab === 'queue'
                            ? 'border-primary text-primary'
                            : 'border-transparent text-slate-600 hover:text-ink hover:border-slate-300'
                    )}
                >
                    <HugeiconsIcon icon={CheckListIcon} size={18} />
                    <span>Antrean Perlu Dicatat</span>
                    <span
                        className={cn(
                            'rounded-full px-2 py-0.5 text-xs font-medium',
                            activeTab === 'queue'
                                ? 'bg-primary/10 text-primary'
                                : 'bg-slate-100 text-slate-600'
                        )}
                    >
                        {queue.length}
                    </span>
                </button>

                <button
                    type="button"
                    onClick={() => setActiveTab('history')}
                    className={cn(
                        'relative flex items-center gap-2 px-5 py-3 text-sm font-semibold transition-all border-b-2',
                        activeTab === 'history'
                            ? 'border-primary text-primary'
                            : 'border-transparent text-slate-600 hover:text-ink hover:border-slate-300'
                    )}
                >
                    <HugeiconsIcon icon={TeachingIcon} size={18} />
                    <span>Riwayat Pembinaan</span>
                    <span
                        className={cn(
                            'rounded-full px-2 py-0.5 text-xs font-medium',
                            activeTab === 'history'
                                ? 'bg-primary/10 text-primary'
                                : 'bg-slate-100 text-slate-600'
                        )}
                    >
                        {history.length}
                    </span>
                </button>
            </div>

            {/* Filter Toolbar */}
            <div className="rounded-xl border border-border bg-white p-4 shadow-xs space-y-3">
                <div className="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div className="grid grid-cols-1 gap-2.5 sm:grid-cols-3 sm:gap-3 flex-1 max-w-3xl">
                        {/* Filter UMKM */}
                        <div>
                            <label className="mb-1 block text-xs font-medium text-slate-600">
                                Filter per UMKM
                            </label>
                            <select
                                value={umkmFilter}
                                onChange={(e) => setUmkmFilter(e.target.value)}
                                className="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-ink shadow-xs focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                            >
                                <option value="all">Semua UMKM</option>
                                {umkmOptions.map((opt) => (
                                    <option key={opt.id || opt.name} value={opt.id || opt.name}>
                                        {opt.name}
                                    </option>
                                ))}
                            </select>
                        </div>

                        {/* Filter Tanggal Mulai */}
                        <div>
                            <label className="mb-1 block text-xs font-medium text-slate-600">
                                Dari Tanggal
                            </label>
                            <input
                                type="date"
                                value={startDate}
                                onChange={(e) => setStartDate(e.target.value)}
                                className="w-full rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-ink shadow-xs focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                            />
                        </div>

                        {/* Filter Tanggal Akhir */}
                        <div>
                            <label className="mb-1 block text-xs font-medium text-slate-600">
                                Sampai Tanggal
                            </label>
                            <input
                                type="date"
                                value={endDate}
                                onChange={(e) => setEndDate(e.target.value)}
                                className="w-full rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-ink shadow-xs focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                            />
                        </div>
                    </div>

                    {/* Search & Reset */}
                    <div className="flex items-end gap-2">
                        <div className="relative flex-1 sm:w-64">
                            <span className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <HugeiconsIcon icon={Search01Icon} size={16} />
                            </span>
                            <Input
                                type="text"
                                value={searchQuery}
                                onChange={(e) => setSearchQuery(e.target.value)}
                                placeholder="Cari UMKM, program..."
                                className="pl-9 text-xs"
                            />
                        </div>

                        {hasActiveFilter && (
                            <Button
                                type="button"
                                variant="outline"
                                size="sm"
                                onClick={resetFilters}
                                className="shrink-0 text-xs gap-1.5 text-slate-600 hover:text-ink"
                            >
                                <HugeiconsIcon icon={RotateLeft01Icon} size={14} />
                                Reset
                            </Button>
                        )}
                    </div>
                </div>
            </div>

            {/* TAB CONTENT 1: Antrean Perlu Dicatat */}
            {activeTab === 'queue' && (
                <Card>
                    <CardHeader className="flex-row items-center gap-3 space-y-0 border-b border-border/60 pb-4">
                        <span className="flex size-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-700">
                            <HugeiconsIcon icon={CheckListIcon} size={20} strokeWidth={1.8} aria-hidden="true" />
                        </span>
                        <div>
                            <CardTitle>Daftar Tindak Lanjut Disetujui</CardTitle>
                            <p className="text-xs text-muted-foreground mt-0.5">
                                Menampilkan {filteredQueue.length} data tindak lanjut yang siap dicatat pembinaannya.
                            </p>
                        </div>
                    </CardHeader>
                    <CardContent className="pt-4">
                        <div className="overflow-x-auto">
                            <table className="w-full text-left text-sm text-ink">
                                <thead className="bg-slate-50 text-xs text-muted-foreground uppercase">
                                    <tr>
                                        <th className="px-4 py-3 font-semibold rounded-tl-xl">UMKM &amp; Pemilik</th>
                                        <th className="px-4 py-3 font-semibold">Program Disetujui</th>
                                        <th className="px-4 py-3 font-semibold">Rencana Kegiatan</th>
                                        <th className="px-4 py-3 font-semibold rounded-tr-xl">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-border">
                                    {filteredQueue.map((item) => (
                                        <tr key={item.id} className="hover:bg-slate-50/50 transition-colors">
                                            <td className="px-4 py-3">
                                                <p className="font-semibold text-ink">{item.umkm_name}</p>
                                                <p className="text-xs text-muted-foreground flex items-center gap-1 mt-0.5">
                                                    <HugeiconsIcon icon={User02Icon} size={12} />
                                                    {item.owner_name}
                                                </p>
                                            </td>
                                            <td className="px-4 py-3">
                                                <span className="inline-flex items-center gap-1 rounded-md bg-slate-100 px-2 py-1 text-xs font-medium text-slate-700">
                                                    <HugeiconsIcon icon={Tag01Icon} size={14} />
                                                    {item.program_name}
                                                </span>
                                            </td>
                                            <td className="px-4 py-3">
                                                <p className="font-medium text-sm text-ink">{item.planned_activity}</p>
                                                <p className="text-xs text-muted-foreground flex items-center gap-1 mt-0.5">
                                                    <HugeiconsIcon icon={Calendar01Icon} size={12} />
                                                    {formatDate(item.planned_on)}
                                                </p>
                                                {item.decision_note && (
                                                    <p className="text-[11px] text-emerald-700 bg-emerald-50 rounded px-1.5 py-0.5 mt-1 inline-block">
                                                        Catatan Kades: "{item.decision_note}"
                                                    </p>
                                                )}
                                            </td>
                                            <td className="px-4 py-3">
                                                <Button size="sm" onClick={() => openModal(item)}>
                                                    Catat Pembinaan
                                                </Button>
                                            </td>
                                        </tr>
                                    ))}
                                    {filteredQueue.length === 0 && (
                                        <tr>
                                            <td colSpan="4" className="px-4 py-12 text-center text-muted-foreground">
                                                <div className="mx-auto flex size-12 items-center justify-center rounded-full bg-slate-100 text-slate-400 mb-2">
                                                    <HugeiconsIcon icon={TeachingIcon} size={24} />
                                                </div>
                                                <p className="font-semibold text-slate-700">
                                                    {hasActiveFilter
                                                        ? 'Tidak ada data yang sesuai filter.'
                                                        : 'Tidak ada data tindak lanjut yang menunggu dicatat.'}
                                                </p>
                                                <p className="text-xs text-slate-500 mt-1 max-w-md mx-auto">
                                                    {hasActiveFilter
                                                        ? 'Coba sesuaikan pilihan filter UMKM atau rentang tanggal.'
                                                        : 'Semua tindak lanjut yang disetujui telah selesai dicatat pembinaannya.'}
                                                </p>
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </CardContent>
                </Card>
            )}

            {/* TAB CONTENT 2: Riwayat Pembinaan */}
            {activeTab === 'history' && (
                <Card>
                    <CardHeader className="flex-row items-center gap-3 space-y-0 border-b border-border/60 pb-4">
                        <span className="flex size-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-primary">
                            <HugeiconsIcon icon={TeachingIcon} size={20} strokeWidth={1.8} aria-hidden="true" />
                        </span>
                        <div>
                            <CardTitle>Riwayat Seluruh Pembinaan UMKM</CardTitle>
                            <p className="text-xs text-muted-foreground mt-0.5">
                                Menampilkan {filteredHistory.length} pembinaan yang telah berhasil dilaksanakan dan dicatat.
                            </p>
                        </div>
                    </CardHeader>
                    <CardContent className="pt-4">
                        <div className="overflow-x-auto">
                            <table className="w-full text-left text-sm text-ink">
                                <thead className="bg-slate-50 text-xs text-muted-foreground uppercase">
                                    <tr>
                                        <th className="px-4 py-3 font-semibold rounded-tl-xl">UMKM &amp; Pemilik</th>
                                        <th className="px-4 py-3 font-semibold">Kegiatan &amp; Program</th>
                                        <th className="px-4 py-3 font-semibold">Tanggal Pelaksanaan</th>
                                        <th className="px-4 py-3 font-semibold">Dicatat Oleh</th>
                                        <th className="px-4 py-3 font-semibold">Status</th>
                                        <th className="px-4 py-3 font-semibold rounded-tr-xl">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-border">
                                    {filteredHistory.map((item) => (
                                        <tr key={item.id} className="hover:bg-slate-50/50 transition-colors">
                                            <td className="px-4 py-3">
                                                <p className="font-semibold text-ink">{item.umkm_name}</p>
                                                <p className="text-xs text-muted-foreground flex items-center gap-1 mt-0.5">
                                                    <HugeiconsIcon icon={User02Icon} size={12} />
                                                    {item.owner_name}
                                                </p>
                                            </td>
                                            <td className="px-4 py-3 max-w-xs">
                                                <p className="font-semibold text-sm text-ink line-clamp-1">{item.title}</p>
                                                <span className="inline-flex items-center gap-1 rounded bg-slate-100 px-1.5 py-0.5 text-[11px] font-medium text-slate-600 mt-1">
                                                    <HugeiconsIcon icon={Tag01Icon} size={12} />
                                                    {item.program_name}
                                                </span>
                                            </td>
                                            <td className="px-4 py-3">
                                                <p className="font-medium text-xs text-ink flex items-center gap-1.5">
                                                    <HugeiconsIcon icon={Calendar01Icon} size={14} className="text-slate-500" />
                                                    {formatDate(item.held_on)}
                                                </p>
                                            </td>
                                            <td className="px-4 py-3">
                                                <p className="text-xs text-slate-700 font-medium">
                                                    {item.recorded_by_name || 'Petugas Desa'}
                                                </p>
                                            </td>
                                            <td className="px-4 py-3">
                                                {item.status === 'scheduled' ? (
                                                    <span className="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-[11px] font-semibold text-amber-800">
                                                        <HugeiconsIcon icon={Calendar01Icon} size={12} strokeWidth={2.5} />
                                                        Terjadwal
                                                    </span>
                                                ) : item.status === 'cancelled' ? (
                                                    <span className="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-700">
                                                        <HugeiconsIcon icon={Cancel01Icon} size={12} strokeWidth={2.5} />
                                                        Dibatalkan
                                                    </span>
                                                ) : (
                                                    <span className="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] font-semibold text-emerald-800">
                                                        <HugeiconsIcon icon={Tick01Icon} size={12} strokeWidth={2.5} />
                                                        Selesai
                                                    </span>
                                                )}
                                            </td>
                                            <td className="px-4 py-3">
                                                <div className="flex flex-wrap items-center gap-1.5">
                                                    {item.status === 'scheduled' && (
                                                        <>
                                                            <Button
                                                                size="sm"
                                                                onClick={() => {
                                                                    setCompletingItem(item);
                                                                    setOutcomeInput(item.outcome || '');
                                                                }}
                                                                className="h-7 px-2.5 text-xs bg-emerald-600 hover:bg-emerald-700 text-white gap-1 whitespace-nowrap"
                                                            >
                                                                <HugeiconsIcon icon={Tick01Icon} size={12} strokeWidth={2.5} />
                                                                Selesaikan
                                                            </Button>
                                                            <Button
                                                                variant="outline"
                                                                size="sm"
                                                                onClick={() => setCancellingItem(item)}
                                                                className="h-7 px-2 text-xs border-red-200 text-red-600 hover:bg-red-50 gap-1 whitespace-nowrap"
                                                            >
                                                                <HugeiconsIcon icon={Cancel01Icon} size={12} />
                                                                Batalkan
                                                            </Button>
                                                        </>
                                                    )}
                                                    <Button
                                                        variant="outline"
                                                        size="sm"
                                                        onClick={() => setDetailItem(item)}
                                                        className="h-7 px-2.5 gap-1 text-xs whitespace-nowrap"
                                                    >
                                                        <HugeiconsIcon icon={ViewIcon} size={13} />
                                                        Detail
                                                    </Button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                    {filteredHistory.length === 0 && (
                                        <tr>
                                            <td colSpan="6" className="px-4 py-12 text-center text-muted-foreground">
                                                <div className="mx-auto flex size-12 items-center justify-center rounded-full bg-slate-100 text-slate-400 mb-2">
                                                    <HugeiconsIcon icon={TeachingIcon} size={24} />
                                                </div>
                                                <p className="font-semibold text-slate-700">
                                                    {hasActiveFilter
                                                        ? 'Tidak ada riwayat pembinaan yang sesuai filter.'
                                                        : 'Belum ada riwayat pembinaan yang tercatat.'}
                                                </p>
                                                <p className="text-xs text-slate-500 mt-1 max-w-md mx-auto">
                                                    {hasActiveFilter
                                                        ? 'Coba atur ulang filter UMKM atau tanggal pencarian.'
                                                        : 'Setelah mencatat pembinaan pada tab antrean, rekaman pembinaan akan muncul di sini.'}
                                                </p>
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </CardContent>
                </Card>
            )}

            {/* Modal Form: Catat Pembinaan */}
            <AnimatePresence>
                {selectedItem && (
                    <div className="fixed inset-0 z-50 flex flex-col justify-end lg:items-center lg:justify-center">
                        <motion.button
                            type="button"
                            aria-label="Tutup modal"
                            className="fixed inset-0 bg-ink/50 backdrop-blur-sm"
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            exit={{ opacity: 0 }}
                            onClick={closeModal}
                        />
                        <motion.div
                            className="relative z-10 w-full rounded-t-2xl bg-white shadow-2xl lg:max-w-xl lg:rounded-2xl max-h-[90vh] overflow-y-auto"
                            initial={{ y: '100%' }}
                            animate={{ y: 0 }}
                            exit={{ y: '100%' }}
                            transition={{ type: 'spring', stiffness: 400, damping: 40 }}
                        >
                            <div className="flex items-center justify-between border-b border-border px-5 py-4 sticky top-0 bg-white z-10">
                                <div>
                                    <h3 className="text-lg font-bold text-ink">Catat Pembinaan</h3>
                                    <p className="text-xs text-muted-foreground">UMKM: {selectedItem.umkm_name}</p>
                                </div>
                                <button
                                    type="button"
                                    onClick={closeModal}
                                    className="grid size-8 place-items-center rounded-lg text-slate-500 hover:bg-slate-100"
                                >
                                    <HugeiconsIcon icon={Cancel01Icon} size={20} />
                                </button>
                            </div>
                            <div className="px-5 py-5">
                                <form onSubmit={handleSubmit} className="space-y-4">
                                    <div className="space-y-2">
                                        <label className="text-sm font-semibold text-ink flex items-center gap-1.5">
                                            <HugeiconsIcon icon={File02Icon} size={16} /> Nama Kegiatan
                                        </label>
                                        <Input
                                            name="title"
                                            value={formData.title}
                                            onChange={handleInputChange}
                                            placeholder="Contoh: Mengikuti pelatihan digital marketing..."
                                            required
                                        />
                                    </div>
                                    <div className="space-y-2">
                                        <label className="text-sm font-semibold text-ink flex items-center gap-1.5">
                                            <HugeiconsIcon icon={Calendar01Icon} size={16} /> Tanggal
                                        </label>
                                        <Input
                                            type="date"
                                            name="held_on"
                                            value={formData.held_on}
                                            onChange={handleInputChange}
                                            required
                                        />
                                    </div>
                                    <div className="space-y-2">
                                        <label className="text-sm font-semibold text-ink flex items-center gap-1.5">
                                            <HugeiconsIcon icon={File02Icon} size={16} /> Deskripsi
                                        </label>
                                        <Textarea
                                            name="description"
                                            value={formData.description}
                                            onChange={handleInputChange}
                                            placeholder="Deskripsikan proses pembinaan yang telah dilakukan..."
                                            rows={3}
                                            required
                                        />
                                    </div>
                                    <div className="space-y-2">
                                        <label className="text-sm font-semibold text-ink flex items-center gap-1.5">
                                            Hasil (Outcome)
                                        </label>
                                        <Textarea
                                            name="outcome"
                                            value={formData.outcome}
                                            onChange={handleInputChange}
                                            placeholder="Deskripsikan hasil yang dicapai (opsional)..."
                                            rows={3}
                                        />
                                    </div>
                                    <div className="pt-4 sticky bottom-0 bg-white pb-2">
                                        <Button type="submit" className="w-full" disabled={isSubmitting}>
                                            {isSubmitting ? 'Menyimpan...' : 'Simpan Data Pembinaan'}
                                        </Button>
                                    </div>
                                </form>
                            </div>
                        </motion.div>
                    </div>
                )}
            </AnimatePresence>

            {/* Modal Detail: Riwayat Pembinaan */}
            <AnimatePresence>
                {detailItem && (
                    <div className="fixed inset-0 z-50 flex flex-col justify-end lg:items-center lg:justify-center">
                        <motion.button
                            type="button"
                            aria-label="Tutup modal detail"
                            className="fixed inset-0 bg-ink/50 backdrop-blur-sm"
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            exit={{ opacity: 0 }}
                            onClick={() => setDetailItem(null)}
                        />
                        <motion.div
                            className="relative z-10 w-full rounded-t-2xl bg-white shadow-2xl lg:max-w-xl lg:rounded-2xl max-h-[90vh] overflow-y-auto"
                            initial={{ y: '100%' }}
                            animate={{ y: 0 }}
                            exit={{ y: '100%' }}
                            transition={{ type: 'spring', stiffness: 400, damping: 40 }}
                        >
                            <div className="flex items-center justify-between border-b border-border px-5 py-4 sticky top-0 bg-white z-10">
                                <div>
                                    <h3 className="text-lg font-bold text-ink">Detail Riwayat Pembinaan</h3>
                                    <p className="text-xs text-muted-foreground">Informasi lengkap kegiatan pembinaan yang dicatat</p>
                                </div>
                                <button
                                    type="button"
                                    onClick={() => setDetailItem(null)}
                                    className="grid size-8 place-items-center rounded-lg text-slate-500 hover:bg-slate-100"
                                >
                                    <HugeiconsIcon icon={Cancel01Icon} size={20} />
                                </button>
                            </div>

                            <div className="px-5 py-5 space-y-4">
                                {/* Profil Usaha & Program */}
                                <div className="grid grid-cols-1 gap-3 sm:grid-cols-2 rounded-xl bg-slate-50 p-4 border border-slate-100">
                                    <div>
                                        <p className="text-xs font-medium text-slate-500">Nama UMKM</p>
                                        <p className="text-sm font-bold text-ink">{detailItem.umkm_name}</p>
                                        <p className="text-xs text-slate-600 mt-0.5">Pemilik: {detailItem.owner_name}</p>
                                    </div>
                                    <div>
                                        <p className="text-xs font-medium text-slate-500">Program Terkait</p>
                                        <span className="inline-flex items-center gap-1 rounded bg-white px-2 py-1 text-xs font-semibold text-slate-700 border border-slate-200 mt-0.5">
                                            <HugeiconsIcon icon={Tag01Icon} size={14} />
                                            {detailItem.program_name}
                                        </span>
                                    </div>
                                </div>

                                {/* Detail Pelaksanaan */}
                                <div className="space-y-3">
                                    <div>
                                        <p className="text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                            Nama Kegiatan Pembinaan
                                        </p>
                                        <p className="text-base font-bold text-ink mt-0.5">{detailItem.title}</p>
                                    </div>

                                    <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                        <div>
                                            <p className="text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                                Tanggal Pelaksanaan
                                            </p>
                                            <p className="text-sm font-medium text-ink flex items-center gap-1.5 mt-0.5">
                                                <HugeiconsIcon icon={Calendar01Icon} size={16} className="text-slate-500" />
                                                {formatDate(detailItem.held_on)}
                                            </p>
                                        </div>
                                        <div>
                                            <p className="text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                                Dicatat Oleh
                                            </p>
                                            <p className="text-sm font-medium text-ink flex items-center gap-1.5 mt-0.5">
                                                <HugeiconsIcon icon={User02Icon} size={16} className="text-slate-500" />
                                                {detailItem.recorded_by_name || 'Petugas Desa'}
                                            </p>
                                        </div>
                                    </div>

                                    <div>
                                        <p className="text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                            Deskripsi Pembinaan
                                        </p>
                                        <div className="mt-1 rounded-lg border border-slate-200 bg-slate-50/50 p-3 text-sm text-slate-700 leading-relaxed whitespace-pre-line">
                                            {detailItem.description || '-'}
                                        </div>
                                    </div>

                                    <div>
                                        <p className="text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                            Hasil (Outcome)
                                        </p>
                                        <div className="mt-1 rounded-lg border border-slate-200 bg-slate-50/50 p-3 text-sm text-slate-700 leading-relaxed whitespace-pre-line">
                                            {detailItem.outcome || 'Tidak ada catatan hasil tambahan.'}
                                        </div>
                                    </div>

                                    <div className="flex items-center justify-between pt-2">
                                        <span className="text-xs text-slate-500">
                                            Status Pelaksanaan:
                                        </span>
                                        {detailItem.status === 'scheduled' ? (
                                            <span className="inline-flex items-center gap-1 rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">
                                                <HugeiconsIcon icon={Calendar01Icon} size={14} strokeWidth={2.5} />
                                                Terjadwal (Belum Selesai)
                                            </span>
                                        ) : detailItem.status === 'cancelled' ? (
                                            <span className="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                                <HugeiconsIcon icon={Cancel01Icon} size={14} strokeWidth={2.5} />
                                                Dibatalkan
                                            </span>
                                        ) : (
                                            <span className="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800">
                                                <HugeiconsIcon icon={Tick01Icon} size={14} strokeWidth={2.5} />
                                                Selesai Dilaksanakan
                                            </span>
                                        )}
                                    </div>
                                </div>

                                <div className="pt-3 border-t border-border flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-end">
                                    {detailItem.status === 'scheduled' && (
                                        <>
                                            <Button
                                                type="button"
                                                variant="outline"
                                                onClick={() => {
                                                    const itemToCancel = detailItem;
                                                    setDetailItem(null);
                                                    setCancellingItem(itemToCancel);
                                                }}
                                                className="border-red-200 text-red-600 hover:bg-red-50 text-xs"
                                            >
                                                Batalkan Jadwal
                                            </Button>
                                            <Button
                                                type="button"
                                                onClick={() => {
                                                    const itemToComplete = detailItem;
                                                    setDetailItem(null);
                                                    setCompletingItem(itemToComplete);
                                                    setOutcomeInput(itemToComplete.outcome || '');
                                                }}
                                                className="bg-emerald-600 hover:bg-emerald-700 text-white text-xs gap-1"
                                            >
                                                <HugeiconsIcon icon={Tick01Icon} size={14} strokeWidth={2.5} />
                                                Selesaikan Pembinaan
                                            </Button>
                                        </>
                                    )}
                                    <Button
                                        type="button"
                                        variant="outline"
                                        onClick={() => setDetailItem(null)}
                                        className={detailItem.status === 'scheduled' ? 'text-xs' : 'w-full'}
                                    >
                                        Tutup
                                    </Button>
                                </div>
                            </div>
                        </motion.div>
                    </div>
                )}
            </AnimatePresence>

            {/* Modal: Selesaikan Pembinaan */}
            <AnimatePresence>
                {completingItem && (
                    <div className="fixed inset-0 z-50 flex flex-col justify-end lg:items-center lg:justify-center">
                        <motion.button
                            type="button"
                            aria-label="Tutup modal"
                            className="fixed inset-0 bg-ink/50 backdrop-blur-sm"
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            exit={{ opacity: 0 }}
                            onClick={() => !isSubmitting && setCompletingItem(null)}
                        />
                        <motion.div
                            className="relative z-10 w-full rounded-t-2xl bg-white shadow-2xl lg:max-w-md lg:rounded-2xl overflow-hidden"
                            initial={{ y: '100%' }}
                            animate={{ y: 0 }}
                            exit={{ y: '100%' }}
                            transition={{ type: 'spring', stiffness: 400, damping: 40 }}
                        >
                            <div className="flex items-center justify-between border-b border-border px-5 py-4">
                                <div>
                                    <h3 className="text-base font-bold text-ink">Selesaikan Pembinaan</h3>
                                    <p className="text-xs text-muted-foreground">{completingItem.umkm_name} • {completingItem.title}</p>
                                </div>
                                <button
                                    type="button"
                                    onClick={() => !isSubmitting && setCompletingItem(null)}
                                    className="grid size-8 place-items-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600"
                                >
                                    <HugeiconsIcon icon={Cancel01Icon} size={20} />
                                </button>
                            </div>
                            <form onSubmit={handleCompleteSession} className="px-5 py-4 space-y-4">
                                <div className="space-y-1.5">
                                    <label className="text-xs font-semibold text-ink">
                                        Hasil Kegiatan Pembinaan (Outcome) <span className="text-red-500">*</span>
                                    </label>
                                    <Textarea
                                        value={outcomeInput}
                                        onChange={(e) => setOutcomeInput(e.target.value)}
                                        placeholder="Deskripsikan hasil atau dampak yang dicapai dari pembinaan ini..."
                                        rows={4}
                                        required
                                        className="text-xs"
                                    />
                                    <p className="text-[11px] text-muted-foreground">
                                        Hasil ini akan tampil pada riwayat usaha pemilik UMKM dan pembinaan akan berstatus selesai.
                                    </p>
                                </div>
                                <div className="flex items-center justify-end gap-2 pt-2 border-t border-border">
                                    <Button
                                        type="button"
                                        variant="outline"
                                        size="sm"
                                        onClick={() => setCompletingItem(null)}
                                        disabled={isSubmitting}
                                    >
                                        Batal
                                    </Button>
                                    <Button
                                        type="submit"
                                        size="sm"
                                        disabled={isSubmitting || !outcomeInput.trim()}
                                        className="bg-emerald-600 hover:bg-emerald-700 text-white gap-1"
                                    >
                                        <HugeiconsIcon icon={Tick01Icon} size={14} strokeWidth={2.5} />
                                        {isSubmitting ? 'Menyimpan...' : 'Tandai Selesai'}
                                    </Button>
                                </div>
                            </form>
                        </motion.div>
                    </div>
                )}
            </AnimatePresence>

            {/* Modal: Batalkan Jadwal Pembinaan */}
            <AnimatePresence>
                {cancellingItem && (
                    <div className="fixed inset-0 z-50 flex flex-col justify-end lg:items-center lg:justify-center">
                        <motion.button
                            type="button"
                            aria-label="Tutup modal"
                            className="fixed inset-0 bg-ink/50 backdrop-blur-sm"
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            exit={{ opacity: 0 }}
                            onClick={() => !isSubmitting && setCancellingItem(null)}
                        />
                        <motion.div
                            className="relative z-10 w-full rounded-t-2xl bg-white shadow-2xl lg:max-w-md lg:rounded-2xl overflow-hidden"
                            initial={{ y: '100%' }}
                            animate={{ y: 0 }}
                            exit={{ y: '100%' }}
                            transition={{ type: 'spring', stiffness: 400, damping: 40 }}
                        >
                            <div className="flex items-center justify-between border-b border-border px-5 py-4 bg-red-50/50">
                                <div className="flex items-center gap-2">
                                    <span className="flex size-7 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-600">
                                        <HugeiconsIcon icon={Cancel01Icon} size={16} strokeWidth={2.5} />
                                    </span>
                                    <div>
                                        <h3 className="text-base font-bold text-red-900">Batalkan Jadwal Pembinaan?</h3>
                                        <p className="text-xs text-red-700">UMKM: {cancellingItem.umkm_name}</p>
                                    </div>
                                </div>
                                <button
                                    type="button"
                                    onClick={() => !isSubmitting && setCancellingItem(null)}
                                    className="grid size-8 place-items-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600"
                                >
                                    <HugeiconsIcon icon={Cancel01Icon} size={20} />
                                </button>
                            </div>
                            <div className="px-5 py-4 space-y-3">
                                <p className="text-xs text-slate-600 leading-relaxed">
                                    Apakah Anda yakin ingin membatalkan jadwal pembinaan <strong>"{cancellingItem.title}"</strong>?
                                </p>
                                <p className="text-[11px] text-slate-500 bg-slate-50 p-2.5 rounded-lg border border-slate-200">
                                    Sesi ini akan tetap tersimpan di riwayat dengan penanda <strong>Dibatalkan</strong>, dan tidak akan dihitung sebagai pembinaan selesai.
                                </p>
                                <div className="flex items-center justify-end gap-2 pt-2 border-t border-border">
                                    <Button
                                        type="button"
                                        variant="outline"
                                        size="sm"
                                        onClick={() => setCancellingItem(null)}
                                        disabled={isSubmitting}
                                    >
                                        Kembali
                                    </Button>
                                    <Button
                                        type="button"
                                        size="sm"
                                        onClick={handleCancelSession}
                                        disabled={isSubmitting}
                                        className="bg-red-600 hover:bg-red-700 text-white"
                                    >
                                        {isSubmitting ? 'Membatalkan...' : 'Ya, Batalkan Jadwal'}
                                    </Button>
                                </div>
                            </div>
                        </motion.div>
                    </div>
                )}
            </AnimatePresence>
        </div>
    );
}
