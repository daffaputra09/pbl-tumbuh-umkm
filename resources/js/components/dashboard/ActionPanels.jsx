import { useMemo, useState } from 'react';
import { Alert02Icon, Clock01Icon, Location01Icon, Search01Icon } from '@hugeicons/core-free-icons';
import { Icon } from '@/components/landing/shared';
import { getObstacleIcon } from '@/components/dashboard/ObstaclePanel';
import { EmptyState, Panel } from '@/components/dashboard/Panel';
import { formatDate, formatNumber } from '@/components/dashboard/lib/metrics';
import { cn } from '@/lib/utils';

const INITIAL_VISIBLE_ROWS = 6;

function matchesSearch(record, query) {
    const keyword = query.trim().toLowerCase();
    const businessName = (record.businessName || '').toLowerCase();
    const hamlet = (record.hamlet || '').toLowerCase();

    return keyword === '' || businessName.includes(keyword) || hamlet.includes(keyword);
}

function ScoreBadge({ score }) {
    return (
        <span
            className={cn(
                'inline-flex min-w-11 justify-center rounded-md px-2 py-0.5 text-xs font-bold tabular-nums',
                score >= 85 ? 'bg-rose-50 text-rose-700' : 'bg-sun-50 text-sun-700',
            )}
        >
            {score}
        </span>
    );
}

function ShowMoreButton({ isExpanded, hiddenCount, onToggle }) {
    return (
        <button
            type="button"
            onClick={onToggle}
            className="mt-3 w-full rounded-xl border border-dashed border-border py-2.5 text-sm font-semibold text-brand-hover transition-colors hover:border-brand-200 hover:bg-brand-50"
        >
            {isExpanded ? 'Tampilkan lebih sedikit' : `Tampilkan ${hiddenCount} lainnya`}
        </button>
    );
}

