import { motion } from 'motion/react';
import {
    CheckmarkCircle02Icon,
    Factory01Icon,
    Idea01Icon,
    LegalDocument01Icon,
    Megaphone01Icon,
    Money03Icon,
    SmartPhone01Icon,
    Tag01Icon,
    User02Icon,
    UserCheck01Icon,
} from '@hugeicons/core-free-icons';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Icon, Reveal, SampleLabel, SectionTitle, Underline } from '@/components/landing/shared';
import { cn } from '@/lib/utils';

const ASSESSED = 24;

const needs = [
    { label: 'Modal', icon: Money03Icon, high: 16, moderate: 5, low: 3, dominant: 18 },
    { label: 'Pemasaran', icon: Megaphone01Icon, high: 11, moderate: 8, low: 5, dominant: 14 },
    { label: 'Digitalisasi', icon: SmartPhone01Icon, high: 8, moderate: 9, low: 7, dominant: 9 },
    { label: 'Legalitas', icon: LegalDocument01Icon, high: 5, moderate: 7, low: 12, dominant: 6 },
    { label: 'Produksi', icon: Factory01Icon, high: 3, moderate: 6, low: 15, dominant: 4 },
];

const programs = [
    { name: 'Akses KUR mikro', provider: 'BUMDes', category: 'Modal' },
    { name: 'Pelatihan pemasaran lewat media sosial', provider: 'Desa', category: 'Pemasaran' },
];

const areas = [
    { title: 'Modal', icon: Money03Icon, asks: 'Kondisi modal, kebutuhan tambahan, dan hambatan mendapat pembiayaan.' },
    { title: 'Pemasaran', icon: Megaphone01Icon, asks: 'Cara mendapat pelanggan, jangkauan pasar, dan kemampuan promosi.' },
    { title: 'Legalitas', icon: LegalDocument01Icon, asks: 'NIB, NPWP, sertifikat halal, PIRT atau BPOM, dan kendala mengurusnya.' },
    { title: 'Produksi', icon: Factory01Icon, asks: 'Kapasitas, bahan baku, peralatan, dan tenaga kerja.' },
    { title: 'Digitalisasi', icon: SmartPhone01Icon, asks: 'WhatsApp Business, marketplace, pembukuan, dan pembayaran digital.' },
];

export default function Profiling() {
    return (
        <section id="profiling" className="relative border-y bg-white py-20 sm:py-28">
            <div className="mx-auto max-w-6xl px-5">
                <div className="grid items-center gap-12 lg:grid-cols-[1.05fr_1fr] lg:gap-16">
                    <Reveal className="order-2 lg:order-1">
                        <ExampleCard />
                    </Reveal>

                    <Reveal delay={0.1} className="order-1 flex flex-col gap-5 lg:order-2">
                        <SectionTitle>
                            Satu hasil analisis, <Underline>dua cara</Underline> membacanya
                        </SectionTitle>
                        <p className="text-base leading-relaxed text-muted-foreground sm:text-lg">
                            Usaha keripik bisa kesulitan modal sekaligus bingung memasarkan produknya. Tumbuh UMKM mencatat keduanya, lalu
                            menyampaikan hasilnya sesuai siapa yang membaca.
                        </p>
                        <p className="text-base leading-relaxed text-muted-foreground sm:text-lg">
                            Pemilik usaha membaca kalimat yang langsung bisa dipahami. Petugas desa melihat angka per bidang untuk menentukan
                            siapa yang dibina lebih dulu.
                        </p>
                    </Reveal>
                </div>

                <div className="mt-20 sm:mt-24">
                    <Reveal className="flex flex-col gap-2 sm:flex-row sm:items-baseline sm:justify-between">
                        <h3 className="text-xl font-extrabold tracking-tight text-ink sm:text-2xl">Lima bidang yang ditanyakan ke setiap usaha</h3>
                        <p className="text-sm text-muted-foreground">Kategori lain bisa ditambahkan oleh petugas.</p>
                    </Reveal>
                    <Reveal delay={0.1}>
                        <dl className="mt-6 grid border-t border-slate-200 sm:grid-cols-2 sm:gap-x-8 lg:grid-cols-5 lg:gap-x-0">
                            {areas.map((area) => (
                                <div
                                    key={area.title}
                                    className="flex flex-col gap-2 border-b border-slate-200 py-5 lg:border-b-0 lg:border-l lg:px-5 lg:py-6 lg:first:border-l-0 lg:first:pl-0"
                                >
                                    <dt className="flex items-center gap-2 font-bold text-ink">
                                        <Icon icon={area.icon} size={18} className="text-brand" />
                                        {area.title}
                                    </dt>
                                    <dd className="text-sm leading-relaxed text-muted-foreground">{area.asks}</dd>
                                </div>
                            ))}
                        </dl>
                    </Reveal>
                </div>
            </div>
        </section>
    );
}

