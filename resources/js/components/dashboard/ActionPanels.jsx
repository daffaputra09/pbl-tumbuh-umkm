import { useMemo, useState } from 'react';
import { Alert02Icon, Clock01Icon, Location01Icon, Search01Icon } from '@hugeicons/core-free-icons';
import { Icon } from '@/components/landing/shared';
import { OBSTACLE_ICONS } from '@/components/dashboard/ObstaclePanel';
import { EmptyState, Panel } from '@/components/dashboard/Panel';
import { formatDate, formatNumber } from '@/components/dashboard/lib/metrics';
import { cn } from '@/lib/utils';

const INITIAL_VISIBLE_ROWS = 6;

function matchesSearch(record, query) {
    const keyword = query.trim().toLowerCase();

    return keyword === '' || record.businessName.toLowerCase().includes(keyword) || record.hamlet.toLowerCase().includes(keyword);
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

export function AttentionTable({ records, obstacleLabels }) {
    const [query, setQuery] = useState('');
    const [isExpanded, setIsExpanded] = useState(false);
    const filtered = useMemo(() => records.filter((record) => matchesSearch(record, query)), [records, query]);
    const visible = isExpanded ? filtered : filtered.slice(0, INITIAL_VISIBLE_ROWS);

    return (
        <Panel
            id="perlu-perhatian"
            title="UMKM yang perlu segera dibina"
            description={`${formatNumber(records.length)} UMKM aktif berkebutuhan tinggi dan belum punya sesi pembinaan.`}
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
                <>
                    <div className="hidden overflow-x-auto md:block">
                        <table className="w-full text-left text-sm">
                            <thead>
                                <tr className="border-b border-border text-xs font-semibold text-muted-foreground">
                                    <th scope="col" className="pb-3 font-semibold">Usaha</th>
                                    <th scope="col" className="pb-3 font-semibold">Dusun</th>
                                    <th scope="col" className="pb-3 font-semibold">Kendala utama</th>
                                    <th scope="col" className="pb-3 text-right font-semibold">Skor</th>
                                    <th scope="col" className="pb-3 text-right font-semibold">Terdaftar</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-border">
                                {visible.map((record) => (
                                    <tr key={record.id} className="transition-colors hover:bg-slate-50/80">
                                        <td className="py-3 pr-4">
                                            <p className="font-semibold text-ink">{record.businessName}</p>
                                            <p className="text-xs text-muted-foreground">{record.employeeCount} tenaga kerja</p>
                                        </td>
                                        <td className="py-3 pr-4 text-slate-600">{record.hamlet}</td>
                                        <td className="py-3 pr-4">
                                            <span className="inline-flex items-center gap-2 text-slate-700">
                                                <Icon icon={OBSTACLE_ICONS[record.dominant.slug]} size={16} className="text-brand" />
                                                {obstacleLabels[record.dominant.slug]}
                                            </span>
                                        </td>
                                        <td className="py-3 text-right">
                                            <ScoreBadge score={Math.round(record.dominant.score)} />
                                        </td>
                                        <td className="py-3 pl-4 text-right whitespace-nowrap text-slate-600">{formatDate(record.createdAt)}</td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>

                    <ul className="flex flex-col gap-2 md:hidden">
                        {visible.map((record) => (
                            <li key={record.id} className="rounded-xl border border-border p-3.5">
                                <div className="flex items-start justify-between gap-3">
                                    <div className="min-w-0">
                                        <p className="truncate font-semibold text-ink">{record.businessName}</p>
                                        <p className="mt-0.5 flex items-center gap-1 text-xs text-muted-foreground">
                                            <Icon icon={Location01Icon} size={13} />
                                            {record.hamlet} · {record.employeeCount} tenaga kerja
                                        </p>
                                    </div>
                                    <ScoreBadge score={Math.round(record.dominant.score)} />
                                </div>
                                <p className="mt-2.5 inline-flex items-center gap-2 rounded-lg bg-brand-50 px-2.5 py-1 text-xs font-medium text-brand-hover">
                                    <Icon icon={OBSTACLE_ICONS[record.dominant.slug]} size={14} />
                                    Kendala utama: {obstacleLabels[record.dominant.slug]}
                                </p>
                            </li>
                        ))}
                    </ul>

                    {filtered.length > INITIAL_VISIBLE_ROWS && (
                        <ShowMoreButton
                            isExpanded={isExpanded}
                            hiddenCount={filtered.length - INITIAL_VISIBLE_ROWS}
                            onToggle={() => setIsExpanded((current) => !current)}
                        />
                    )}
                </>
            )}
        </Panel>
    );
}

export function VerificationQueue({ records }) {
    const [isExpanded, setIsExpanded] = useState(false);
    const visible = isExpanded ? records : records.slice(0, INITIAL_VISIBLE_ROWS);
    const overdueCount = records.filter((record) => record.waitingDays > 7).length;

    return (
        <Panel
            id="antrean-verifikasi"
            title="Antrean verifikasi"
            description={overdueCount > 0 ? `${overdueCount} data sudah menunggu lebih dari 7 hari.` : 'Data paling lama menunggu tampil di atas.'}
        >
            {records.length === 0 ? (
                <EmptyState message="Tidak ada data yang menunggu verifikasi." />
            ) : (
                <>
                    <ul className="flex flex-col divide-y divide-border">
                        {visible.map((record) => {
                            const isOverdue = record.waitingDays > 7;
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
                                            {needsFix ? 'Perlu perbaikan' : 'Menunggu dicek'} · {record.hamlet}
                                        </p>
                                    </div>
                                    <span
                                        className={cn(
                                            'shrink-0 rounded-md px-2 py-0.5 text-xs font-semibold whitespace-nowrap tabular-nums',
                                            isOverdue ? 'bg-rose-50 text-rose-700' : 'bg-slate-100 text-slate-600',
                                        )}
                                    >
                                        {record.waitingDays === 0 ? 'Hari ini' : `${record.waitingDays} hari`}
                                    </span>
                                </li>
                            );
                        })}
                    </ul>

                    {records.length > INITIAL_VISIBLE_ROWS && (
                        <ShowMoreButton
                            isExpanded={isExpanded}
                            hiddenCount={records.length - INITIAL_VISIBLE_ROWS}
                            onToggle={() => setIsExpanded((current) => !current)}
                        />
                    )}
                </>
            )}
        </Panel>
    );
}
