import { HugeiconsIcon } from '@hugeicons/react';
import {
    Calendar03Icon,
    CheckmarkCircle02Icon,
    Clock01Icon,
    Factory01Icon,
    Idea01Icon,
    LegalDocument01Icon,
    Megaphone01Icon,
    Money03Icon,
    Notification01Icon,
    SmartPhone01Icon,
} from '@hugeicons/core-free-icons';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { cn } from '@/lib/utils';

// TODO: data di bawah ini masih dummy karena belum ada login & API.
// Nantinya diganti dengan data asli hasil isian halaman Profil & Kebutuhan,
// diambil berdasarkan akun UMKM yang sedang login.
const DUMMY_PROFILE = {
    namaUsaha: 'Warung Ibu Sari',
    namaPemilik: 'Sari Wulandari',
    kategori: 'Makanan & Minuman',
    statusVerifikasi: 'review', // 'verified' | 'review'
};

// TODO: dummy, nantinya diisi otomatis dari hasil scoring assessment
// (kategori kendala dengan skor tertinggi, dikerjakan bagian Daffa).
const KENDALA_UTAMA = {
    nama: 'Pemasaran',
    penjelasan: 'Kendala paling besar yang kamu hadapi saat ini ada di bagian pemasaran. Makanya rekomendasi program di bawah ini banyak soal promosi dan jualan online.',
    icon: Megaphone01Icon,
};

// TODO: dummy juga, nantinya hasil pengecekan dokumen asli oleh petugas.
const VERIFICATION_ITEMS = [
    { label: 'Profil Usaha', done: true, note: 'Sudah lengkap diisi' },
    { label: 'Legalitas (NIB/NPWP)', done: false, note: 'Belum dilengkapi, cek halaman Profil UMKM' },
    { label: 'Lokasi Usaha', done: true, note: 'Alamat sudah tercatat' },
];

const RECOMMENDATIONS = [
    {
        title: 'Pelatihan Pemasaran Digital',
        penyelenggara: 'Dinas Koperasi & UKM',
        description: 'Cocok buat kamu yang mau belajar promosi lewat media sosial dan marketplace supaya jangkauan pembeli lebih luas.',
        bidang: 'Pemasaran',
        icon: Megaphone01Icon,
    },
    {
        title: 'Pendampingan Sertifikasi Halal',
        penyelenggara: 'Kementerian Agama',
        description: 'Dibantu tahap demi tahap mengurus sertifikasi halal tanpa perlu bingung sendiri.',
        bidang: 'Legalitas',
        icon: LegalDocument01Icon,
    },
    {
        title: 'Akses Bantuan Modal Usaha',
        penyelenggara: 'Program KUR Perbankan',
        description: 'Informasi program KUR dan bantuan modal yang sesuai dengan skala usahamu.',
        bidang: 'Modal',
        icon: Money03Icon,
    },
    {
        title: 'Pendampingan Produksi & Kemasan',
        penyelenggara: 'Dinas Perindustrian',
        description: 'Tips mengemas produk supaya lebih menarik dan efisien di ongkos produksi.',
        bidang: 'Produksi',
        icon: Factory01Icon,
    },
    {
        title: 'Pengenalan Pembayaran QRIS',
        penyelenggara: 'Bank Indonesia',
        description: 'Panduan mendaftar dan memakai QRIS supaya pembeli lebih mudah bertransaksi.',
        bidang: 'Digitalisasi',
        icon: SmartPhone01Icon,
    },
];

const TODAY_LABEL = new Intl.DateTimeFormat('id-ID', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric',
}).format(new Date());