export function AttentionTable({ records = [], obstacleLabels = {}, className }) {
    const safeRecords = Array.isArray(records) ? records : [];
    const safeObstacleLabels = obstacleLabels || {};
    const [query, setQuery] = useState('');
    const filtered = useMemo(() => safeRecords.filter((record) => matchesSearch(record, query)), [safeRecords, query]);

    return (
        <Panel
            id="perlu-perhatian"
            className={cn('h-[480px]', className)}
            title="UMKM yang perlu segera dibina"
            description={`${formatNumber(safeRecords.length)} UMKM aktif berkebutuhan tinggi dan belum punya sesi pembinaan.`}
            action={
                <label className="relative w-full sm:w-60">
                    <span className="sr-only">Cari nama usaha atau dusun</span>
                    <Icon icon={Search01Icon} size={17} className="pointer-events-none absolute top-1/2 left-3 -translate-y-1/2 text-slate-400" />
                    <input
                        type="search"
                        value={query}
                        onChange={(event) => setQuery(event.target.value)}
                        placeholder="Cari usaha atau dusun"
                        className="h-10 w-full rounded-xl border border-border bg-white pr-3 pl-9 text-sm text-ink placeholder:text-slate-400 focus-visible:border-primary focus-visible:ring-[3px] focus-visible:ring-ring/25 focus-visible:outline-none"
                    />
                </label>
            }
        >
            {filtered.length === 0 ? (
                <EmptyState message={records.length === 0 ? 'Semua UMKM berkebutuhan tinggi sudah dibina.' : 'Tidak ada usaha yang cocok dengan pencarian.'} />
            ) : (
                <div className="flex-1 min-h-0 overflow-y-auto pr-1.5 [scrollbar-width:thin] [scrollbar-color:#cbd5e1_transparent]">
                    <div className="hidden md:block">
                        <table className="w-full text-left text-sm">
                            <thead className="sticky top-0 z-10 border-b border-border bg-white text-xs font-semibold text-muted-foreground shadow-[0_1px_0_0_#e2e8f0]">
                                <tr>
                                    <th scope="col" className="bg-white py-3 pr-4 font-semibold">Usaha</th>
                                    <th scope="col" className="bg-white py-3 pr-4 font-semibold">Dusun</th>
                                    <th scope="col" className="bg-white py-3 pr-4 font-semibold">Kendala utama</th>
                                    <th scope="col" className="bg-white py-3 text-right font-semibold">Skor</th>
                                    <th scope="col" className="bg-white py-3 pl-4 text-right font-semibold">Terdaftar</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-border">
                                {filtered.map((record) => (
                                    <tr key={record.id} className="transition-colors hover:bg-slate-50/80">
                                        <td className="py-3 pr-4">
                                            <p className="font-semibold text-ink">{record.businessName}</p>
                                            <p className="text-xs text-muted-foreground">{record.employeeCount} tenaga kerja</p>
                                        </td>
                                        <td className="py-3 pr-4 text-slate-600">{record.hamlet || '-'}</td>
                                        <td className="py-3 pr-4">
                                            <span className="inline-flex items-center gap-2 text-slate-700">
                                                <Icon icon={getObstacleIcon(record.dominant?.slug)} size={16} className="text-brand" />
                                                {(record.dominant?.slug && obstacleLabels[record.dominant.slug]) || 'Kendala Utama'}
                                            </span>
                                        </td>
                                        <td className="py-3 text-right">
                                            <ScoreBadge score={Math.round(record.dominant?.score || 0)} />
                                        </td>
                                        <td className="py-3 pl-4 text-right whitespace-nowrap text-slate-600">{formatDate(record.createdAt)}</td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>

                    <ul className="flex flex-col gap-2 md:hidden">
                        {filtered.map((record) => (
                            <li key={record.id} className="rounded-xl border border-border p-3.5">
                                <div className="flex items-start justify-between gap-3">
                                    <div className="min-w-0">
                                        <p className="truncate font-semibold text-ink">{record.businessName}</p>
                                        <p className="mt-0.5 flex items-center gap-1 text-xs text-muted-foreground">
                                            <Icon icon={Location01Icon} size={13} />
                                            {record.hamlet || '-'} · {record.employeeCount} tenaga kerja
                                        </p>
                                    </div>
                                    <ScoreBadge score={Math.round(record.dominant?.score || 0)} />
                                </div>
                                <p className="mt-2.5 inline-flex items-center gap-2 rounded-lg bg-brand-50 px-2.5 py-1 text-xs font-medium text-brand-hover">
                                    <Icon icon={getObstacleIcon(record.dominant?.slug)} size={14} />
                                    Kendala utama: {(record.dominant?.slug && obstacleLabels[record.dominant.slug]) || 'Kendala Utama'}
                                </p>
                            </li>
                        ))}
                    </ul>
                </div>
            )}
        </Panel>
    );
}

export function VerificationQueue({ records = [], role, followUpQueue = [], routes = {}, className }) {
    const isHead = role === 'kepala-desa';
    const safeQueue = Array.isArray(followUpQueue) ? followUpQueue : [];
    const safeRecords = Array.isArray(records) ? records : [];
    const safeRoutes = routes || {};
    const queueData = isHead ? safeQueue : safeRecords;
    const overdueCount = queueData.filter((record) => (record.waitingDays ?? record.usia_hari ?? 0) > 7).length;

    return (
        <Panel
            id="antrean-verifikasi"
            className={cn('h-[480px]', className)}
            title={isHead ? 'Antrean Persetujuan Tindak Lanjut' : 'Antrean verifikasi'}
            description={
                isHead
                    ? (overdueCount > 0
                        ? `${overdueCount} pengajuan menunggu lebih dari 7 hari.`
                        : `${queueData.length} pengajuan berstatus pending menunggu persetujuan.`)
                    : (overdueCount > 0
                        ? `${overdueCount} data sudah menunggu lebih dari 7 hari.`
                        : 'Data paling lama menunggu tampil di atas.')
            }
            action={
                isHead ? (
                    <a
                        href={safeRoutes.pimpinanFollowUps || '/pimpinan/persetujuan-tindak-lanjut'}
                        className="inline-flex items-center gap-1 text-xs font-semibold text-brand hover:text-brand-hover hover:underline"
                    >
                        Buka persetujuan ({queueData.length}) →
                    </a>
                ) : (
                    <a
                        href="/petugas/umkm"
                        className="inline-flex items-center gap-1 text-xs font-semibold text-brand hover:text-brand-hover hover:underline"
                    >
                        Buka verifikasi ({queueData.length}) →
                    </a>
                )
            }
        >
            {queueData.length === 0 ? (
                <EmptyState
                    message={
                        isHead
                            ? 'Tidak ada pengajuan tindak lanjut yang menunggu persetujuan.'
                            : 'Tidak ada data yang menunggu verifikasi.'
                    }
                />
            ) : (
                <div className="flex-1 min-h-0 overflow-y-auto pr-1.5 [scrollbar-width:thin] [scrollbar-color:#cbd5e1_transparent]">
                    <ul className="flex flex-col divide-y divide-border">
                        {queueData.map((record) => {
                            const waitingDays = record.waitingDays ?? record.usia_hari ?? 0;
                            const isOverdue = waitingDays > 7;

                            if (isHead) {
                                return (
                                    <li key={record.id} className="group/queue flex items-center justify-between gap-3 py-3 first:pt-0">
                                        <div className="flex min-w-0 items-center gap-3">
                                            <span
                                                className={cn(
                                                    'grid size-9 shrink-0 place-items-center rounded-xl',
                                                    isOverdue ? 'bg-rose-50 text-rose-600' : 'bg-sun-50 text-sun',
                                                )}
                                            >
                                                <Icon icon={isOverdue ? Alert02Icon : Clock01Icon} size={18} />
                                            </span>
                                            <div className="min-w-0">
                                                <div className="flex items-center gap-2">
                                                    <p className="truncate text-sm font-semibold text-ink">
                                                        {record.businessName || record.nama_usaha}
                                                    </p>
                                                    <span className="rounded bg-amber-50 px-1.5 py-0.5 text-[10px] font-semibold text-amber-700">
                                                        Pending
                                                    </span>
                                                </div>
                                                <p className="truncate text-xs text-muted-foreground">
                                                    {record.programName || record.plannedActivity || record.rencana_kegiatan || 'Program Pendampingan'} · Dusun {record.hamlet || record.dusun || '-'}
                                                </p>
                                                <p className="text-[11px] text-slate-400">
                                                    Diajukan oleh {record.submittedBy || record.diajukan_oleh || 'Petugas Desa'}
                                                </p>
                                            </div>
                                        </div>
                                        <div className="flex shrink-0 items-center gap-2">
                                            <span
                                                className={cn(
                                                    'rounded-md px-2 py-0.5 text-xs font-semibold whitespace-nowrap tabular-nums',
                                                    isOverdue ? 'bg-rose-50 text-rose-700' : 'bg-slate-100 text-slate-600',
                                                )}
                                            >
                                                {waitingDays === 0 ? 'Hari ini' : `${waitingDays} hari`}
                                            </span>
                                            <a
                                                href={safeRoutes.pimpinanFollowUps || '/pimpinan/persetujuan-tindak-lanjut'}
                                                className="inline-flex size-7 items-center justify-center rounded-lg border border-border text-slate-500 hover:border-brand-300 hover:bg-brand-50 hover:text-brand"
                                                title="Buka persetujuan"
                                            >
                                                <span className="text-xs font-bold">→</span>
                                            </a>
                                        </div>
                                    </li>
                                );
                            }

                            const needsFix = record.verificationStatus === 'needs_revision';

                            return (
                                <li key={record.id} className="flex items-center gap-3 py-3 first:pt-0">
                                    <span
                                        className={cn(
                                            'grid size-9 shrink-0 place-items-center rounded-xl',
                                            needsFix ? 'bg-rose-50 text-rose-600' : 'bg-sun-50 text-sun',
                                        )}
                                    >
                                        <Icon icon={needsFix ? Alert02Icon : Clock01Icon} size={18} />
                                    </span>
                                    <div className="min-w-0 flex-1">
                                        <p className="truncate text-sm font-semibold text-ink">{record.businessName}</p>
                                        <p className="text-xs text-muted-foreground">
                                            {needsFix ? 'Perlu perbaikan' : 'Menunggu dicek'} · {record.hamlet || 'Desa Junrejo'}
                                        </p>
                                    </div>
                                    <span
                                        className={cn(
                                            'shrink-0 rounded-md px-2 py-0.5 text-xs font-semibold whitespace-nowrap tabular-nums',
                                            isOverdue ? 'bg-rose-50 text-rose-700' : 'bg-slate-100 text-slate-600',
                                        )}
                                    >
                                        {waitingDays === 0 ? 'Hari ini' : `${waitingDays} hari`}
                                    </span>
                                </li>
                            );
                        })}
                    </ul>
                </div>
            )}
        </Panel>
    );
}
