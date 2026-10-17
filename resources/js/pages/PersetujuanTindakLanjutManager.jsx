import { useState, useMemo } from 'react';
import { HugeiconsIcon } from '@hugeicons/react';
import {
    CheckListIcon,
    Cancel01Icon,
    File02Icon,
    Calendar01Icon,
    Tick01Icon,
    TeachingIcon,
    Note01Icon,
    ViewIcon,
    ArrowLeft01Icon,
    Search01Icon,
    Alert02Icon,
    User02Icon,
} from '@hugeicons/core-free-icons';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Textarea } from '@/components/ui/textarea';
import { Input } from '@/components/ui/input';
import { cn } from '@/lib/utils';
import { motion, AnimatePresence } from 'motion/react';

// Modal backdrop with animation
function Backdrop({ onClick }) {
    return (
        <motion.button
            type="button"
            aria-label="Tutup modal"
            className="absolute inset-0 bg-ink/40 backdrop-blur-sm"
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            onClick={onClick}
        />
    );
}

// Shared modal shell with slide-up animation
function ModalShell({ children, maxWidth = 'lg:max-w-md' }) {
    return (
        <motion.div
            className={cn(
                'relative z-10 w-full rounded-t-2xl bg-white shadow-2xl lg:rounded-2xl overflow-hidden',
                maxWidth,
            )}
            initial={{ y: '100%' }}
            animate={{ y: 0 }}
            exit={{ y: '100%' }}
            transition={{ type: 'spring', stiffness: 400, damping: 40 }}
        >
            {children}
        </motion.div>
    );
}

