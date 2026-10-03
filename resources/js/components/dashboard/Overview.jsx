import { motion } from 'motion/react';
import {
    Calendar03Icon,
    CheckListIcon,
    FileExportIcon,
    Leaf01Icon,
    MapsLocation01Icon,
    RefreshIcon,
    Tag01Icon,
    Target02Icon,
    TeachingIcon,
} from '@hugeicons/core-free-icons';
import { Icon } from '@/components/landing/shared';
import { Button, buttonVariants } from '@/components/ui/button';
import { Select } from '@/components/ui/select';
import { SegmentedControl } from '@/components/dashboard/Panel';
import { formatNumber } from '@/components/dashboard/lib/metrics';

const ROLE_LABELS = {
    petugas: 'Petugas Desa',
    'kepala-desa': 'Kepala Desa',
};

function greeting(date) {
    const hour = date.getHours();

    if (hour < 11) {
        return 'Selamat pagi';
    }

    if (hour < 15) {
        return 'Selamat siang';
    }

    return hour < 18 ? 'Selamat sore' : 'Selamat malam';
}

export function WelcomeBanner({ role, now, pendingCount, attentionCount, isSampleData }) {
    const isHead = role === 'kepala-desa';
    const dateLabel = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });

    return (
        <section className="relative isolate overflow-hidden rounded-3xl bg-gradient-to-br from-brand-900 via-brand-800 to-brand-600 p-6 text-white sm:p-8 print:bg-none print:p-0 print:text-ink">
            <div aria-hidden className="bg-grid mask-radial absolute inset-0 -z-10 opacity-[0.12] print:hidden" />
            <div aria-hidden className="absolute -top-20 -right-16 -z-10 size-72 rounded-full bg-sun/25 blur-3xl print:hidden" />
            <Icon icon={Leaf01Icon} size={120} strokeWidth={1} className="animate-float-slow absolute -right-4 -bottom-8 -z-10 hidden text-white/10 sm:block print:hidden" />

            <div className="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div className="max-w-2xl">
                    <p className="flex flex-wrap items-center gap-2 text-sm text-brand-100 print:text-muted-foreground">
                        <Icon icon={Calendar03Icon} size={16} />
                        {dateLabel}
                        {isSampleData && (
                            <span className="rounded-md bg-white/15 px-2 py-0.5 text-[11px] font-semibold text-white print:bg-slate-100 print:text-slate-600">
                                Data contoh
                            </span>
                        )}
                    </p>
                    <h1 className="mt-2 text-2xl font-extrabold tracking-tight text-balance sm:text-3xl">
                        {greeting(now)}, {ROLE_LABELS[role]}
                    </h1>
                    <p className="mt-2 text-sm leading-relaxed text-brand-50/90 sm:text-base print:text-muted-foreground">
                        {isHead ? (
                            <>
                                Ada <strong className="font-bold text-sun-200">{formatNumber(attentionCount)} UMKM</strong> berkebutuhan tinggi yang
                                belum punya sesi pembinaan. Pakai ringkasan di bawah untuk menentukan urutan program desa.
                            </>
                        ) : (
                            <>
                                <strong className="font-bold text-sun-200">{formatNumber(pendingCount)} data</strong> menunggu verifikasi dan{' '}
                                <strong className="font-bold text-sun-200">{formatNumber(attentionCount)} UMKM</strong> berkebutuhan tinggi belum dibina.
                            </>
                        )}
                    </p>
                </div>

                <div className="flex flex-wrap gap-2 print:hidden">
                    <a href={isHead ? '#perlu-perhatian' : '#antrean-verifikasi'} className={buttonVariants({ variant: 'light' })}>
                        <Icon icon={isHead ? Target02Icon : CheckListIcon} size={18} />
                        {isHead ? 'Lihat UMKM prioritas' : 'Cek antrean'}
                    </a>
                    <Button variant="outline-light" onClick={() => window.print()}>
                        <Icon icon={FileExportIcon} size={18} />
                        Cetak ringkasan
                    </Button>
                </div>
            </div>
        </section>
    );
}

const PERIOD_OPTIONS = [
    { value: 6, label: '6 bulan' },
    { value: 12, label: '12 bulan' },
];

