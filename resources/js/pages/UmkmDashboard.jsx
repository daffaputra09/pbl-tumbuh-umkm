import { HugeiconsIcon } from '@hugeicons/react';
import {
    Calendar03Icon,
    Cancel01Icon,
    CheckmarkCircle02Icon,
    Idea01Icon,
    Notification01Icon,
    Tag01Icon,
} from '@hugeicons/core-free-icons';
import { buttonVariants } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { cn } from '@/lib/utils';

const VERIFICATION_META = {
    pending: {
        label: 'Menunggu Cek',
        description: 'Data usaha kamu sedang ditinjau petugas desa.',
        badgeClass: 'bg-sun-50 text-sun-700',
        icon: Notification01Icon,
    },
    verified: {
        label: 'Terverifikasi',
        description: 'Data usaha kamu sudah lengkap dan aktif.',
        badgeClass: 'bg-success/10 text-success',
        icon: CheckmarkCircle02Icon,
    },
    needs_revision: {
        label: 'Perlu Diperbaiki',
        description: 'Ada bagian data usaha yang perlu kamu lengkapi atau perbaiki lagi.',
        badgeClass: 'bg-destructive/10 text-destructive',
        icon: Notification01Icon,
    },
    rejected: {
        label: 'Ditolak',
        description: 'Data usaha kamu belum bisa diverifikasi. Hubungi petugas desa untuk info lebih lanjut.',
        badgeClass: 'bg-destructive/10 text-destructive',
        icon: Cancel01Icon,
    },
};

const TODAY_LABEL = new Intl.DateTimeFormat('id-ID', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric',
}).format(new Date());

