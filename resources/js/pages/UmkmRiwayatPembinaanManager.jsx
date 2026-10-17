import { useState, useMemo } from 'react';
import { HugeiconsIcon } from '@hugeicons/react';
import {
    TeachingIcon,
    Calendar01Icon,
    Calendar03Icon,
    CheckmarkCircle02Icon,
    Clock01Icon,
    Tag01Icon,
    Cancel01Icon,
    Search01Icon,
    ViewIcon,
    Store04Icon,
    User02Icon,
    RotateLeft01Icon,
    InformationCircleIcon,
    ArrowRight02Icon,
} from '@hugeicons/core-free-icons';

import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import PageBackground from '@/components/umkm/PageBackground';
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

export default function UmkmRiwayatPembinaanManager({
    sessions: initialSessions = [],
    business = {},
}) {
    const [sessions] = useState(initialSessions || []);

    // Filters
    const [statusFilter, setStatusFilter] = useState('all'); // 'all' | 'completed' | 'approved'
    const [startDate, setStartDate] = useState('');
    const [endDate, setEndDate] = useState('');
    const [searchQuery, setSearchQuery] = useState('');

    // Detail Modal
    const [selectedSession, setSelectedSession] = useState(null);

    // Counts
    const totalCount = sessions.length;
    const completedCount = useMemo(
        () => sessions.filter((s) => s.status === 'completed').length,
        [sessions]
    );
    const approvedCount = useMemo(
        () => sessions.filter((s) => s.status === 'approved').length,
        [sessions]
    );

    const hasActiveFilter =
        statusFilter !== 'all' || startDate !== '' || endDate !== '' || searchQuery.trim() !== '';

    function resetFilters() {
        setStatusFilter('all');
        setStartDate('');
        setEndDate('');
        setSearchQuery('');
    }

    // Filtered sessions
    const filteredSessions = useMemo(() => {
        return sessions.filter((item) => {
            if (statusFilter !== 'all') {
                if (item.status !== statusFilter) return false;
            }

            const itemDate = item.held_on || item.planned_on;
            if (startDate && itemDate && itemDate < startDate) {
                return false;
            }
            if (endDate && itemDate && itemDate > endDate) {
                return false;
            }

            if (searchQuery.trim()) {
                const q = searchQuery.toLowerCase();
                const match =
                    item.title?.toLowerCase().includes(q) ||
                    item.program_name?.toLowerCase().includes(q) ||
                    item.program_provider?.toLowerCase().includes(q) ||
                    item.planned_activity?.toLowerCase().includes(q) ||
                    item.description?.toLowerCase().includes(q) ||
                    item.outcome?.toLowerCase().includes(q) ||
                    item.recorded_by_name?.toLowerCase().includes(q);
                if (!match) return false;
            }

            return true;
        });
    }, [sessions, statusFilter, startDate, endDate, searchQuery]);

    return (
        <div className="relative min-h-[calc(100vh-4rem)] pb-16">
            <PageBackground />

            <div className="mx-auto max-w-6xl px-4 pt-4 sm:px-6 sm:pt-6">
                {/* Header Banner */}
                <div className="relative overflow-hidden rounded-3xl bg-gradient-to-br from-primary to-brand-hover p-6 text-white shadow-sm sm:p-8">
                    <div className="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                        <div className="space-y-2 max-w-2xl">
                            <div className="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold backdrop-blur-md">
                                <HugeiconsIcon icon={Store04Icon} size={15} />
                                <span>{business.name || 'Usaha Saya'}</span>
                            </div>
                            <h1 className="text-2xl font-extrabold tracking-tight sm:text-3xl">
                                Riwayat Pembinaan Usaha
                            </h1>
                            <p className="text-sm leading-relaxed text-white/85 sm:text-base">
                                Pantau seluruh riwayat pembinaan dan pendampingan yang telah disetujui oleh Kepala Desa maupun yang sudah selesai terlaksana oleh Petugas Desa untuk usaha Anda.
                            </p>
                        </div>

                        <div className="flex shrink-0 items-center gap-3">
                            <a
                                href="/umkm/rekomendasi"
                                className="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-bold text-primary shadow-sm transition hover:bg-white/95"
                            >
                                <HugeiconsIcon icon={Tag01Icon} size={16} />
                                Rekomendasi Program
                            </a>
                        </div>
                    </div>
                </div>

                {/* Stat Cards */}
                <div className="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <Card className="border border-border/80 bg-white shadow-xs">
                        <CardContent className="p-5">
                            <div className="flex items-center justify-between">
                                <span className="text-xs font-medium text-slate-500 uppercase tracking-wider">
                                    Total Pembinaan
                                </span>
                                <div className="grid size-10 place-items-center rounded-xl bg-brand-50 text-brand-hover">
                                    <HugeiconsIcon icon={TeachingIcon} size={20} />
                                </div>
                            </div>
                            <div className="mt-3">
                                <div className="text-3xl font-extrabold text-ink">{totalCount}</div>
                                <p className="mt-1 text-xs text-slate-500">
                                    Program yang disetujui &amp; terlaksana
                                </p>
                            </div>
                        </CardContent>
                    </Card>

                    <Card className="border border-border/80 bg-white shadow-xs">
                        <CardContent className="p-5">
                            <div className="flex items-center justify-between">
                                <span className="text-xs font-medium text-slate-500 uppercase tracking-wider">
                                    Pembinaan Terlaksana
                                </span>
                                <div className="grid size-10 place-items-center rounded-xl bg-emerald-50 text-emerald-600">
                                    <HugeiconsIcon icon={CheckmarkCircle02Icon} size={20} />
                                </div>
                            </div>
                            <div className="mt-3">
                                <div className="text-3xl font-extrabold text-emerald-700">{completedCount}</div>
                                <p className="mt-1 text-xs text-slate-500">
                                    Telah didampingi oleh petugas desa
                                </p>
                            </div>
                        </CardContent>
                    </Card>

                    <Card className="border border-border/80 bg-white shadow-xs">
                        <CardContent className="p-5">
                            <div className="flex items-center justify-between">
                                <span className="text-xs font-medium text-slate-500 uppercase tracking-wider">
                                    Menunggu Pelaksanaan
                                </span>
                                <div className="grid size-10 place-items-center rounded-xl bg-sky-50 text-sky-600">
                                    <HugeiconsIcon icon={Clock01Icon} size={20} />
                                </div>
                            </div>
                            <div className="mt-3">
                                <div className="text-3xl font-extrabold text-sky-700">{approvedCount}</div>
                                <p className="mt-1 text-xs text-slate-500">
                                    Disetujui, menunggu jadwal kegiatan
                                </p>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                {/* Filter and Search Bar */}
                <div className="mt-6 rounded-2xl border border-border/80 bg-white p-4 shadow-xs">
                    <div className="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        {/* Status Filter Tabs */}
                        <div className="flex flex-wrap items-center gap-1.5">
                            <button
                                type="button"
                                onClick={() => setStatusFilter('all')}
                                className={cn(
                                    'rounded-xl px-3.5 py-2 text-xs font-semibold transition',
                                    statusFilter === 'all'
                                        ? 'bg-brand text-white shadow-xs'
                                        : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
                                )}
                            >
                                Semua ({totalCount})
                            </button>
                            <button
                                type="button"
                                onClick={() => setStatusFilter('completed')}
                                className={cn(
                                    'inline-flex items-center gap-1.5 rounded-xl px-3.5 py-2 text-xs font-semibold transition',
                                    statusFilter === 'completed'
                                        ? 'bg-emerald-600 text-white shadow-xs'
                                        : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
                                )}
                            >
                                <span className="size-2 rounded-full bg-emerald-400" />
                                Terlaksana ({completedCount})
                            </button>
                            <button
                                type="button"
                                onClick={() => setStatusFilter('approved')}
                                className={cn(
                                    'inline-flex items-center gap-1.5 rounded-xl px-3.5 py-2 text-xs font-semibold transition',
                                    statusFilter === 'approved'
                                        ? 'bg-sky-600 text-white shadow-xs'
                                        : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
                                )}
                            >
                                <span className="size-2 rounded-full bg-sky-400" />
                                Disetujui ({approvedCount})
                            </button>
                        </div>

                        {/* Search and Date Filter */}
                        <div className="flex flex-wrap items-center gap-2">
                            {/* Search Input */}
                            <div className="relative min-w-[220px] flex-1 sm:w-64">
                                <HugeiconsIcon
                                    icon={Search01Icon}
                                    size={16}
                                    className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"
                                />
                                <Input
                                    type="text"
                                    placeholder="Cari program / topik..."
                                    value={searchQuery}
                                    onChange={(e) => setSearchQuery(e.target.value)}
                                    className="h-9 pl-9 pr-3 text-xs"
                                />
                            </div>

                            {/* Date range filter */}
                            <div className="flex items-center gap-1.5 text-xs text-slate-500">
                                <HugeiconsIcon icon={Calendar03Icon} size={15} className="text-slate-400" />
                                <Input
                                    type="date"
                                    value={startDate}
                                    onChange={(e) => setStartDate(e.target.value)}
                                    className="h-9 w-32 px-2 text-xs"
                                    title="Tanggal mulai"
                                />
                                <span>-</span>
                                <Input
                                    type="date"
                                    value={endDate}
                                    onChange={(e) => setEndDate(e.target.value)}
                                    className="h-9 w-32 px-2 text-xs"
                                    title="Tanggal akhir"
                                />
                            </div>

                            {/* Reset filter button */}
                            {hasActiveFilter && (
                                <Button
                                    type="button"
                                    variant="ghost"
                                    size="sm"
                                    onClick={resetFilters}
                                    className="h-9 text-xs text-slate-600 hover:text-ink"
                                >
                                    <HugeiconsIcon icon={RotateLeft01Icon} size={14} className="mr-1" />
                                    Reset
                                </Button>
                            )}
                        </div>
                    </div>
                </div>

                {/* Content Section */}
                <div className="mt-6">
                    {sessions.length === 0 ? (
                        /* Initial empty state when no coaching history exists */
                        <Card className="border border-dashed border-border/80 bg-white py-12 text-center shadow-xs">
                            <CardContent className="mx-auto flex max-w-md flex-col items-center justify-center p-6">
                                <div className="grid size-16 place-items-center rounded-2xl bg-brand-50 text-brand">
                                    <HugeiconsIcon icon={TeachingIcon} size={32} />
                                </div>
                                <h3 className="mt-4 text-lg font-bold text-ink">
                                    Belum Ada Riwayat Pembinaan
                                </h3>
                                <p className="mt-2 text-sm leading-relaxed text-slate-500">
                                    Saat ini belum ada tindak lanjut pembinaan yang disetujui atau terlaksana untuk usaha Anda. Daftarkan minat pada program bantuan yang direkomendasikan agar petugas desa dapat menjadwalkan pembinaan.
                                </p>
                                <div className="mt-6">
                                    <a
                                        href="/umkm/rekomendasi"
                                        className="inline-flex items-center gap-2 rounded-xl bg-brand px-5 py-2.5 text-sm font-semibold text-white shadow-xs transition hover:bg-brand-hover"
                                    >
                                        <HugeiconsIcon icon={Tag01Icon} size={16} />
                                        Lihat Rekomendasi Program
                                    </a>
                                </div>
                            </CardContent>
                        </Card>
                    ) : filteredSessions.length === 0 ? (
                        /* Empty state after filtering */
                        <Card className="border border-dashed border-border/80 bg-white py-12 text-center shadow-xs">
                            <CardContent className="mx-auto flex max-w-md flex-col items-center justify-center p-6">
                                <div className="grid size-14 place-items-center rounded-2xl bg-slate-100 text-slate-400">
                                    <HugeiconsIcon icon={Search01Icon} size={28} />
                                </div>
                                <h3 className="mt-4 text-base font-bold text-ink">
                                    Tidak Ada Data yang Cocok
                                </h3>
                                <p className="mt-1 text-sm text-slate-500">
                                    Tidak ditemukan pembinaan yang sesuai dengan filter atau kata kunci yang Anda masukkan.
                                </p>
                                <div className="mt-5">
                                    <Button
                                        type="button"
                                        variant="outline"
                                        size="sm"
                                        onClick={resetFilters}
                                        className="rounded-xl text-xs"
                                    >
                                        <HugeiconsIcon icon={RotateLeft01Icon} size={14} className="mr-1.5" />
                                        Reset Semua Filter
                                    </Button>
                                </div>
                            </CardContent>
                        </Card>
                    ) : (
                        /* Sessions List */
                        <div className="grid grid-cols-1 gap-4">
                            {filteredSessions.map((session) => {
                                const isCompleted = session.status === 'completed';
                                const displayDate = isCompleted
                                    ? formatDate(session.held_on)
                                    : formatDate(session.planned_on);

                                return (
                                    <motion.div
                                        key={session.id}
                                        initial={{ opacity: 0, y: 8 }}
                                        animate={{ opacity: 1, y: 0 }}
                                        transition={{ duration: 0.2 }}
                                    >
                                        <Card className="group relative overflow-hidden border border-border/80 bg-white p-5 shadow-xs transition-all hover:border-brand-200 hover:shadow-md">
                                            <div className="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                                                {/* Left Details */}
                                                <div className="space-y-2.5 min-w-0 flex-1">
                                                    {/* Badges & Date */}
                                                    <div className="flex flex-wrap items-center gap-2">
                                                        {isCompleted ? (
                                                            <span className="inline-flex items-center gap-1.5 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                                                                <HugeiconsIcon icon={CheckmarkCircle02Icon} size={14} />
                                                                Terlaksana
                                                            </span>
                                                        ) : (
                                                            <span className="inline-flex items-center gap-1.5 rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-xs font-semibold text-sky-700">
                                                                <HugeiconsIcon icon={Clock01Icon} size={14} />
                                                                Disetujui (Menunggu Pelaksanaan)
                                                            </span>
                                                        )}

                                                        <span className="inline-flex items-center gap-1.5 text-xs text-slate-500">
                                                            <HugeiconsIcon icon={Calendar01Icon} size={14} className="text-slate-400" />
                                                            {isCompleted ? 'Pelaksanaan:' : 'Rencana:'} <strong className="font-semibold text-ink">{displayDate}</strong>
                                                        </span>

                                                        <span className="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">
                                                            <HugeiconsIcon icon={Tag01Icon} size={13} className="text-slate-400" />
                                                            {session.program_name}
                                                        </span>
                                                    </div>

                                                    {/* Title & Activity */}
                                                    <div>
                                                        <h3 className="text-base font-bold text-ink sm:text-lg">
                                                            {session.title || session.planned_activity}
                                                        </h3>
                                                        <p className="mt-1 line-clamp-2 text-xs leading-relaxed text-slate-600 sm:text-sm">
                                                            {isCompleted
                                                                ? session.outcome || session.description || 'Pembinaan telah selesai dilaksanakan oleh petugas desa.'
                                                                : session.planned_activity || 'Pembinaan telah disetujui oleh Kepala Desa dan siap dilaksanakan.'}
                                                        </p>
                                                    </div>

                                                    {/* Footer Meta */}
                                                    <div className="flex flex-wrap items-center gap-y-1 gap-x-4 pt-1 text-xs text-slate-500">
                                                        <span className="inline-flex items-center gap-1">
                                                            <HugeiconsIcon icon={User02Icon} size={13} className="text-slate-400" />
                                                            Pendamping:{' '}
                                                            <span className="font-semibold text-slate-700">
                                                                {session.recorded_by_name}
                                                            </span>
                                                        </span>

                                                        {session.decision_note && (
                                                            <span className="inline-flex items-center gap-1 text-slate-500">
                                                                <HugeiconsIcon icon={InformationCircleIcon} size={13} className="text-sky-500" />
                                                                Catatan Kades:{' '}
                                                                <span className="italic text-slate-600">
                                                                    "{session.decision_note}"
                                                                </span>
                                                            </span>
                                                        )}
                                                    </div>
                                                </div>

                                                {/* Right CTA */}
                                                <div className="flex shrink-0 items-center lg:pl-4">
                                                    <Button
                                                        type="button"
                                                        onClick={() => setSelectedSession(session)}
                                                        variant="outline"
                                                        className="w-full rounded-xl border-slate-200 text-xs font-semibold text-slate-700 hover:border-brand hover:bg-brand-50 hover:text-brand-hover lg:w-auto"
                                                    >
                                                        <HugeiconsIcon icon={ViewIcon} size={15} className="mr-1.5" />
                                                        Lihat Rincian
                                                    </Button>
                                                </div>
                                            </div>
                                        </Card>
                                    </motion.div>
                                );
                            })}
                        </div>
                    )}
                </div>
            </div>

            {/* Detail Modal */}
            <AnimatePresence>
                {selectedSession && (
                    <div
                        className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-ink/50 backdrop-blur-sm"
                        role="dialog"
                        aria-modal="true"
                        aria-labelledby="modal-pembinaan-title"
                    >
                        <motion.div
                            initial={{ opacity: 0, scale: 0.95, y: 10 }}
                            animate={{ opacity: 1, scale: 1, y: 0 }}
                            exit={{ opacity: 0, scale: 0.95, y: 10 }}
                            transition={{ duration: 0.18 }}
                            className="relative flex max-h-[90vh] w-full max-w-2xl flex-col rounded-3xl bg-white shadow-2xl"
                        >
                            {/* Modal Header */}
                            <div className="flex items-start justify-between border-b border-border/80 px-6 py-5">
                                <div className="space-y-1">
                                    <div className="flex flex-wrap items-center gap-2">
                                        {selectedSession.status === 'completed' ? (
                                            <span className="inline-flex items-center gap-1.5 rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">
                                                <HugeiconsIcon icon={CheckmarkCircle02Icon} size={13} />
                                                Terlaksana
                                            </span>
                                        ) : (
                                            <span className="inline-flex items-center gap-1.5 rounded-full border border-sky-200 bg-sky-50 px-2.5 py-0.5 text-xs font-semibold text-sky-700">
                                                <HugeiconsIcon icon={Clock01Icon} size={13} />
                                                Disetujui
                                            </span>
                                        )}
                                        <span className="text-xs font-medium text-slate-500">
                                            {selectedSession.program_name}
                                        </span>
                                    </div>
                                    <h2
                                        id="modal-pembinaan-title"
                                        className="text-xl font-extrabold tracking-tight text-ink"
                                    >
                                        {selectedSession.title || selectedSession.planned_activity}
                                    </h2>
                                </div>
                                <button
                                    type="button"
                                    onClick={() => setSelectedSession(null)}
                                    className="grid size-9 place-items-center rounded-xl text-slate-400 hover:bg-slate-100 hover:text-slate-700"
                                    aria-label="Tutup jendela"
                                >
                                    <HugeiconsIcon icon={Cancel01Icon} size={20} />
                                </button>
                            </div>

                            {/* Modal Body */}
                            <div className="flex-1 overflow-y-auto px-6 py-5 space-y-5 text-sm text-slate-700">
                                {/* Program Information Card */}
                                <div className="rounded-2xl border border-slate-100 bg-slate-50/70 p-4 space-y-2">
                                    <div className="flex items-center justify-between text-xs font-bold uppercase tracking-wider text-slate-500">
                                        <span>Informasi Program Bantuan</span>
                                        <span className="text-slate-400">{selectedSession.program_provider}</span>
                                    </div>
                                    <div className="text-base font-bold text-ink">
                                        {selectedSession.program_name}
                                    </div>
                                    {selectedSession.program_description && (
                                        <p className="text-xs leading-relaxed text-slate-600">
                                            {selectedSession.program_description}
                                        </p>
                                    )}
                                </div>

                                {/* Schedule & Officer Info */}
                                <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                    <div className="rounded-2xl border border-border/80 bg-white p-3.5 space-y-1">
                                        <span className="text-xs font-medium text-slate-500">
                                            {selectedSession.status === 'completed'
                                                ? 'Tanggal Pelaksanaan'
                                                : 'Rencana Pelaksanaan'}
                                        </span>
                                        <div className="flex items-center gap-1.5 font-bold text-ink">
                                            <HugeiconsIcon icon={Calendar01Icon} size={16} className="text-brand" />
                                            <span>
                                                {formatDate(
                                                    selectedSession.status === 'completed'
                                                        ? selectedSession.held_on
                                                        : selectedSession.planned_on
                                                )}
                                            </span>
                                        </div>
                                    </div>

                                    <div className="rounded-2xl border border-border/80 bg-white p-3.5 space-y-1">
                                        <span className="text-xs font-medium text-slate-500">
                                            Petugas Pendamping
                                        </span>
                                        <div className="flex items-center gap-1.5 font-bold text-ink">
                                            <HugeiconsIcon icon={User02Icon} size={16} className="text-brand" />
                                            <span>{selectedSession.recorded_by_name}</span>
                                        </div>
                                    </div>
                                </div>

                                {/* Detailed Content */}
                                <div className="space-y-4">
                                    {/* Description / Summary */}
                                    <div className="space-y-1.5">
                                        <h4 className="text-xs font-bold uppercase tracking-wider text-slate-500">
                                            {selectedSession.status === 'completed'
                                                ? 'Materi & Deskripsi Pembinaan'
                                                : 'Rencana Kegiatan Pembinaan'}
                                        </h4>
                                        <div className="rounded-2xl border border-border/80 bg-slate-50/50 p-4 text-sm leading-relaxed text-slate-800 whitespace-pre-wrap">
                                            {selectedSession.description || selectedSession.planned_activity || '-'}
                                        </div>
                                    </div>

                                    {/* Outcome (for completed sessions) */}
                                    {selectedSession.status === 'completed' && selectedSession.outcome && (
                                        <div className="space-y-1.5">
                                            <h4 className="text-xs font-bold uppercase tracking-wider text-emerald-700">
                                                Hasil &amp; Dampak Pembinaan
                                            </h4>
                                            <div className="rounded-2xl border border-emerald-200 bg-emerald-50/60 p-4 text-sm leading-relaxed text-emerald-900 whitespace-pre-wrap">
                                                {selectedSession.outcome}
                                            </div>
                                        </div>
                                    )}

                                    {/* Decision Note from Village Head */}
                                    {selectedSession.decision_note && (
                                        <div className="space-y-1.5">
                                            <h4 className="text-xs font-bold uppercase tracking-wider text-sky-700">
                                                Catatan Persetujuan Kepala Desa
                                            </h4>
                                            <div className="rounded-2xl border border-sky-200 bg-sky-50/60 p-4 text-sm leading-relaxed text-sky-900">
                                                <p className="italic">"{selectedSession.decision_note}"</p>
                                                {selectedSession.decided_at && (
                                                    <p className="mt-2 text-xs font-medium text-sky-600">
                                                        Disetujui pada {formatDate(selectedSession.decided_at)} oleh {selectedSession.decider_name}
                                                    </p>
                                                )}
                                            </div>
                                        </div>
                                    )}
                                </div>
                            </div>

                            {/* Modal Footer */}
                            <div className="flex items-center justify-end border-t border-border/80 px-6 py-4 bg-slate-50/50 rounded-b-3xl">
                                <Button
                                    type="button"
                                    onClick={() => setSelectedSession(null)}
                                    className="rounded-xl px-5 py-2 text-xs font-semibold"
                                >
                                    Tutup
                                </Button>
                            </div>
                        </motion.div>
                    </div>
                )}
            </AnimatePresence>
        </div>
    );
}
