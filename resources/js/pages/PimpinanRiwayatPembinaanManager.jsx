import { useState, useMemo } from 'react';
import { HugeiconsIcon } from '@hugeicons/react';
import {
    TeachingIcon,
    Calendar01Icon,
    Tag01Icon,
    Cancel01Icon,
    Search01Icon,
    Tick01Icon,
    ViewIcon,
    Store01Icon,
    User02Icon,
    RotateLeft01Icon,
    Analytics01Icon,
} from '@hugeicons/core-free-icons';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { cn } from '@/lib/utils';
import { motion, AnimatePresence } from 'motion/react';

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

export default function PimpinanRiwayatPembinaanManager({
    history: initialHistory = [],
    businesses = [],
}) {
    const [history] = useState(initialHistory || []);

    // Filters
    const [umkmFilter, setUmkmFilter] = useState('all');
    const [startDate, setStartDate] = useState('');
    const [endDate, setEndDate] = useState('');
    const [searchQuery, setSearchQuery] = useState('');

    // Detail Modal
    const [detailItem, setDetailItem] = useState(null);

    // Combine distinct businesses for the dropdown filter
    const umkmOptions = useMemo(() => {
        const map = new Map();
        businesses.forEach((b) => {
            if (b.name) map.set(String(b.id || b.name), { id: b.id, name: b.name });
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
    }, [businesses, history]);

    const hasActiveFilter =
        umkmFilter !== 'all' || startDate !== '' || endDate !== '' || searchQuery.trim() !== '';

    function resetFilters() {
        setUmkmFilter('all');
        setStartDate('');
        setEndDate('');
        setSearchQuery('');
    }

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

        // Default sorting: dari yang terbaru dicatat oleh petugas sampai terlama
        return list.slice().sort((a, b) => {
            const timeA = a.created_at ? new Date(a.created_at).getTime() : 0;
            const timeB = b.created_at ? new Date(b.created_at).getTime() : 0;
            if (timeB !== timeA) {
                return timeB - timeA;
            }
            return (b.id || 0) - (a.id || 0);
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

    // Sessions held this month
    const thisMonthCount = useMemo(() => {
        const now = new Date();
        const currentYear = now.getFullYear();
        const currentMonth = now.getMonth();

        return history.filter((h) => {
            if (!h.held_on) return false;
            const d = new Date(h.held_on);
            return d.getFullYear() === currentYear && d.getMonth() === currentMonth;
        }).length;
    }, [history]);

    return (
        <div className="space-y-6">
            {/* Header */}
            <header>
                <div>
                    <h1 className="text-2xl font-bold tracking-tight text-ink sm:text-3xl">
                        Riwayat Pembinaan UMKM
                    </h1>
                    <p className="mt-1 text-sm text-slate-600 max-w-3xl">
                        Pantau seluruh rekam jejak pelaksanaan kegiatan pembinaan UMKM yang telah diselesaikan dan dicatat oleh petugas desa.
                    </p>
                </div>
            </header>

            {/* Stat Cards */}
            <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <Card className="border-border/80 shadow-xs">
                    <CardContent className="flex items-center gap-3 p-4">
                        <span className="flex size-11 items-center justify-center rounded-xl bg-brand-50 text-primary">
                            <HugeiconsIcon icon={TeachingIcon} size={22} strokeWidth={1.8} />
                        </span>
                        <div>
                            <p className="text-xs font-medium text-slate-500">Total Pembinaan Selesai</p>
                            <p className="text-2xl font-bold text-ink">{history.length}</p>
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

                <Card className="border-border/80 shadow-xs">
                    <CardContent className="flex items-center gap-3 p-4">
                        <span className="flex size-11 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                            <HugeiconsIcon icon={Analytics01Icon} size={22} strokeWidth={1.8} />
                        </span>
                        <div>
                            <p className="text-xs font-medium text-slate-500">Pembinaan Bulan Ini</p>
                            <p className="text-2xl font-bold text-ink">{thisMonthCount}</p>
                        </div>
                    </CardContent>
                </Card>
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
                                placeholder="Cari UMKM, kegiatan..."
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

            {/* Table Card */}
            <Card>
                <CardHeader className="flex-row items-center gap-3 space-y-0 border-b border-border/60 pb-4">
                    <span className="flex size-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-primary">
                        <HugeiconsIcon icon={TeachingIcon} size={20} strokeWidth={1.8} aria-hidden="true" />
                    </span>
                    <div>
                        <CardTitle>Daftar Riwayat Pembinaan</CardTitle>
                        <p className="text-xs text-muted-foreground mt-0.5">
                            Menampilkan {filteredHistory.length} data pembinaan yang telah dicatat oleh petugas desa.
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
                                            {item.created_at && (
                                                <p className="text-[11px] text-muted-foreground mt-0.5">
                                                    Dicatat: {item.created_at}
                                                </p>
                                            )}
                                        </td>
                                        <td className="px-4 py-3">
                                            <span className="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] font-semibold text-emerald-800">
                                                <HugeiconsIcon icon={Tick01Icon} size={12} strokeWidth={2.5} />
                                                Selesai
                                            </span>
                                        </td>
                                        <td className="px-4 py-3">
                                            <Button
                                                variant="outline"
                                                size="sm"
                                                onClick={() => setDetailItem(item)}
                                                className="gap-1 text-xs"
                                            >
                                                <HugeiconsIcon icon={ViewIcon} size={14} />
                                                Lihat Detail
                                            </Button>
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
                                                    ? 'Coba atur ulang pilihan filter UMKM atau tanggal pencarian.'
                                                    : 'Daftar riwayat pembinaan akan terisi setelah petugas desa mencatat hasil pembinaan.'}
                                            </p>
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>

            {/* Modal Detail: Riwayat Pembinaan (Read-only) */}
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
                                    <p className="text-xs text-muted-foreground">Laporan hasil kegiatan pembinaan UMKM desa</p>
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
                                        <p className="text-xs font-medium text-slate-500">Program Bantuan</p>
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
                                            {detailItem.created_at && (
                                                <p className="text-xs text-muted-foreground mt-0.5">
                                                    Waktu: {detailItem.created_at} WIB
                                                </p>
                                            )}
                                        </div>
                                    </div>

                                    <div>
                                        <p className="text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                            Deskripsi Pelaksanaan
                                        </p>
                                        <div className="mt-1 rounded-lg border border-slate-200 bg-slate-50/50 p-3 text-sm text-slate-700 leading-relaxed whitespace-pre-line">
                                            {detailItem.description || '-'}
                                        </div>
                                    </div>

                                    <div>
                                        <p className="text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                            Hasil &amp; Capaian (Outcome)
                                        </p>
                                        <div className="mt-1 rounded-lg border border-slate-200 bg-slate-50/50 p-3 text-sm text-slate-700 leading-relaxed whitespace-pre-line">
                                            {detailItem.outcome || 'Tidak ada catatan hasil tambahan.'}
                                        </div>
                                    </div>

                                    <div className="flex items-center justify-between pt-2">
                                        <span className="text-xs text-slate-500">
                                            Status Pelaksanaan:
                                        </span>
                                        <span className="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800">
                                            <HugeiconsIcon icon={Tick01Icon} size={14} strokeWidth={2.5} />
                                            Selesai Dilaksanakan
                                        </span>
                                    </div>
                                </div>

                                <div className="pt-3 border-t border-border">
                                    <Button
                                        type="button"
                                        variant="outline"
                                        onClick={() => setDetailItem(null)}
                                        className="w-full"
                                    >
                                        Tutup
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
