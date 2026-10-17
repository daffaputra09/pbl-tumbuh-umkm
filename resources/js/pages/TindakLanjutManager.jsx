import { useState, useMemo } from 'react';
import { HugeiconsIcon } from '@hugeicons/react';
import {
    TeachingIcon,
    File02Icon,
    Calendar01Icon,
    Tag01Icon,
    Cancel01Icon,
    Tick01Icon,
    Alert02Icon,
    Search01Icon,
} from '@hugeicons/core-free-icons';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { motion, AnimatePresence } from 'motion/react';
import { apiFetch } from '@/lib/api';
import { cn } from '@/lib/utils';

export default function TindakLanjutManager({ queue: initialQueue = [] }) {
    const [queue, setQueue] = useState(initialQueue);
    const [selectedItem, setSelectedItem] = useState(null);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [statusFilter, setStatusFilter] = useState('all');
    const [searchQuery, setSearchQuery] = useState('');
    const [formData, setFormData] = useState({
        planned_activity: '',
        planned_on: '',
        submission_note: '',
    });

    const counts = useMemo(() => {
        return {
            all: queue.length,
            ready: queue.filter((item) => item.status === 'ready').length,
            pending: queue.filter((item) => item.status === 'pending').length,
            approved: queue.filter((item) => item.status === 'approved').length,
            rejected: queue.filter((item) => item.status === 'rejected').length,
        };
    }, [queue]);

    const filteredQueue = useMemo(() => {
        return queue.filter((item) => {
            const matchesStatus =
                statusFilter === 'all' || item.status === statusFilter;

            if (!matchesStatus) return false;

            if (!searchQuery.trim()) return true;

            const q = searchQuery.toLowerCase();
            return (
                item.umkm_name?.toLowerCase().includes(q) ||
                item.owner_name?.toLowerCase().includes(q) ||
                item.recommended_program?.toLowerCase().includes(q) ||
                item.problem_category?.toLowerCase().includes(q)
            );
        });
    }, [queue, statusFilter, searchQuery]);

    function openModal(item) {
        setSelectedItem(item);
        setFormData({
            planned_activity: item.planned_activity || (item.recommended_program ? `Pelaksanaan ${item.recommended_program}` : ''),
            planned_on: item.planned_on || '',
            submission_note: item.submission_note || '',
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
            if (selectedItem) {
                await apiFetch('/petugas/tindak-lanjut', {
                    method: 'POST',
                    body: JSON.stringify({
                        business_id: selectedItem.business_id,
                        program_recommendation_id: selectedItem.program_recommendation_id,
                        assistance_program_id: selectedItem.assistance_program_id,
                        planned_activity: formData.planned_activity,
                        planned_on: formData.planned_on,
                        submission_note: formData.submission_note,
                    }),
                });
            }

            setQueue((prev) =>
                prev.map((item) =>
                    item.id === selectedItem.id
                        ? {
                              ...item,
                              status: 'pending',
                              planned_activity: formData.planned_activity,
                              planned_on: formData.planned_on,
                              submission_note: formData.submission_note,
                          }
                        : item,
                ),
            );
            closeModal();
            alert('Tindak lanjut berhasil diajukan ke Kepala Desa!');
        } catch (error) {
            alert(error.message || 'Gagal mengajukan tindak lanjut.');
        } finally {
            setIsSubmitting(false);
        }
    }

    return (
        <div className="space-y-6">
            <header>
                <div>
                    <h1 className="text-2xl font-bold tracking-tight text-ink sm:text-3xl">Ajukan Tindak Lanjut</h1>
                    <p className="mt-1 text-sm text-slate-600 max-w-3xl">
                        Tandai rekomendasi yang sudah diminati pelaku, lalu ajukan program pembinaan kepada Kepala Desa untuk disetujui.
                    </p>
                </div>
            </header>

            {/* Filter Pills & Search */}
            <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div className="flex flex-wrap gap-2">
                    <button
                        type="button"
                        onClick={() => setStatusFilter('all')}
                        className={cn(
                            'inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-semibold transition-all',
                            statusFilter === 'all'
                                ? 'bg-primary text-white shadow-sm'
                                : 'bg-slate-100 text-slate-600 hover:bg-slate-200',
                        )}
                    >
                        Semua
                        <span className={cn('rounded-full px-1.5 py-0.2 text-[10px]', statusFilter === 'all' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700')}>
                            {counts.all}
                        </span>
                    </button>
                    <button
                        type="button"
                        onClick={() => setStatusFilter('ready')}
                        className={cn(
                            'inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-semibold transition-all',
                            statusFilter === 'ready'
                                ? 'bg-primary text-white shadow-sm'
                                : 'bg-slate-100 text-slate-600 hover:bg-slate-200',
                        )}
                    >
                        Perlu Pengajuan
                        <span className={cn('rounded-full px-1.5 py-0.2 text-[10px]', statusFilter === 'ready' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700')}>
                            {counts.ready}
                        </span>
                    </button>
                    <button
                        type="button"
                        onClick={() => setStatusFilter('pending')}
                        className={cn(
                            'inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-semibold transition-all',
                            statusFilter === 'pending'
                                ? 'bg-amber-600 text-white shadow-sm'
                                : 'bg-amber-50 text-amber-800 hover:bg-amber-100',
                        )}
                    >
                        Menunggu Persetujuan
                        <span className={cn('rounded-full px-1.5 py-0.2 text-[10px]', statusFilter === 'pending' ? 'bg-white/20 text-white' : 'bg-amber-200/70 text-amber-900')}>
                            {counts.pending}
                        </span>
                    </button>
                    <button
                        type="button"
                        onClick={() => setStatusFilter('approved')}
                        className={cn(
                            'inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-semibold transition-all',
                            statusFilter === 'approved'
                                ? 'bg-emerald-600 text-white shadow-sm'
                                : 'bg-emerald-50 text-emerald-800 hover:bg-emerald-100',
                        )}
                    >
                        Disetujui
                        <span className={cn('rounded-full px-1.5 py-0.2 text-[10px]', statusFilter === 'approved' ? 'bg-white/20 text-white' : 'bg-emerald-200/70 text-emerald-900')}>
                            {counts.approved}
                        </span>
                    </button>
                    <button
                        type="button"
                        onClick={() => setStatusFilter('rejected')}
                        className={cn(
                            'inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-semibold transition-all',
                            statusFilter === 'rejected'
                                ? 'bg-red-600 text-white shadow-sm'
                                : 'bg-red-50 text-red-800 hover:bg-red-100',
                        )}
                    >
                        Ditolak
                        <span className={cn('rounded-full px-1.5 py-0.2 text-[10px]', statusFilter === 'rejected' ? 'bg-white/20 text-white' : 'bg-red-200/70 text-red-900')}>
                            {counts.rejected}
                        </span>
                    </button>
                </div>

                <div className="relative w-full sm:w-64">
                    <HugeiconsIcon icon={Search01Icon} size={15} className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                    <Input
                        type="search"
                        placeholder="Cari UMKM atau program..."
                        value={searchQuery}
                        onChange={(e) => setSearchQuery(e.target.value)}
                        className="pl-8 text-xs h-9"
                    />
                </div>
            </div>

            <Card>
                <CardHeader className="flex-row items-center gap-3 space-y-0">
                    <span className="flex size-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-primary">
                        <HugeiconsIcon icon={TeachingIcon} size={20} strokeWidth={1.8} aria-hidden="true" />
                    </span>
                    <div>
                        <CardTitle>Antrean Rekomendasi & Tindak Lanjut</CardTitle>
                        <p className="text-xs text-muted-foreground mt-0.5">
                            Menampilkan {filteredQueue.length} data tindak lanjut
                        </p>
                    </div>
                </CardHeader>
                <CardContent>
                    <div className="overflow-x-auto">
                        <table className="w-full text-left text-sm text-ink">
                            <thead className="bg-slate-50 text-xs text-muted-foreground uppercase">
                                <tr>
                                    <th className="px-4 py-3 font-semibold rounded-tl-xl">UMKM & Pemilik</th>
                                    <th className="px-4 py-3 font-semibold">Kategori Kendala</th>
                                    <th className="px-4 py-3 font-semibold">Rekomendasi Program</th>
                                    <th className="px-4 py-3 font-semibold rounded-tr-xl">Status & Aksi</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-border">
                                {filteredQueue.map((item) => (
                                    <tr key={item.id} className="hover:bg-slate-50/50 transition-colors">
                                        <td className="px-4 py-3">
                                            <p className="font-semibold">{item.umkm_name}</p>
                                            <p className="text-xs text-muted-foreground">{item.owner_name}</p>
                                        </td>
                                        <td className="px-4 py-3">
                                            <span className="inline-flex items-center gap-1 rounded-md bg-slate-100 px-2 py-1 text-xs font-medium text-slate-600">
                                                <HugeiconsIcon icon={Tag01Icon} size={14} />
                                                {item.problem_category}
                                            </span>
                                        </td>
                                        <td className="px-4 py-3">
                                            <p className="text-sm font-medium">{item.recommended_program}</p>
                                            {item.planned_activity && (
                                                <p className="text-xs text-slate-500 mt-0.5 flex items-center gap-1">
                                                    <HugeiconsIcon icon={File02Icon} size={12} />
                                                    {item.planned_activity}
                                                </p>
                                            )}
                                            {item.status === 'rejected' && item.decision_note && (
                                                <p className="text-xs font-medium text-red-600 mt-1 flex items-center gap-1">
                                                    <HugeiconsIcon icon={Alert02Icon} size={12} />
                                                    Alasan ditolak: "{item.decision_note}"
                                                </p>
                                            )}
                                        </td>
                                        <td className="px-4 py-3">
                                            {item.status === 'ready' && item.can_submit ? (
                                                <Button size="sm" onClick={() => openModal(item)}>
                                                    Buat Pengajuan
                                                </Button>
                                            ) : item.status === 'ready' ? (
                                                <span className="text-xs font-medium text-slate-500">
                                                    Usaha ini belum punya akun pelaku.
                                                </span>
                                            ) : item.status === 'pending' ? (
                                                <span className="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-2.5 py-1 text-[11px] font-semibold text-amber-800">
                                                    <span className="size-1.5 rounded-full bg-amber-500 animate-pulse" />
                                                    Menunggu Persetujuan
                                                </span>
                                            ) : item.status === 'approved' ? (
                                                <div className="flex flex-col sm:flex-row sm:items-center gap-2">
                                                    <span className="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] font-semibold text-emerald-800">
                                                        <HugeiconsIcon icon={Tick01Icon} size={12} strokeWidth={2.5} />
                                                        Disetujui
                                                    </span>
                                                    <a
                                                        href="/petugas/catat-pembinaan"
                                                        className="inline-flex items-center text-xs font-medium text-primary hover:underline whitespace-nowrap"
                                                    >
                                                        Catat Pembinaan &rarr;
                                                    </a>
                                                </div>
                                            ) : item.status === 'rejected' ? (
                                                <div className="flex flex-col sm:flex-row sm:items-center gap-2">
                                                    <span
                                                        className="inline-flex items-center gap-1 rounded-full bg-red-100 px-2.5 py-1 text-[11px] font-semibold text-red-700"
                                                        title={item.decision_note || 'Ditolak Kepala Desa'}
                                                    >
                                                        <HugeiconsIcon icon={Cancel01Icon} size={12} strokeWidth={2.5} />
                                                        Ditolak
                                                    </span>
                                                    {item.can_submit && (
                                                        <Button
                                                            size="sm"
                                                            variant="outline"
                                                            onClick={() => openModal(item)}
                                                            className="h-7 px-2.5 text-xs whitespace-nowrap"
                                                        >
                                                            Ajukan Ulang
                                                        </Button>
                                                    )}
                                                </div>
                                            ) : null}
                                        </td>
                                    </tr>
                                ))}
                                {filteredQueue.length === 0 && (
                                    <tr>
                                        <td colSpan="4" className="px-4 py-8 text-center text-muted-foreground">
                                            {searchQuery.trim()
                                                ? 'Tidak ada rekomendasi yang sesuai dengan pencarian.'
                                                : queue.length === 0
                                                  ? 'Belum ada pelaku yang mendaftarkan minat pada program.'
                                                  : 'Tidak ada data pada kategori status ini.'}
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>

            {/* Modal */}
            <AnimatePresence>
                {selectedItem && (
                    <div className="fixed inset-0 z-50 flex flex-col justify-end lg:items-center lg:justify-center">
                        <motion.button
                            type="button"
                            aria-label="Tutup modal"
                            className="absolute inset-0 bg-ink/40 backdrop-blur-sm"
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            exit={{ opacity: 0 }}
                            onClick={closeModal}
                        />
                        <motion.div
                            className="relative z-10 w-full rounded-t-2xl bg-white shadow-2xl lg:max-w-md lg:rounded-2xl"
                            initial={{ y: '100%' }}
                            animate={{ y: 0 }}
                            exit={{ y: '100%' }}
                            transition={{ type: 'spring', stiffness: 400, damping: 40 }}
                        >
                            <div className="flex items-center justify-between border-b border-border px-5 py-4">
                                <div>
                                    <h3 className="text-lg font-bold text-ink">
                                        {selectedItem.status === 'rejected' ? 'Ajukan Ulang Tindak Lanjut' : 'Buat Pengajuan Tindak Lanjut'}
                                    </h3>
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
                                {selectedItem.status === 'rejected' && selectedItem.decision_note && (
                                    <div className="mb-4 rounded-xl border border-red-200 bg-red-50 p-3 text-xs text-red-800">
                                        <p className="font-semibold flex items-center gap-1">
                                            <HugeiconsIcon icon={Alert02Icon} size={14} /> Catatan Penolakan Sebelumnya:
                                        </p>
                                        <p className="mt-1 italic">"{selectedItem.decision_note}"</p>
                                    </div>
                                )}
                                <form onSubmit={handleSubmit} className="space-y-4">
                                    <div className="space-y-2">
                                        <label className="text-sm font-semibold text-ink flex items-center gap-1.5">
                                            <HugeiconsIcon icon={File02Icon} size={16} /> Rencana Kegiatan
                                        </label>
                                        <Input
                                            name="planned_activity"
                                            value={formData.planned_activity}
                                            onChange={handleInputChange}
                                            placeholder="Contoh: Mengikuti workshop pemasaran..."
                                            required
                                        />
                                    </div>
                                    <div className="space-y-2">
                                        <label className="text-sm font-semibold text-ink flex items-center gap-1.5">
                                            <HugeiconsIcon icon={Calendar01Icon} size={16} /> Rencana Tanggal Pelaksanaan
                                        </label>
                                        <Input
                                            type="date"
                                            name="planned_on"
                                            value={formData.planned_on}
                                            onChange={handleInputChange}
                                            required
                                        />
                                    </div>
                                    <div className="space-y-2">
                                        <label className="text-sm font-semibold text-ink flex items-center gap-1.5">
                                            Catatan Petugas
                                        </label>
                                        <Textarea
                                            name="submission_note"
                                            value={formData.submission_note}
                                            onChange={handleInputChange}
                                            placeholder="Tuliskan catatan atau justifikasi untuk kepala desa..."
                                            rows={4}
                                            required
                                        />
                                    </div>
                                    <div className="pt-4">
                                        <Button type="submit" className="w-full" disabled={isSubmitting}>
                                            {isSubmitting ? 'Mengirim...' : 'Kirim Pengajuan'}
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