export default function UmkmDashboard({ business, ownerName, primaryObstacle = null, recommendations = [] }) {
    const verification = VERIFICATION_META[business.verificationStatus] ?? VERIFICATION_META.pending;
    const needsAttention = business.verificationStatus === 'needs_revision' || business.verificationStatus === 'rejected';
    const programCount = recommendations.length;

    return (
            <div className="mx-auto max-w-5xl">
                <div className="relative overflow-hidden rounded-3xl bg-gradient-to-br from-primary to-brand-hover px-6 py-7 text-white sm:px-9 sm:py-9">
                    <div className="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <div className="flex flex-wrap items-center gap-2 text-sm text-white/80">
                                <HugeiconsIcon icon={Calendar03Icon} size={16} aria-hidden="true" />
                                {TODAY_LABEL}
                            </div>
                            <h1 className="mt-3 text-2xl font-extrabold tracking-tight sm:text-3xl">
                                Selamat datang, {ownerName}!
                            </h1>
                            <p className="mt-2 max-w-lg text-sm leading-relaxed text-white/85">
                                {`Data usaha ${business.name}: ${verification.description} `}
                                {primaryObstacle
                                    ? 'Rekomendasi di bawah mengikuti kendala yang sudah kamu isi.'
                                    : 'Isi kebutuhan dan kendala supaya program yang cocok bisa ditampilkan.'}
                            </p>
                        </div>

                        <div className="flex shrink-0 flex-wrap gap-2.5">
                            <a
                                href="#status-verifikasi"
                                className="rounded-full bg-white px-5 py-2.5 text-sm font-semibold text-primary transition-colors hover:bg-white/90"
                            >
                                Cek Status Verifikasi
                            </a>
                            <a
                                href="#rekomendasi"
                                className="rounded-full border border-white/40 px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-white/10"
                            >
                                Lihat Rekomendasi Program
                            </a>
                        </div>
                    </div>
                </div>

                <div className="mt-6 grid gap-4 sm:grid-cols-3">
                    <SummaryCard
                        icon={verification.icon}
                        badgeClass={verification.badgeClass}
                        title="Status Verifikasi"
                        headline={verification.label}
                        description={verification.description}
                    />
                    <SummaryCard
                        icon={primaryObstacle ? Tag01Icon : Notification01Icon}
                        badgeClass="bg-sun-50 text-sun-700"
                        title="Kendala Utama"
                        headline={primaryObstacle ? primaryObstacle.name : 'Belum ada'}
                        description={primaryObstacle
                            ? primaryObstacle.explanation
                            : 'Isi halaman Kebutuhan dan kendala supaya kendala utama bisa ditampilkan.'}
                    />
                    <SummaryCard
                        icon={Idea01Icon}
                        badgeClass="bg-brand-50 text-primary"
                        title="Program yang Cocok"
                        headline={programCount > 0 ? `${programCount} Program` : 'Belum ada'}
                        description={programCount > 0
                            ? 'Program ini dipilih dari kendala yang sudah kamu isi.'
                            : 'Belum ada program yang cocok dengan kendala usahamu.'}
                    />
                </div>

                <Card id="status-verifikasi" className="mt-6 scroll-mt-6">
                    <CardHeader>
                        <CardTitle className="text-base">Status Verifikasi Data Usahamu</CardTitle>
                        <p className="text-sm text-muted-foreground">
                            Begini progres pengecekan data usahamu oleh petugas desa, dijelaskan sesederhana mungkin.
                        </p>
                    </CardHeader>
                    <CardContent>
                        <div
                            className={cn(
                                'flex items-start gap-3 rounded-xl border px-4 py-3',
                                verification.badgeClass,
                                'border-transparent',
                            )}
                        >
                            <HugeiconsIcon icon={verification.icon} size={20} className="mt-0.5 shrink-0" aria-hidden="true" />
                            <div>
                                <p className="text-sm font-bold">{verification.label}</p>
                                <p className="mt-0.5 text-sm leading-relaxed opacity-90">{verification.description}</p>
                                {business.verificationNote && (
                                    <p className="mt-2 text-sm leading-relaxed opacity-90">
                                        Catatan dari petugas: &ldquo;{business.verificationNote}&rdquo;
                                    </p>
                                )}
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <section id="rekomendasi" className="mt-8 scroll-mt-6">
                    <div className="mb-4">
                        <h2 className="text-lg font-bold text-ink">Rekomendasi Program Untukmu</h2>
                        <p className="mt-1 text-sm text-muted-foreground">
                            Dipilih berdasarkan kendala yang sudah kamu ceritakan di halaman Kebutuhan dan kendala.
                        </p>
                    </div>

                    {programCount === 0 ? (
                        <Card>
                            <CardContent className="py-8 text-sm leading-relaxed text-muted-foreground">
                                {primaryObstacle
                                    ? 'Belum ada program yang cocok dengan kendala usahamu.'
                                    : 'Isi kebutuhan dan kendala dulu. Program yang cocok baru muncul setelah kuesioner tersimpan.'}
                                {' '}
                                <a href="/umkm/kebutuhan" className="font-semibold text-primary hover:underline">Buka kebutuhan dan kendala</a>
                            </CardContent>
                        </Card>
                    ) : (
                        <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            {recommendations.map((program) => (
                                <Card key={program.id} className="gap-3">
                                    <CardHeader className="gap-3">
                                        <div className="flex items-start justify-between gap-2">
                                            <span className="flex size-10 items-center justify-center rounded-xl bg-brand-50 text-primary">
                                                <HugeiconsIcon icon={Idea01Icon} size={20} strokeWidth={1.8} aria-hidden="true" />
                                            </span>
                                            <span className="rounded-full bg-muted px-2.5 py-0.5 text-[11px] font-semibold text-muted-foreground">
                                                {program.category}
                                            </span>
                                        </div>
                                        <div>
                                            <CardTitle className="text-base">{program.name}</CardTitle>
                                            <p className="mt-0.5 text-xs font-medium text-primary">{program.provider}</p>
                                        </div>
                                    </CardHeader>
                                    <CardContent className="flex flex-col gap-4">
                                        <p className="text-sm leading-relaxed text-muted-foreground">{program.description}</p>
                                        <a href={program.href} className={cn(buttonVariants({ size: 'sm' }), 'self-start')}>
                                            Lihat detail
                                        </a>
                                    </CardContent>
                                </Card>
                            ))}
                        </div>
                    )}
                </section>
            </div>
    );
}

function SummaryCard({ icon, badgeClass, title, headline, description }) {
    return (
        <Card className="gap-3">
            <CardHeader className="flex-row items-center gap-3 space-y-0">
                <span className={cn('flex size-10 shrink-0 items-center justify-center rounded-xl', badgeClass)}>
                    <HugeiconsIcon icon={icon} size={20} strokeWidth={1.8} aria-hidden="true" />
                </span>
                <CardTitle className="text-sm text-muted-foreground">{title}</CardTitle>
            </CardHeader>
            <CardContent>
                <p className="text-lg font-bold text-ink">{headline}</p>
                <p className="mt-1 text-sm leading-relaxed text-muted-foreground">{description}</p>
            </CardContent>
        </Card>
    );
}