export function FilterBar({ references, filters, onChange, onReset, shownCount, totalCount }) {
    const hasActiveFilter = filters.hamlet !== 'semua' || filters.businessType !== 'semua';

    return (
        <section
            aria-label="Filter data dashboard"
            className="flex flex-col gap-3 rounded-2xl border border-border bg-white p-3 shadow-xs sm:p-4 lg:flex-row lg:items-center print:hidden"
        >
            <div className="grid flex-1 gap-3 sm:grid-cols-2 lg:max-w-xl">
                <label className="flex flex-col gap-1">
                    <span className="sr-only">Dusun</span>
                    <Select value={filters.hamlet} onChange={(event) => onChange({ hamlet: event.target.value })} className="h-10">
                        <option value="semua">Semua dusun</option>
                        {references.hamlets.map((name) => (
                            <option key={name} value={name}>
                                Dusun {name}
                            </option>
                        ))}
                    </Select>
                </label>
                <label className="flex flex-col gap-1">
                    <span className="sr-only">Jenis usaha</span>
                    <Select value={filters.businessType} onChange={(event) => onChange({ businessType: event.target.value })} className="h-10">
                        <option value="semua">Semua jenis usaha</option>
                        {references.businessTypes.map((type) => (
                            <option key={type.slug} value={type.slug}>
                                {type.name}
                            </option>
                        ))}
                    </Select>
                </label>
            </div>

            <div className="flex flex-wrap items-center justify-between gap-3 lg:ml-auto lg:justify-end">
                <p className="text-xs text-muted-foreground" aria-live="polite">
                    Menampilkan <strong className="font-semibold text-ink">{formatNumber(shownCount)}</strong> dari {formatNumber(totalCount)} UMKM
                </p>
                <div className="flex items-center gap-2">
                    {hasActiveFilter && (
                        <Button variant="ghost" size="sm" onClick={onReset}>
                            <Icon icon={RefreshIcon} size={16} />
                            Reset
                        </Button>
                    )}
                    <SegmentedControl
                        label="Periode tren"
                        options={PERIOD_OPTIONS}
                        value={filters.periodMonths}
                        onChange={(periodMonths) => onChange({ periodMonths })}
                    />
                </div>
            </div>
        </section>
    );
}

export function HeadInsights({ metrics }) {
    const busiestHamlet = [...metrics.hamlets].sort((first, second) => second.highNeed - first.highNeed)[0];
    const topGroup = metrics.grouping[0];
    const overdue = metrics.verificationQueue.filter((record) => record.waitingDays > 7).length;

    const insights = [
        {
            icon: TeachingIcon,
            title: `${metrics.summary.mentored.share}% UMKM terverifikasi sudah dibina`,
            body: `${formatNumber(metrics.needsAttention.length)} UMKM berkebutuhan tinggi belum punya sesi pembinaan.`,
        },
        busiestHamlet &&
            busiestHamlet.highNeed > 0 && {
                icon: MapsLocation01Icon,
                title: `Dusun ${busiestHamlet.name} paling membutuhkan perhatian`,
                body: `${formatNumber(busiestHamlet.highNeed)} dari ${formatNumber(busiestHamlet.total)} UMKM di sana punya kendala tingkat tinggi.`,
            },
        topGroup &&
            topGroup.value > 0 && {
                icon: Tag01Icon,
                title: `Kelompok ${topGroup.label} paling banyak`,
                body: `${formatNumber(topGroup.value)} UMKM punya kendala utama di bidang itu pada asesmen terkini.`,
            },
        {
            icon: CheckListIcon,
            title: overdue > 0 ? `${overdue} data tertahan lebih dari 7 hari` : 'Verifikasi berjalan lancar',
            body: overdue > 0 ? 'Minta petugas menuntaskan antrean agar UMKM bisa segera dianalisis.' : 'Tidak ada data yang menunggu lebih dari seminggu.',
        },
    ].filter(Boolean);

    return (
        <section aria-label="Sorotan untuk Kepala Desa" className="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            {insights.map((insight, index) => (
                <motion.article
                    key={insight.title}
                    initial={{ opacity: 0, y: 12 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.5, delay: 0.05 * index }}
                    className="flex gap-3 rounded-2xl border border-brand-100 bg-brand-50/60 p-4"
                >
                    <span className="grid size-9 shrink-0 place-items-center rounded-xl bg-white text-brand shadow-xs">
                        <Icon icon={insight.icon} size={18} />
                    </span>
                    <div>
                        <h3 className="text-sm font-bold text-ink">{insight.title}</h3>
                        <p className="mt-1 text-xs leading-relaxed text-muted-foreground">{insight.body}</p>
                    </div>
                </motion.article>
            ))}
        </section>
    );
}
