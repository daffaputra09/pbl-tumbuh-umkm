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
}) {
    const [queue, setQueue] = useState(initialQueue || []);
    const [history, setHistory] = useState(initialHistory || []);
    const [activeTab, setActiveTab] = useState('queue'); // 'queue' | 'history'

    // Filters
    const [startDate, setStartDate] = useState('');
    const [endDate, setEndDate] = useState('');
    const [searchQuery, setSearchQuery] = useState('');

    // Modals
    const [selectedItem, setSelectedItem] = useState(null); // for recording form
    const [detailItem, setDetailItem] = useState(null); // for viewing history detail
    const [updateItem, setUpdateItem] = useState(null); // for updating status/outcome
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [isUpdating, setIsUpdating] = useState(false);
    const [formData, setFormData] = useState({
        title: '',
        held_on: '',
        description: '',
        outcome: '',
    });
    const [updateFormData, setUpdateFormData] = useState({
        status: 'completed',
        title: '',
        held_on: '',
        description: '',
        outcome: '',
    });
    const [successMessage, setSuccessMessage] = useState('');
    const [errorMessage, setErrorMessage] = useState('');

    const hasActiveFilter = startDate !== '' || endDate !== '' || searchQuery.trim() !== '';

    function resetFilters() {
        setStartDate('');
        setEndDate('');
        setSearchQuery('');
    }

    // Filtered queue items
    const filteredQueue = useMemo(() => {
        return queue.filter((item) => {
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
    }, [queue, startDate, endDate, searchQuery]);

    // Filtered history items
    const filteredHistory = useMemo(() => {
        return history.filter((item) => {
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
    }, [history, startDate, endDate, searchQuery]);

    const completedCount = useMemo(
        () => history.filter((h) => h.status === 'completed').length,
        [history]
    );

    // Unique count of businesses assisted (completed only)
    const uniqueAssistedCount = useMemo(() => {
        const set = new Set();
        history
            .filter((h) => h.status === 'completed')
            .forEach((h) => {
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

    function openUpdateModal(item, initialStatus = 'completed') {
        setDetailItem(null);
        setUpdateItem(item);
        setUpdateFormData({
            status: initialStatus,
            title: item.title || '',
            held_on: item.held_on || new Date().toISOString().split('T')[0],
            description: item.description || '',
            outcome: item.outcome || '',
        });
    }

    function closeUpdateModal() {
        if (isUpdating) return;
        setUpdateItem(null);
    }

    function handleInputChange(e) {
        setFormData({ ...formData, [e.target.name]: e.target.value });
    }

    function handleUpdateInputChange(e) {
        setUpdateFormData({ ...updateFormData, [e.target.name]: e.target.value });
    }

    async function handleSubmit(e) {
        e.preventDefault();
        setIsSubmitting(true);
        try {
            const res = await apiFetch('/petugas/catat-pembinaan', {
                method: 'POST',
                body: JSON.stringify({
                    follow_up_id: selectedItem.id,
                    title: formData.title,
                    held_on: formData.held_on,
                    description: formData.description,
                    outcome: formData.outcome,
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
                status: 'scheduled',
                recorded_by_name: 'Petugas Desa',
                created_at: new Date().toISOString(),
            };

            setQueue((prev) => prev.filter((item) => item.id !== selectedItem.id));
            setHistory((prev) => [newSession, ...prev]);
            closeModal();

            setSuccessMessage(`Pembinaan untuk "${selectedItem.umkm_name}" berhasil dicatat.`);
            setErrorMessage('');
            setTimeout(() => setSuccessMessage(''), 6000);
        } catch (error) {
            setErrorMessage(error.message || 'Gagal mencatat data pembinaan.');
            setSuccessMessage('');
        } finally {
            setIsSubmitting(false);
        }
    }

    async function handleUpdateSubmit(e) {
        e.preventDefault();
        if (!updateItem) return;
        setIsUpdating(true);
        try {
            const res = await apiFetch(`/petugas/catat-pembinaan/${updateItem.id}`, {
                method: 'PATCH',
                body: JSON.stringify({
                    status: updateFormData.status,
                    title: updateFormData.title,
                    held_on: updateFormData.held_on,
                    description: updateFormData.description,
                    outcome: updateFormData.outcome,
                }),
            });

            const updatedSession = res?.session || {
                ...updateItem,
                status: updateFormData.status,
                title: updateFormData.title,
                held_on: updateFormData.held_on,
                description: updateFormData.description,
                outcome: updateFormData.outcome,
            };

            setHistory((prev) =>
                prev.map((item) => (item.id === updateItem.id ? updatedSession : item))
            );
            setUpdateItem(null);

            setSuccessMessage(
                res?.message ||
                    (updateFormData.status === 'completed'
                        ? `Pembinaan untuk "${updateItem.umkm_name}" berhasil ditandai selesai beserta hasilnya.`
                        : updateFormData.status === 'cancelled'
                          ? `Jadwal pembinaan untuk "${updateItem.umkm_name}" berhasil dibatalkan.`
                          : `Status pembinaan untuk "${updateItem.umkm_name}" berhasil diperbarui.`)
            );
            setErrorMessage('');
            setTimeout(() => setSuccessMessage(''), 6000);
        } catch (error) {
            setErrorMessage(error.message || 'Gagal memperbarui status pembinaan.');
            setSuccessMessage('');
        } finally {
            setIsUpdating(false);
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
                            <p className="text-2xl font-bold text-ink">{completedCount}</p>
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
            <div className="rounded-xl border border-border bg-white p-4 shadow-xs">
                <div className="flex flex-col gap-3 md:flex-row md:items-end">
                    <div className="grid grid-cols-1 gap-3 sm:grid-cols-2 md:w-[28rem] md:shrink-0">
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
                    <div className="flex min-w-0 flex-1 items-end gap-2">
                        <div className="relative min-w-0 flex-1">
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
                                                        ? 'Coba sesuaikan rentang tanggal atau kata kunci pencarian.'
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
                                                {item.status === 'completed' ? (
                                                    <span className="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] font-semibold text-emerald-800">
                                                        <HugeiconsIcon icon={Tick01Icon} size={12} strokeWidth={2.5} />
                                                        Selesai
                                                    </span>
                                                ) : item.status === 'cancelled' ? (
                                                    <span className="inline-flex items-center gap-1 rounded-full bg-red-100 px-2.5 py-1 text-[11px] font-semibold text-red-700">
                                                        <HugeiconsIcon icon={Cancel01Icon} size={12} strokeWidth={2.5} />
                                                        Dibatalkan
                                                    </span>
                                                ) : (
                                                    <span className="inline-flex items-center gap-1 rounded-full bg-sky-100 px-2.5 py-1 text-[11px] font-semibold text-sky-800">
                                                        <HugeiconsIcon icon={Calendar01Icon} size={12} strokeWidth={2.5} />
                                                        Dijadwalkan
                                                    </span>
                                                )}
                                            </td>
                                            <td className="px-4 py-3">
                                                <div className="flex flex-wrap items-center gap-1.5">
                                                    {item.status === 'scheduled' && (
                                                        <>
                                                            <Button
                                                                size="sm"
                                                                onClick={() => openUpdateModal(item, 'completed')}
                                                                className="h-7 px-2.5 text-xs bg-emerald-600 hover:bg-emerald-700 text-white"
                                                            >
                                                                <HugeiconsIcon icon={Tick01Icon} size={13} strokeWidth={2.5} className="mr-1" />
                                                                Tandai Selesai
                                                            </Button>
                                                            <Button
                                                                variant="outline"
                                                                size="sm"
                                                                onClick={() => openUpdateModal(item, 'cancelled')}
                                                                className="h-7 px-2.5 text-xs border-red-200 text-red-700 hover:bg-red-50"
                                                            >
                                                                <HugeiconsIcon icon={Cancel01Icon} size={13} strokeWidth={2.5} className="mr-1" />
                                                                Batalkan
                                                            </Button>
                                                        </>
                                                    )}
                                                    <Button
                                                        variant="outline"
                                                        size="sm"
                                                        onClick={() => setDetailItem(item)}
                                                        className="h-7 px-2.5 gap-1 text-xs"
                                                    >
                                                        <HugeiconsIcon icon={ViewIcon} size={14} />
                                                        Lihat Detail
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
                                                        ? 'Coba atur ulang rentang tanggal atau kata kunci pencarian.'
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
                                        {detailItem.status === 'completed' ? (
                                            <span className="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800">
                                                <HugeiconsIcon icon={Tick01Icon} size={14} strokeWidth={2.5} />
                                                Selesai Dilaksanakan
                                            </span>
                                        ) : detailItem.status === 'cancelled' ? (
                                            <span className="inline-flex items-center gap-1 rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                                <HugeiconsIcon icon={Cancel01Icon} size={14} strokeWidth={2.5} />
                                                Dibatalkan
                                            </span>
                                        ) : (
                                            <span className="inline-flex items-center gap-1 rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold text-sky-800">
                                                <HugeiconsIcon icon={Calendar01Icon} size={14} strokeWidth={2.5} />
                                                Dijadwalkan
                                            </span>
                                        )}
                                    </div>
                                </div>

                                <div className="pt-3 border-t border-border flex flex-col sm:flex-row gap-2">
                                    {detailItem.status === 'scheduled' && (
                                        <>
                                            <Button
                                                type="button"
                                                onClick={() => openUpdateModal(detailItem, 'completed')}
                                                className="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white"
                                            >
                                                <HugeiconsIcon icon={Tick01Icon} size={15} strokeWidth={2.5} className="mr-1.5" />
                                                Tandai Selesai
                                            </Button>
                                            <Button
                                                type="button"
                                                variant="outline"
                                                onClick={() => openUpdateModal(detailItem, 'cancelled')}
                                                className="flex-1 border-red-200 text-red-700 hover:bg-red-50"
                                            >
                                                <HugeiconsIcon icon={Cancel01Icon} size={15} strokeWidth={2.5} className="mr-1.5" />
                                                Batalkan Jadwal
                                            </Button>
                                        </>
                                    )}
                                    <Button
                                        type="button"
                                        variant="outline"
                                        onClick={() => setDetailItem(null)}
                                        className="flex-1"
                                    >
                                        Tutup
                                    </Button>
                                </div>
                            </div>
                        </motion.div>
                    </div>
                )}
            </AnimatePresence>

            {/* Modal Update Status & Hasil Pembinaan */}
            <AnimatePresence>
                {updateItem && (
                    <div className="fixed inset-0 z-50 flex flex-col justify-end lg:items-center lg:justify-center">
                        <motion.button
                            type="button"
                            aria-label="Tutup modal ubah status"
                            className="fixed inset-0 bg-ink/50 backdrop-blur-sm"
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            exit={{ opacity: 0 }}
                            onClick={closeUpdateModal}
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
                                    <h3 className="text-lg font-bold text-ink">
                                        {updateFormData.status === 'cancelled'
                                            ? 'Batalkan Jadwal Pembinaan'
                                            : 'Tandai Pembinaan Selesai'}
                                    </h3>
                                    <p className="text-xs text-muted-foreground">
                                        UMKM: {updateItem.umkm_name} &middot; {updateItem.program_name}
                                    </p>
                                </div>
                                <button
                                    type="button"
                                    onClick={closeUpdateModal}
                                    className="grid size-8 place-items-center rounded-lg text-slate-500 hover:bg-slate-100"
                                >
                                    <HugeiconsIcon icon={Cancel01Icon} size={20} />
                                </button>
                            </div>
                            <div className="px-5 py-5">
                                <form onSubmit={handleUpdateSubmit} className="space-y-4">
                                    <div className="space-y-2">
                                        <label className="text-sm font-semibold text-ink block">
                                            Status Pembinaan
                                        </label>
                                        <select
                                            name="status"
                                            value={updateFormData.status}
                                            onChange={handleUpdateInputChange}
                                            className="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-ink shadow-xs focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                                        >
                                            <option value="completed">Selesai (Completed)</option>
                                            <option value="cancelled">Dibatalkan (Cancelled)</option>
                                            <option value="scheduled">Dijadwalkan (Scheduled)</option>
                                        </select>
                                    </div>

                                    {updateFormData.status === 'cancelled' ? (
                                        <div className="rounded-xl border border-red-200 bg-red-50 p-4 text-xs text-red-800 space-y-1">
                                            <p className="font-bold">Konfirmasi Pembatalan Jadwal</p>
                                            <p>
                                                Sesi pembinaan ini akan ditandai sebagai <strong>Dibatalkan</strong>. Data sesi tetap tersimpan di riwayat sebagai arsip dan tidak dihitung sebagai pembinaan selesai.
                                            </p>
                                        </div>
                                    ) : (
                                        <>
                                            <div className="space-y-2">
                                                <label className="text-sm font-semibold text-ink flex items-center gap-1.5">
                                                    <HugeiconsIcon icon={File02Icon} size={16} /> Nama Kegiatan
                                                </label>
                                                <Input
                                                    name="title"
                                                    value={updateFormData.title}
                                                    onChange={handleUpdateInputChange}
                                                    required
                                                />
                                            </div>
                                            <div className="space-y-2">
                                                <label className="text-sm font-semibold text-ink flex items-center gap-1.5">
                                                    <HugeiconsIcon icon={Calendar01Icon} size={16} /> Tanggal Pelaksanaan
                                                </label>
                                                <Input
                                                    type="date"
                                                    name="held_on"
                                                    value={updateFormData.held_on}
                                                    onChange={handleUpdateInputChange}
                                                    required
                                                />
                                            </div>
                                            <div className="space-y-2">
                                                <label className="text-sm font-semibold text-ink flex items-center gap-1.5">
                                                    <HugeiconsIcon icon={File02Icon} size={16} /> Deskripsi Kegiatan
                                                </label>
                                                <Textarea
                                                    name="description"
                                                    value={updateFormData.description}
                                                    onChange={handleUpdateInputChange}
                                                    rows={3}
                                                    required
                                                />
                                            </div>
                                            <div className="space-y-2">
                                                <label className="text-sm font-semibold text-ink flex items-center gap-1.5">
                                                    Hasil Kegiatan (Outcome)
                                                </label>
                                                <Textarea
                                                    name="outcome"
                                                    value={updateFormData.outcome}
                                                    onChange={handleUpdateInputChange}
                                                    placeholder="Contoh: Pemilik sudah membuat 5 foto produk..."
                                                    rows={3}
                                                    required={updateFormData.status === 'completed'}
                                                />
                                            </div>
                                        </>
                                    )}

                                    <div className="pt-4 sticky bottom-0 bg-white pb-2 flex gap-2">
                                        <Button
                                            type="button"
                                            variant="outline"
                                            onClick={closeUpdateModal}
                                            className="flex-1"
                                            disabled={isUpdating}
                                        >
                                            Batal
                                        </Button>
                                        <Button
                                            type="submit"
                                            className={cn(
                                                'flex-1',
                                                updateFormData.status === 'cancelled'
                                                    ? 'bg-red-600 hover:bg-red-700 text-white'
                                                    : ''
                                            )}
                                            disabled={isUpdating}
                                        >
                                            {isUpdating
                                                ? 'Menyimpan...'
                                                : updateFormData.status === 'cancelled'
                                                  ? 'Simpan Pembatalan'
                                                  : 'Simpan'}
                                        </Button>
                                    </div>
                                </form>
                            </div>
                        </motion.div>
                    </div>
                )}
            </AnimatePresence>
        </div>
    );
}