function DetailModal({ item, onClose, onApprove, onReject }) {
    const isPending = item.status === 'pending';
    const isApproved = item.status === 'approved';

    return (
        <div className="fixed inset-0 z-50 flex flex-col justify-end lg:items-center lg:justify-center">
            <Backdrop onClick={onClose} />
            <ModalShell maxWidth="lg:max-w-lg">
                {/* Header */}
                <div className="flex items-center justify-between border-b border-border px-5 py-4">
                    <div className="flex items-center gap-3">
                        <span className="flex size-8 shrink-0 items-center justify-center rounded-full bg-brand-50 text-primary">
                            <HugeiconsIcon icon={ViewIcon} size={18} strokeWidth={1.8} />
                        </span>
                        <div>
                            <div className="flex items-center gap-2">
                                <h3 className="text-base font-bold text-ink">Detail Pengajuan</h3>
                                <span
                                    className={cn(
                                        'inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold',
                                        isPending
                                            ? 'bg-amber-100 text-amber-800'
                                            : isApproved
                                              ? 'bg-emerald-100 text-emerald-800'
                                              : 'bg-red-100 text-red-800',
                                    )}
                                >
                                    {isPending ? 'Menunggu' : isApproved ? 'Disetujui' : 'Ditolak'}
                                </span>
                            </div>
                            <p className="text-xs text-muted-foreground">UMKM: {item.umkm_name}</p>
                        </div>
                    </div>
                    <button
                        type="button"
                        onClick={onClose}
                        className="grid size-8 place-items-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600"
                    >
                        <HugeiconsIcon icon={Cancel01Icon} size={20} />
                    </button>
                </div>

                {/* Body */}
                <div className="px-5 py-5 space-y-4 max-h-[75vh] overflow-y-auto">
                    <div className="grid grid-cols-1 gap-3">
                        <div className="rounded-xl bg-slate-50 p-4 space-y-3">
                            <div className="flex items-start gap-3">
                                <span className="mt-0.5 flex size-7 shrink-0 items-center justify-center rounded-lg bg-white border border-slate-200 text-primary">
                                    <HugeiconsIcon icon={TeachingIcon} size={15} strokeWidth={1.8} />
                                </span>
                                <div>
                                    <p className="text-[11px] font-semibold uppercase tracking-wide text-muted-foreground">
                                        Rekomendasi Program
                                    </p>
                                    <p className="mt-0.5 text-sm font-semibold text-ink">{item.recommended_program}</p>
                                </div>
                            </div>

                            <div className="flex items-start gap-3">
                                <span className="mt-0.5 flex size-7 shrink-0 items-center justify-center rounded-lg bg-white border border-slate-200 text-primary">
                                    <HugeiconsIcon icon={File02Icon} size={15} strokeWidth={1.8} />
                                </span>
                                <div>
                                    <p className="text-[11px] font-semibold uppercase tracking-wide text-muted-foreground">
                                        Rencana Kegiatan
                                    </p>
                                    <p className="mt-0.5 text-sm text-ink">{item.planned_activity}</p>
                                </div>
                            </div>

                            <div className="flex items-start gap-3">
                                <span className="mt-0.5 flex size-7 shrink-0 items-center justify-center rounded-lg bg-white border border-slate-200 text-primary">
                                    <HugeiconsIcon icon={Calendar01Icon} size={15} strokeWidth={1.8} />
                                </span>
                                <div>
                                    <p className="text-[11px] font-semibold uppercase tracking-wide text-muted-foreground">
                                        Tanggal Rencana Pelaksanaan
                                    </p>
                                    <p className="mt-0.5 text-sm text-ink">{item.planned_on}</p>
                                </div>
                            </div>

                            {item.submitted_by_name && (
                                <div className="flex items-start gap-3">
                                    <span className="mt-0.5 flex size-7 shrink-0 items-center justify-center rounded-lg bg-white border border-slate-200 text-primary">
                                        <HugeiconsIcon icon={User02Icon} size={15} strokeWidth={1.8} />
                                    </span>
                                    <div>
                                        <p className="text-[11px] font-semibold uppercase tracking-wide text-muted-foreground">
                                            Diajukan Oleh
                                        </p>
                                        <p className="mt-0.5 text-sm text-ink">{item.submitted_by_name}</p>
                                    </div>
                                </div>
                            )}
                        </div>

                        <div className="rounded-xl border border-slate-200 p-4">
                            <div className="flex items-center gap-2 mb-2">
                                <HugeiconsIcon icon={Note01Icon} size={15} strokeWidth={1.8} className="text-muted-foreground" />
                                <p className="text-[11px] font-semibold uppercase tracking-wide text-muted-foreground">
                                    Catatan Petugas
                                </p>
                            </div>
                            <p className="text-sm leading-relaxed text-ink">{item.submission_note}</p>
                        </div>

                        {!isPending && item.decision_note && (
                            <div
                                className={cn(
                                    'rounded-xl p-4 border',
                                    isApproved
                                        ? 'border-emerald-200 bg-emerald-50/50'
                                        : 'border-red-200 bg-red-50/50',
                                )}
                            >
                                <div className="flex items-center gap-2 mb-1.5">
                                    <HugeiconsIcon
                                        icon={isApproved ? Tick01Icon : Alert02Icon}
                                        size={15}
                                        className={isApproved ? 'text-emerald-700' : 'text-red-700'}
                                    />
                                    <p
                                        className={cn(
                                            'text-[11px] font-semibold uppercase tracking-wide',
                                            isApproved ? 'text-emerald-800' : 'text-red-800',
                                        )}
                                    >
                                        Catatan Keputusan ({isApproved ? 'Disetujui' : 'Ditolak'})
                                    </p>
                                </div>
                                <p className="text-sm leading-relaxed text-ink">{item.decision_note}</p>
                                {item.decided_at && (
                                    <p className="text-xs text-muted-foreground mt-2">
                                        Tanggal Keputusan: {item.decided_at}
                                    </p>
                                )}
                            </div>
                        )}
                    </div>
                </div>

                {/* Footer */}
                <div className="flex items-center justify-end gap-3 border-t border-border px-5 py-4 bg-slate-50/50">
                    <Button variant="outline" size="sm" onClick={onClose} className="gap-1.5">
                        <HugeiconsIcon icon={ArrowLeft01Icon} size={14} />
                        {isPending ? 'Kembali' : 'Tutup'}
                    </Button>
                    {isPending && (
                        <>
                            <Button
                                size="sm"
                                onClick={onReject}
                                className="border border-red-200 bg-white text-red-600 shadow-none hover:bg-red-50"
                            >
                                Tolak
                            </Button>
                            <Button
                                size="sm"
                                onClick={onApprove}
                                className="bg-success hover:bg-success/90 text-white border-transparent"
                            >
                                <HugeiconsIcon icon={Tick01Icon} size={14} />
                                Setujui
                            </Button>
                        </>
                    )}
                </div>
            </ModalShell>
        </div>
    );
}

