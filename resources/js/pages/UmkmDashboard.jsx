import { HugeiconsIcon } from '@hugeicons/react';
import {
    CheckmarkCircle02Icon,
    Factory01Icon,
    LegalDocument01Icon,
    Megaphone01Icon,
    Money03Icon,
    Notification01Icon,
    SmartPhone01Icon,
    Store04Icon,
} from '@hugeicons/core-free-icons';
import PageBackground from '@/components/umkm/PageBackground';
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

const RECOMMENDATIONS = [
    {
        title: 'Pelatihan Pemasaran Digital',
        description: 'Belajar promosi lewat media sosial dan marketplace supaya jangkauan pembeli lebih luas.',
        bidang: 'Pemasaran',
        icon: Megaphone01Icon,
    },
    {
        title: 'Pendampingan Sertifikasi Halal',
        description: 'Dibantu tahap demi tahap mengurus sertifikasi halal tanpa perlu bingung sendiri.',
        bidang: 'Legalitas',
        icon: LegalDocument01Icon,
    },
    {
        title: 'Akses Bantuan Modal Usaha',
        description: 'Informasi program KUR dan bantuan modal yang sesuai dengan skala usahamu.',
        bidang: 'Modal',
        icon: Money03Icon,
    },
    {
        title: 'Pendampingan Produksi & Kemasan',
        description: 'Tips mengemas produk supaya lebih menarik dan efisien di ongkos produksi.',
        bidang: 'Produksi',
        icon: Factory01Icon,
    },
    {
        title: 'Pengenalan Pembayaran QRIS',
        description: 'Panduan mendaftar dan memakai QRIS supaya pembeli lebih mudah bertransaksi.',
        bidang: 'Digitalisasi',
        icon: SmartPhone01Icon,
    },
];

export default function UmkmDashboard() {
    const isVerified = DUMMY_PROFILE.statusVerifikasi === 'verified';

    return (
        <div className="relative isolate min-h-screen overflow-hidden bg-background py-10 sm:py-14">
            <PageBackground />

            <div className="mx-auto max-w-5xl px-5">
                <header className="mb-8 flex flex-col gap-6 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <p className="text-sm font-semibold text-primary">Dashboard Pelaku UMKM</p>
                        <h1 className="mt-1 text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">
                            Halo, {DUMMY_PROFILE.namaPemilik.split(' ')[0]} 👋
                        </h1>
                        <p className="mt-2 max-w-lg text-sm leading-relaxed text-muted-foreground">
                            Ini ringkasan usaha <span className="font-semibold text-ink">{DUMMY_PROFILE.namaUsaha}</span> kamu
                            hari ini.
                        </p>
                    </div>

                    <div className="flex items-center gap-3">
                        <a href="/akun" className="text-sm font-semibold text-primary underline-offset-4 hover:underline">
                            Profil akun
                        </a>
                        <StatusBadge verified={isVerified} />
                    </div>
                </header>

                <Card className="mb-8">
                    <CardContent className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div className="flex items-center gap-3">
                            <span className="flex size-11 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-primary">
                                <HugeiconsIcon icon={Store04Icon} size={22} strokeWidth={1.8} aria-hidden="true" />
                            </span>
                            <div>
                                <p className="text-sm font-bold text-ink">{DUMMY_PROFILE.namaUsaha}</p>
                                <p className="text-xs text-muted-foreground">{DUMMY_PROFILE.kategori}</p>
                            </div>
                        </div>

                        {!isVerified && (
                            <p className="text-xs leading-relaxed text-muted-foreground sm:max-w-xs sm:text-right">
                                Data usahamu sedang ditinjau oleh petugas desa. Biasanya proses ini memakan waktu beberapa
                                hari kerja.
                            </p>
                        )}
                    </CardContent>
                </Card>

                <section>
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
                                    <span className="flex size-10 items-center justify-center rounded-xl bg-brand-50 text-primary">
                                        <HugeiconsIcon icon={program.icon} size={20} strokeWidth={1.8} aria-hidden="true" />
                                    </span>
                                    <div>
                                        <span className="mb-1.5 inline-block rounded-full bg-muted px-2.5 py-0.5 text-[11px] font-semibold text-muted-foreground">
                                            {program.bidang}
                                        </span>
                                        <CardTitle className="text-base">{program.title}</CardTitle>
                                    </div>
                                </CardHeader>
                                <CardContent className="text-sm leading-relaxed text-muted-foreground">
                                    {program.description}
                                </CardContent>
                            </Card>
                        ))}
                    </div>
                </section>
            </div>
        </div>
    );
}

function StatusBadge({ verified }) {
    return (
        <div
            className={cn(
                'flex items-center gap-2 self-start rounded-full border px-4 py-2 text-sm font-semibold',
                verified ? 'border-success/30 bg-success/10 text-success' : 'border-sun-200 bg-sun-50 text-sun-700',
            )}
        >
            <HugeiconsIcon icon={verified ? CheckmarkCircle02Icon : Notification01Icon} size={16} aria-hidden="true" />
            {verified ? 'Terverifikasi' : 'Dalam Tinjauan'}
        </div>
    );
}