export default function UmkmDashboard() {
    const isVerified = DUMMY_PROFILE.statusVerifikasi === 'verified';

    return (
            <div className="mx-auto max-w-5xl">
                {/* Welcome banner */}
                <div className="relative overflow-hidden rounded-3xl bg-gradient-to-br from-primary to-brand-hover px-6 py-7 text-white sm:px-9 sm:py-9">
                    <div className="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <div className="flex flex-wrap items-center gap-2 text-sm text-white/80">
                                <HugeiconsIcon icon={Calendar03Icon} size={16} aria-hidden="true" />
                                {TODAY_LABEL}
                                <span className="rounded-full bg-white/15 px-2.5 py-0.5 text-[11px] font-semibold">
                                    Data contoh
                                </span>
                            </div>
                            <h1 className="mt-3 text-2xl font-extrabold tracking-tight sm:text-3xl">
                                Selamat datang, {DUMMY_PROFILE.namaPemilik}!
                            </h1>
                            <p className="mt-2 max-w-lg text-sm leading-relaxed text-white/85">
                                {isVerified
                                    ? `Data usaha ${DUMMY_PROFILE.namaUsaha} sudah terverifikasi. Yuk lihat program pembinaan yang cocok buat usahamu.`
                                    : `Data usaha ${DUMMY_PROFILE.namaUsaha} sedang ditinjau petugas desa. Sambil menunggu, kamu sudah bisa lihat rekomendasi program di bawah ini.`}
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

                {/* Tiga ringkasan utama: status verifikasi, kendala utama, program yang cocok */}
                <div className="mt-6 grid gap-4 sm:grid-cols-3">
                    <SummaryCard
                        icon={isVerified ? CheckmarkCircle02Icon : Notification01Icon}
                        tone={isVerified ? 'success' : 'sun'}
                        title="Status Verifikasi"
                        headline={isVerified ? 'Terverifikasi' : 'Menunggu Cek'}
                        description={
                            isVerified
                                ? 'Data usaha kamu sudah lengkap dan aktif.'
                                : 'Data usaha kamu sedang ditinjau petugas desa.'
                        }
                    />
                    <SummaryCard
                        icon={KENDALA_UTAMA.icon}
                        tone="sun"
                        title="Kendala Utama"
                        headline={KENDALA_UTAMA.nama}
                        description={KENDALA_UTAMA.penjelasan}
                    />
                    <SummaryCard
                        icon={Idea01Icon}
                        tone="brand"
                        title="Program yang Cocok"
                        headline={`${RECOMMENDATIONS.length} Program`}
                        description="Sudah kami pilihkan program yang sesuai dengan kendala usahamu."
                    />
                </div>

                {/* Status verifikasi detail */}
                <Card id="status-verifikasi" className="mt-6 scroll-mt-6">
                    <CardHeader>
                        <CardTitle className="text-base">Kelengkapan Data Usahamu</CardTitle>
                        <p className="text-sm text-muted-foreground">
                            Begini progres pengecekan data usahamu oleh petugas desa, dijelaskan sesederhana mungkin.
                        </p>
                    </CardHeader>
                    <CardContent className="flex flex-col gap-3 sm:flex-row sm:gap-4">
                        {VERIFICATION_ITEMS.map((item) => (
                            <div
                                key={item.label}
                                className={cn(
                                    'flex flex-1 items-start gap-3 rounded-xl border px-4 py-3',
                                    item.done ? 'border-success/30 bg-success/5' : 'border-sun-200 bg-sun-50',
                                )}
                            >
                                <HugeiconsIcon
                                    icon={item.done ? CheckmarkCircle02Icon : Clock01Icon}
                                    size={18}
                                    className={cn('mt-0.5 shrink-0', item.done ? 'text-success' : 'text-sun-700')}
                                    aria-hidden="true"
                                />
                                <div>
                                    <p className="text-sm font-semibold text-ink">{item.label}</p>
                                    <p className="mt-0.5 text-xs leading-relaxed text-muted-foreground">{item.note}</p>
                                </div>
                            </div>
                        ))}
                    </CardContent>
                </Card>

                {/* Rekomendasi program */}
                <section id="rekomendasi" className="mt-8 scroll-mt-6">
                    <div className="mb-4">
                        <h2 className="text-lg font-bold text-ink">Rekomendasi Program Untukmu</h2>
                        <p className="mt-1 text-sm text-muted-foreground">
                            Dipilih berdasarkan kendala yang sudah kamu ceritakan di halaman Kebutuhan &amp; Kendala.
                        </p>
                    </div>

                    <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        {RECOMMENDATIONS.map((program) => (
                            <Card key={program.title} className="gap-3">
                                <CardHeader className="gap-3">
                                    <div className="flex items-start justify-between gap-2">
                                        <span className="flex size-10 items-center justify-center rounded-xl bg-brand-50 text-primary">
                                            <HugeiconsIcon icon={program.icon} size={20} strokeWidth={1.8} aria-hidden="true" />
                                        </span>
                                        <span className="rounded-full bg-muted px-2.5 py-0.5 text-[11px] font-semibold text-muted-foreground">
                                            {program.bidang}
                                        </span>
                                    </div>
                                    <div>
                                        <CardTitle className="text-base">{program.title}</CardTitle>
                                        <p className="mt-0.5 text-xs font-medium text-primary">{program.penyelenggara}</p>
                                    </div>
                                </CardHeader>
                                <CardContent className="flex flex-col gap-4">
                                    <p className="text-sm leading-relaxed text-muted-foreground">{program.description}</p>
                                    <Button type="button" size="sm" className="self-start">
                                        Pelajari &amp; Daftar
                                    </Button>
                                </CardContent>
                            </Card>
                        ))}
                    </div>
                </section>
            </div>
    );
}

const TONE_CLASSES = {
    success: 'bg-success/10 text-success',
    sun: 'bg-sun-50 text-sun-700',
    brand: 'bg-brand-50 text-primary',
};

function SummaryCard({ icon, tone, title, headline, description }) {
    return (
        <Card className="gap-3">
            <CardHeader className="flex-row items-center gap-3 space-y-0">
                <span className={cn('flex size-10 shrink-0 items-center justify-center rounded-xl', TONE_CLASSES[tone])}>
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