function ConfirmModal({ item, actionType, onClose, onBack, formData, onFormChange, onSubmit, todayDate }) {
    const isApprove = actionType === 'approve';

    return (
        <div className="fixed inset-0 z-50 flex flex-col justify-end lg:items-center lg:justify-center">
            <Backdrop onClick={onClose} />
            <ModalShell maxWidth="lg:max-w-md">
                {/* Header */}
                <div
                    className={cn(
                        'flex items-center justify-between border-b px-5 py-4',
                        isApprove ? 'border-success/20 bg-success/10' : 'border-red-100 bg-red-50/50',
                    )}
                >
                    <div className="flex items-center gap-3">
                        <span
                            className={cn(
                                'flex size-8 shrink-0 items-center justify-center rounded-full',
                                isApprove ? 'bg-success/20 text-success' : 'bg-red-100 text-red-600',
                            )}
                        >
                            <HugeiconsIcon icon={isApprove ? Tick01Icon : Cancel01Icon} size={18} strokeWidth={2} />
                        </span>
                        <div>
                            <h3 className="text-base font-bold text-ink">
                                {isApprove ? 'Setujui Pengajuan' : 'Tolak Pengajuan'}
                            </h3>
                            <p className="text-xs text-muted-foreground">{item.umkm_name}</p>
                        </div>
                    </div>
                    <button
                        type="button"
                        onClick={onClose}
                        className="grid size-8 place-items-center rounded-lg text-slate-400 hover:bg-slate-100"
                    >
                        <HugeiconsIcon icon={Cancel01Icon} size={20} />
                    </button>
                </div>

                {/* Body */}
                <div className="px-5 py-5">
                    <form onSubmit={onSubmit} className="space-y-5">
                        <div className="rounded-xl bg-slate-50 p-3 text-sm">
                            <div className="grid grid-cols-3 gap-2 mb-2 border-b border-slate-200 pb-2">
                                <span className="text-muted-foreground">Program:</span>
                                <span className="col-span-2 font-medium">{item.recommended_program}</span>
                            </div>
                            <div className="grid grid-cols-3 gap-2">
                                <span className="text-muted-foreground">Tanggal:</span>
                                <span className="col-span-2 font-medium">{item.planned_on}</span>
                            </div>
                        </div>

                        <div className="space-y-2">
                            <label className="text-sm font-semibold text-ink">
                                Catatan Keputusan <span className="text-red-500">*</span>
                            </label>
                            <Textarea
                                name="decision_note"
                                value={formData.decision_note}
                                onChange={onFormChange}
                                placeholder={isApprove ? 'Tambahkan catatan persetujuan...' : 'Alasan penolakan...'}
                                rows={3}
                                required
                            />
                        </div>

                        <div className="flex items-center gap-4 text-xs text-muted-foreground bg-slate-50 p-3 rounded-lg border border-slate-100">
                            <div className="flex-1">
                                <span className="block font-semibold text-slate-700">Ditetapkan oleh:</span>
                                Bpk/Ibu Kepala Desa
                            </div>
                            <div className="w-px h-8 bg-slate-200"></div>
                            <div className="flex-1">
                                <span className="block font-semibold text-slate-700">Tanggal Keputusan:</span>
                                {todayDate}
                            </div>
                        </div>

                        <div className="flex items-center gap-3 pt-1">
                            <Button type="button" variant="outline" className="flex-1 gap-1.5" onClick={onBack}>
                                <HugeiconsIcon icon={ArrowLeft01Icon} size={14} />
                                Kembali
                            </Button>
                            <Button
                                type="submit"
                                className={cn(
                                    'flex-1 border-transparent shadow-none',
                                    isApprove
                                        ? 'bg-success hover:bg-success/90 text-white'
                                        : 'bg-red-600 hover:bg-red-700 text-white',
                                )}
                            >
                                {isApprove ? 'Konfirmasi Persetujuan' : 'Konfirmasi Penolakan'}
                            </Button>
                        </div>
                    </form>
                </div>
            </ModalShell>
        </div>
    );
}