function ExampleCard() {
    return (
        <div className="rounded-3xl bg-canvas p-3 ring-1 ring-slate-200 sm:p-4">
            <div className="rounded-2xl bg-white p-5 ring-1 ring-slate-200 sm:p-6">
                <div className="mb-5 flex items-start justify-between gap-3">
                    <div className="min-w-0">
                        <p className="font-bold text-ink">Keripik Tempe Bu Sri</p>
                        <p className="text-sm text-slate-500">Makanan & Minuman · Dusun Krajan</p>
                    </div>
                    <SampleLabel />
                </div>

                <Tabs defaultValue="umkm" className="gap-5">
                    <TabsList className="w-full bg-slate-50 [&>button]:flex-1">
                        <TabsTrigger value="umkm">
                            <Icon icon={User02Icon} />
                            Pemilik usaha
                        </TabsTrigger>
                        <TabsTrigger value="petugas">
                            <Icon icon={UserCheck01Icon} />
                            Petugas desa
                        </TabsTrigger>
                    </TabsList>

                    <TabsContent value="umkm">
                        <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ duration: 0.25 }} className="flex flex-col gap-3">
                            <div className="grid gap-3 sm:grid-cols-2">
                                <SummaryTile
                                    icon={CheckmarkCircle02Icon}
                                    tone="bg-success/10 text-success"
                                    title="Status Verifikasi"
                                    headline="Terverifikasi"
                                    description="Data usaha kamu sudah lengkap dan aktif."
                                />
                                <SummaryTile
                                    icon={Tag01Icon}
                                    tone="bg-sun-50 text-sun-700"
                                    title="Kendala Utama"
                                    headline="Modal"
                                    description="Usaha ini paling butuh bantuan di bidang modal, lalu pemasaran."
                                />
                            </div>
                            <ProgramList />
                        </motion.div>
                    </TabsContent>

                    <TabsContent value="petugas">
                        <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ duration: 0.25 }} className="flex flex-col gap-4">
                            <div className="flex flex-col gap-3 rounded-2xl border border-border p-4">
                                <div>
                                    <p className="text-sm font-bold text-ink">Status kendala per bidang</p>
                                    <p className="mt-0.5 text-xs text-muted-foreground">Diurutkan dari kebutuhan tinggi terbanyak.</p>
                                </div>
                                <ul className="flex flex-wrap gap-x-3 gap-y-1 text-[11px] text-muted-foreground">
                                    <li className="flex items-center gap-1.5"><span className="size-2 rounded-full bg-sun-700" />Tinggi</li>
                                    <li className="flex items-center gap-1.5"><span className="size-2 rounded-full bg-sun-400" />Sedang</li>
                                    <li className="flex items-center gap-1.5"><span className="size-2 rounded-full bg-brand-200" />Rendah</li>
                                </ul>
                                <ul className="flex flex-col gap-3">
                                    {needs.map((item, index) => (
                                        <li key={item.label} className="grid grid-cols-[auto_1fr] items-center gap-x-3 gap-y-1">
                                            <span className="grid size-8 place-items-center rounded-xl bg-brand-50 text-brand">
                                                <Icon icon={item.icon} size={16} />
                                            </span>
                                            <div className="min-w-0">
                                                <div className="flex items-baseline justify-between gap-2">
                                                    <p className="text-sm font-semibold text-ink">{item.label}</p>
                                                    <p className="text-xs text-muted-foreground">
                                                        <span className="font-bold text-sun-700 tabular-nums">{item.high}</span> tinggi
                                                    </p>
                                                </div>
                                                <p className="text-[11px] text-muted-foreground">Utama bagi {item.dominant} UMKM</p>
                                                <div className="mt-1.5 flex h-2.5 overflow-hidden rounded-full bg-slate-100">
                                                    {[
                                                        ['high', item.high, 'bg-sun-700'],
                                                        ['moderate', item.moderate, 'bg-sun-400'],
                                                        ['low', item.low, 'bg-brand-200'],
                                                    ].map(([key, value, color]) => (
                                                        <motion.span
                                                            key={key}
                                                            className={cn('h-full first:rounded-l-full last:rounded-r-full', color)}
                                                            initial={{ width: 0 }}
                                                            animate={{ width: `${(value / ASSESSED) * 100}%` }}
                                                            transition={{ duration: 0.7, delay: index * 0.06, ease: [0.22, 1, 0.36, 1] }}
                                                        />
                                                    ))}
                                                </div>
                                            </div>
                                        </li>
                                    ))}
                                </ul>
                            </div>
                        </motion.div>
                    </TabsContent>
                </Tabs>
            </div>
        </div>
    );
}

function SummaryTile({ icon, tone, title, headline, description }) {
    return (
        <div className="rounded-2xl border border-border bg-white p-3.5">
            <div className="flex items-center gap-2.5">
                <span className={cn('grid size-9 shrink-0 place-items-center rounded-xl', tone)}>
                    <Icon icon={icon} size={18} />
                </span>
                <p className="text-xs font-medium text-muted-foreground">{title}</p>
            </div>
            <p className="mt-3 text-base font-bold text-ink">{headline}</p>
            <p className="mt-1 text-xs leading-relaxed text-muted-foreground">{description}</p>
        </div>
    );
}

function ProgramList() {
    return (
        <div className="flex flex-col gap-2">
            <p className="text-sm font-bold text-ink">Rekomendasi Program Untukmu</p>
            {programs.map((program) => (
                <div key={program.name} className="flex items-start gap-3 rounded-2xl border border-border px-3 py-3">
                    <span className="grid size-9 shrink-0 place-items-center rounded-xl bg-brand-50 text-primary">
                        <Icon icon={Idea01Icon} size={18} />
                    </span>
                    <div className="min-w-0">
                        <div className="flex flex-wrap items-center gap-2">
                            <p className="text-sm font-bold text-ink">{program.name}</p>
                            <span className="rounded-full bg-muted px-2 py-0.5 text-[10px] font-semibold text-muted-foreground">{program.category}</span>
                        </div>
                        <p className="mt-0.5 text-xs font-medium text-primary">{program.provider}</p>
                    </div>
                </div>
            ))}
        </div>
    );
}
