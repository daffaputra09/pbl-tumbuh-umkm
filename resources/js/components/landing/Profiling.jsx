import { motion } from 'motion/react';
import {
    Factory01Icon,
    Idea01Icon,
    LegalDocument01Icon,
    Megaphone01Icon,
    Money03Icon,
    SmartPhone01Icon,
    User02Icon,
    UserCheck01Icon,
} from '@hugeicons/core-free-icons';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Icon, Reveal, SampleLabel, SectionTitle, Underline } from '@/components/landing/shared';
import { cn } from '@/lib/utils';

const needs = [
    { label: 'Modal', value: 82 },
    { label: 'Pemasaran', value: 65 },
    { label: 'Digitalisasi', value: 55 },
    { label: 'Legalitas', value: 20 },
    { label: 'Produksi', value: 15 },
];

const programs = ['Akses KUR mikro melalui BUMDes', 'Pelatihan pemasaran lewat media sosial'];

const areas = [
    { title: 'Modal', icon: Money03Icon, asks: 'Kondisi modal, kebutuhan tambahan, dan hambatan mendapat pembiayaan.' },
    { title: 'Pemasaran', icon: Megaphone01Icon, asks: 'Cara mendapat pelanggan, jangkauan pasar, dan kemampuan promosi.' },
    { title: 'Legalitas', icon: LegalDocument01Icon, asks: 'NIB, NPWP, sertifikat halal, PIRT atau BPOM, dan kendala mengurusnya.' },
    { title: 'Produksi', icon: Factory01Icon, asks: 'Kapasitas, bahan baku, peralatan, dan tenaga kerja.' },
    { title: 'Digitalisasi', icon: SmartPhone01Icon, asks: 'WhatsApp Business, marketplace, pembukuan, dan pembayaran digital.' },
];

function levelOf(value) {
    if (value >= 60) {
        return { label: 'Tinggi', chip: 'bg-sun-50 text-sun-700', bar: 'bg-sun' };
    }

    if (value >= 40) {
        return { label: 'Sedang', chip: 'bg-brand-50 text-brand-hover', bar: 'bg-brand' };
    }

    return { label: 'Rendah', chip: 'bg-slate-100 text-slate-600', bar: 'bg-slate-300' };
}

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
                        <p className="font-bold text-ink">Usaha keripik tempe</p>
                        <p className="text-sm text-slate-500">Makanan ringan · Data terverifikasi</p>
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
                        <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ duration: 0.25 }} className="flex flex-col gap-4">
                            <p className="rounded-xl bg-brand-50 p-4 text-[15px] leading-relaxed font-semibold text-brand-900">
                                Usaha Anda paling butuh bantuan di bidang modal dan pemasaran.
                            </p>
                            <ul className="flex flex-wrap gap-2">
                                {needs.map((item) => {
                                    const level = levelOf(item.value);

                                    return (
                                        <li key={item.label} className={cn('rounded-lg px-2.5 py-1 text-xs font-semibold', level.chip)}>
                                            {item.label}: {level.label}
                                        </li>
                                    );
                                })}
                            </ul>
                            <ProgramList />
                        </motion.div>
                    </TabsContent>

                    <TabsContent value="petugas">
                        <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ duration: 0.25 }} className="flex flex-col gap-4">
                            <div className="flex flex-col gap-3 rounded-xl border p-4">
                                <p className="text-xs font-bold text-slate-600">Tingkat kebutuhan per bidang</p>
                                {needs.map((item, index) => (
                                    <div key={item.label} className="grid grid-cols-[5.5rem_1fr_2.5rem] items-center gap-3 text-sm">
                                        <span className="text-slate-600">{item.label}</span>
                                        <div className="h-2 overflow-hidden rounded-full bg-slate-100">
                                            <motion.div
                                                className={cn('h-full rounded-full', levelOf(item.value).bar)}
                                                initial={{ width: 0 }}
                                                animate={{ width: `${item.value}%` }}
                                                transition={{ duration: 0.7, delay: index * 0.06, ease: [0.22, 1, 0.36, 1] }}
                                            />
                                        </div>
                                        <span className="text-right font-semibold text-ink tabular-nums">{item.value}%</span>
                                    </div>
                                ))}
                            </div>
                            <ProgramList />
                        </motion.div>
                    </TabsContent>
                </Tabs>
            </div>
        </div>
    );
}

function ProgramList() {
    return (
        <div className="flex flex-col gap-2">
            <p className="text-xs font-bold text-slate-600">Program yang disarankan</p>
            {programs.map((program) => (
                <p key={program} className="flex items-center gap-2.5 rounded-lg border px-3 py-2.5 text-sm font-semibold text-ink">
                    <Icon icon={Idea01Icon} size={16} className="shrink-0 text-sun-700" />
                    {program}
                </p>
            ))}
        </div>
    );
}