export default function PersetujuanTindakLanjutManager({ queue: initialQueue = [] }) {
    const [queue, setQueue] = useState(initialQueue);
    // activeModal: null | 'detail' | 'confirm'
    const [activeModal, setActiveModal] = useState(null);
    const [selectedItem, setSelectedItem] = useState(null);
    const [actionType, setActionType] = useState(null); // 'approve' | 'reject'
    const [formData, setFormData] = useState({ decision_note: '' });
    const [statusFilter, setStatusFilter] = useState('all');
    const [searchQuery, setSearchQuery] = useState('');

    const todayDate = new Date().toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });

    const counts = useMemo(() => {
        return {
            all: queue.length,
            pending: queue.filter((i) => i.status === 'pending').length,
            approved: queue.filter((i) => i.status === 'approved').length,
            rejected: queue.filter((i) => i.status === 'rejected').length,
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
                item.submitted_by_name?.toLowerCase().includes(q)
            );
        });
    }, [queue, statusFilter, searchQuery]);

    function openDetail(item) {
        setSelectedItem(item);
        setActiveModal('detail');
    }

    function openConfirm(type) {
        setActionType(type);
        setFormData({ decision_note: '' });
        setActiveModal('confirm');
    }

    function backToDetail() {
        setActiveModal('detail');
    }

    function closeAll() {
        setActiveModal(null);
        setSelectedItem(null);
        setActionType(null);
    }

    function handleFormChange(e) {
        setFormData({ ...formData, [e.target.name]: e.target.value });
    }

    async function handleSubmit(e) {
        e.preventDefault();
        const finalStatus = actionType === 'approve' ? 'approved' : 'rejected';

        try {
            const response = await fetch(`/pimpinan/persetujuan-tindak-lanjut/${selectedItem.id}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    Accept: 'application/json',
                },
                body: JSON.stringify({
                    status: finalStatus,
                    decision_note: formData.decision_note,
                }),
            });

            if (response.ok) {
                alert('Keputusan berhasil disimpan!');
                setQueue((prev) =>
                    prev.map((item) =>
                        item.id === selectedItem.id
                            ? {
                                  ...item,
                                  status: finalStatus,
                                  decision_note: formData.decision_note,
                                  decided_at: new Date().toISOString().split('T')[0],
                              }
                            : item,
                    ),
                );
                closeAll();
            } else {
                const data = await response.json();
                alert(data.message || 'Gagal menyimpan keputusan.');
            }
        } catch (error) {
            alert('Terjadi kesalahan sistem atau koneksi.');
        }
    }

    return (
        <div className="relative isolate -mx-4 -mt-5 -mb-5 overflow-hidden bg-white sm:-mx-6 sm:-mt-6 sm:-mb-6 lg:-mx-8 lg:-mt-8 lg:-mb-8">
            <div className="mx-auto max-w-7xl px-4 py-10 sm:px-6 sm:py-14 lg:px-8">
                <header className="mb-8">
                    <p className="text-sm font-semibold text-primary">Keputusan</p>
                    <h1 className="mt-1 text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">
                        Persetujuan Tindak Lanjut
                    </h1>
                    <p className="mt-2 text-sm leading-relaxed text-muted-foreground">
                        Tinjau dan berikan keputusan (Setujui atau Tolak) atas pengajuan program pembinaan UMKM dari Petugas Desa.
                    </p>
                </header>

                {/* Filter Pills & Search */}
                <div className="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
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
                            Semua Pengajuan
                            <span className={cn('rounded-full px-1.5 py-0.2 text-[10px]', statusFilter === 'all' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700')}>
                                {counts.all}
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
                            <HugeiconsIcon icon={CheckListIcon} size={20} strokeWidth={1.8} aria-hidden="true" />
                        </span>
                        <div>
                            <CardTitle>Daftar Pengajuan Tindak Lanjut</CardTitle>
                            <p className="text-xs text-muted-foreground mt-0.5">
                                Menampilkan {filteredQueue.length} data pengajuan
                            </p>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div className="overflow-x-auto">
                            <table className="w-full text-left text-sm text-ink">
                                <thead className="bg-slate-50 text-xs text-muted-foreground uppercase">
                                    <tr>
                                        <th className="px-4 py-3 font-semibold rounded-tl-xl">Nama UMKM</th>
                                        <th className="px-4 py-3 font-semibold">Program & Kegiatan</th>
                                        <th className="px-4 py-3 font-semibold">Tanggal Rencana</th>
                                        <th className="px-4 py-3 font-semibold">Catatan Petugas</th>
                                        <th className="px-4 py-3 font-semibold rounded-tr-xl">Status & Aksi</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-border">
                                    {filteredQueue.map((item) => (
                                        <tr key={item.id} className="hover:bg-slate-50/50 transition-colors">
                                            <td className="px-4 py-3">
                                                <p className="font-semibold">{item.umkm_name}</p>
                                                {item.owner_name && (
                                                    <p className="text-xs text-muted-foreground">{item.owner_name}</p>
                                                )}
                                            </td>
                                            <td className="px-4 py-3">
                                                <p className="font-medium text-primary">{item.recommended_program}</p>
                                                <p className="text-xs text-muted-foreground mt-0.5 flex items-center gap-1">
                                                    <HugeiconsIcon icon={File02Icon} size={12} />
                                                    {item.planned_activity}
                                                </p>
                                            </td>
                                            <td className="px-4 py-3">
                                                <span className="inline-flex items-center gap-1 rounded-md bg-slate-100 px-2 py-1 text-xs font-medium text-slate-600">
                                                    <HugeiconsIcon icon={Calendar01Icon} size={14} />
                                                    {item.planned_on}
                                                </span>
                                            </td>
                                            <td
                                                className="px-4 py-3 text-xs italic text-slate-600 max-w-[200px] truncate"
                                                title={item.submission_note}
                                            >
                                                "{item.submission_note}"
                                            </td>
                                            <td className="px-4 py-3">
                                                {item.status === 'pending' ? (
                                                    <Button
                                                        size="sm"
                                                        variant="outline"
                                                        onClick={() => openDetail(item)}
                                                        className="gap-1.5 whitespace-nowrap"
                                                    >
                                                        <HugeiconsIcon icon={ViewIcon} size={14} />
                                                        Tinjau Pengajuan
                                                    </Button>
                                                ) : (
                                                    <div className="flex items-center gap-2">
                                                        <span
                                                            className={cn(
                                                                'inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[11px] font-semibold',
                                                                item.status === 'approved'
                                                                    ? 'bg-emerald-100 text-emerald-800'
                                                                    : 'bg-red-100 text-red-700',
                                                            )}
                                                        >
                                                            <HugeiconsIcon
                                                                icon={item.status === 'approved' ? Tick01Icon : Cancel01Icon}
                                                                size={12}
                                                                strokeWidth={2.5}
                                                            />
                                                            {item.status === 'approved' ? 'Disetujui' : 'Ditolak'}
                                                        </span>
                                                        <Button
                                                            size="sm"
                                                            variant="ghost"
                                                            onClick={() => openDetail(item)}
                                                            className="h-7 px-2 text-xs text-slate-500 hover:text-ink whitespace-nowrap"
                                                        >
                                                            Detail
                                                        </Button>
                                                    </div>
                                                )}
                                            </td>
                                        </tr>
                                    ))}
                                    {filteredQueue.length === 0 && (
                                        <tr>
                                            <td colSpan="5" className="px-4 py-8 text-center text-muted-foreground">
                                                {searchQuery.trim()
                                                    ? 'Tidak ada pengajuan yang sesuai dengan pencarian.'
                                                    : queue.length === 0
                                                      ? 'Tidak ada antrean persetujuan saat ini.'
                                                      : 'Tidak ada data pada kategori status ini.'}
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </CardContent>
                </Card>
            </div>

            {/* Modal Stack */}
            <AnimatePresence mode="wait">
                {activeModal === 'detail' && selectedItem && (
                    <DetailModal
                        key="detail-modal"
                        item={selectedItem}
                        onClose={closeAll}
                        onApprove={() => openConfirm('approve')}
                        onReject={() => openConfirm('reject')}
                    />
                )}
                {activeModal === 'confirm' && selectedItem && (
                    <ConfirmModal
                        key="confirm-modal"
                        item={selectedItem}
                        actionType={actionType}
                        onClose={closeAll}
                        onBack={backToDetail}
                        formData={formData}
                        onFormChange={handleFormChange}
                        onSubmit={handleSubmit}
                        todayDate={todayDate}
                    />
                )}
            </AnimatePresence>
        </div>
    );
}
