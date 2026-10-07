import { useRef } from 'react';
import { motion, useScroll, useTransform } from 'motion/react';
import {
    ArrowDown01Icon,
    ArrowRight02Icon,
    CheckmarkCircle02Icon,
    Idea01Icon,
    Leaf01Icon,
    Tick02Icon,
} from '@hugeicons/core-free-icons';
import BlurText from '@/components/reactbits/BlurText';
import ShinyText from '@/components/reactbits/ShinyText';
import DashboardPreview from '@/components/landing/DashboardPreview';
import { buttonVariants } from '@/components/ui/button';
import { Icon } from '@/components/landing/shared';
import { cn } from '@/lib/utils';

const roles = ['Pelaku UMKM', 'Petugas Desa', 'Kepala Desa'];

export default function Hero() {
    const previewRef = useRef(null);
    const { scrollYProgress } = useScroll({ target: previewRef, offset: ['start end', 'start 30%'] });
    const rotateX = useTransform(scrollYProgress, [0, 1], [22, 0]);
    const scale = useTransform(scrollYProgress, [0, 1], [0.92, 1]);

    return (
        <section id="beranda" className="relative isolate overflow-hidden pt-32 pb-20 sm:pt-40 lg:pb-28">
            <HeroBackground />

            <div className="mx-auto flex max-w-6xl flex-col items-center px-5 text-center">
                <h1 className="max-w-4xl text-[2.6rem] leading-[1.05] font-extrabold tracking-[-0.035em] text-ink sm:text-6xl lg:text-7xl">
                    <BlurText text="Dari Data, Menjadi Aksi" highlight={['Aksi']} highlightClassName="text-brand" delay={0.2} />
                    <br />
                    <span className="relative inline-block">
                        <BlurText text="untuk UMKM Desa." delay={0.55} />
                        <motion.svg
                            aria-hidden
                            viewBox="0 0 300 20"
                            preserveAspectRatio="none"
                            className="absolute -bottom-2 left-[38%] h-3 w-[62%] text-sun sm:-bottom-3 sm:h-4"
                        >
                            <motion.path
                                d="M3 14 C 60 4, 140 4, 200 10 S 280 16, 297 6"
                                fill="none"
                                stroke="currentColor"
                                strokeWidth="5"
                                strokeLinecap="round"
                                initial={{ pathLength: 0 }}
                                animate={{ pathLength: 1 }}
                                transition={{ duration: 1, delay: 1.2, ease: 'easeInOut' }}
                            />
                        </motion.svg>
                    </span>
                </h1>

                <motion.p
                    initial={{ opacity: 0, y: 16 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.7, delay: 0.9 }}
                    className="mt-7 max-w-2xl text-base leading-relaxed text-pretty text-muted-foreground sm:text-lg"
                >
                    Tumbuh UMKM membantu pemerintah desa mendata usaha warga, memahami kendala yang mereka hadapi, lalu menentukan
                    program pembinaan yang tepat. Semua tersimpan di satu tempat dan mudah dipantau.
                </motion.p>

                <motion.div
                    initial={{ opacity: 0, y: 16 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.7, delay: 1.05 }}
                    className="mt-9 flex w-full flex-col items-center justify-center gap-3 sm:w-auto sm:flex-row"
                >
                    <a href="/register" className={cn(buttonVariants({ size: 'lg' }), 'w-full sm:w-auto')}>
                        Daftarkan Usaha Anda
                        <Icon icon={ArrowRight02Icon} size={18} strokeWidth={2} className="transition-transform group-hover/button:translate-x-1" />
                    </a>
                    <a href="#cara-kerja" className={cn(buttonVariants({ variant: 'outline', size: 'lg' }), 'w-full sm:w-auto')}>
                        Lihat cara kerjanya
                        <Icon icon={ArrowDown01Icon} size={18} strokeWidth={2} className="transition-transform group-hover/button:translate-y-0.5" />
                    </a>
                </motion.div>

                <motion.ul
                    initial={{ opacity: 0 }}
                    animate={{ opacity: 1 }}
                    transition={{ duration: 0.7, delay: 1.25 }}
                    className="mt-8 flex flex-wrap items-center justify-center gap-x-5 gap-y-2 text-sm text-slate-600"
                >
                    <li className="font-medium text-slate-500">Dirancang untuk:</li>
                    {roles.map((role) => (
                        <li key={role} className="flex items-center gap-1.5 font-semibold text-ink">
                            <span className="grid size-5 place-items-center rounded-full bg-brand-100 text-brand">
                                <Icon icon={Tick02Icon} size={12} strokeWidth={2.5} />
                            </span>
                            {role}
                        </li>
                    ))}
                </motion.ul>
            </div>

            <div ref={previewRef} className="relative mx-auto mt-16 max-w-6xl px-5 [perspective:1400px] sm:mt-20">
                <motion.div
                    style={{ rotateX, scale, transformOrigin: 'center top' }}
                    initial={{ opacity: 0, y: 60 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 1, delay: 0.9, ease: [0.22, 1, 0.36, 1] }}
                    className="relative"
                >
                    <div aria-hidden className="absolute -inset-x-10 -top-10 bottom-10 -z-10 rounded-[3rem] bg-gradient-to-b from-brand-200/60 via-brand-100/30 to-transparent blur-3xl" />
                    <DashboardPreview />

                    <FloatingCard className="-top-8 -left-6 hidden lg:block" floatClassName="animate-float" delay={1.6}>
                        <span className="grid size-10 place-items-center rounded-xl bg-sun-50 text-sun">
                            <Icon icon={Idea01Icon} size={20} />
                        </span>
                        <div>
                            <p className="text-[11px] font-medium text-slate-500">Rekomendasi program</p>
                            <p className="text-sm font-bold text-ink">Pelatihan pemasaran digital</p>
                            <p className="text-[11px] font-semibold text-brand">Cocok untuk 18 UMKM</p>
                        </div>
                    </FloatingCard>

                    <FloatingCard className="-right-6 bottom-16 hidden lg:block" floatClassName="animate-float-slow" delay={1.8}>
                        <span className="grid size-10 place-items-center rounded-xl bg-green-50 text-success">
                            <Icon icon={CheckmarkCircle02Icon} size={20} />
                        </span>
                        <div>
                            <p className="text-[11px] font-medium text-slate-500">Data diverifikasi</p>
                            <p className="text-sm font-bold text-ink">Batik Tulis Lestari</p>
                            <p className="text-[11px] font-semibold text-success">Oleh Petugas Desa · baru saja</p>
                        </div>
                    </FloatingCard>
                </motion.div>
            </div>
        </section>
    );
}

function FloatingCard({ children, className, floatClassName, delay }) {
    return (
        <motion.div
            initial={{ opacity: 0, scale: 0.85 }}
            animate={{ opacity: 1, scale: 1 }}
            transition={{ duration: 0.6, delay, ease: [0.22, 1, 0.36, 1] }}
            className={cn('absolute z-10', className)}
        >
            <div className={cn("flex items-center gap-3 rounded-2xl border border-white bg-white/90 p-3 pr-5 text-left shadow-[0_20px_40px_-16px_rgb(15_23_42/0.3)] backdrop-blur-md", floatClassName)}>
                {children}
            </div>
        </motion.div>
    );
}

function HeroBackground() {
    return (
        <div aria-hidden className="pointer-events-none absolute inset-0 -z-10">
            <div className="absolute inset-0 bg-grid mask-radial" />
            <div className="absolute -top-40 left-1/2 h-[520px] w-[900px] -translate-x-1/2 rounded-full bg-brand-200/50 blur-[120px]" />
            <div className="absolute top-40 -right-40 h-80 w-80 rounded-full bg-sun-200/50 blur-[100px]" />
            <div className="absolute top-72 -left-32 h-72 w-72 rounded-full bg-brand-300/30 blur-[100px]" />

            <motion.div
                className="absolute top-36 left-[6%] hidden text-brand-400/70 md:block"
                initial={{ opacity: 0, rotate: -30, scale: 0.6 }}
                animate={{ opacity: 1, rotate: -12, scale: 1 }}
                transition={{ duration: 1.2, delay: 0.8 }}
            >
                <Icon icon={Leaf01Icon} size={44} strokeWidth={1.4} className="animate-float-slow" />
            </motion.div>
            <motion.div
                className="absolute top-64 right-[8%] hidden text-sun/60 md:block"
                initial={{ opacity: 0, rotate: 40, scale: 0.6 }}
                animate={{ opacity: 1, rotate: 18, scale: 1 }}
                transition={{ duration: 1.2, delay: 1 }}
            >
                <Icon icon={Leaf01Icon} size={34} strokeWidth={1.4} className="animate-float" />
            </motion.div>

            <svg className="absolute top-28 left-0 hidden h-[420px] w-full lg:block" viewBox="0 0 1440 420" preserveAspectRatio="none" fill="none">
                <motion.path
                    d="M-20 380 C 180 360, 240 250, 380 260 S 560 330, 700 220 S 980 120, 1100 150 S 1320 60, 1460 40"
                    stroke="url(#growth)"
                    strokeWidth="1.5"
                    strokeDasharray="6 8"
                    initial={{ pathLength: 0, opacity: 0 }}
                    animate={{ pathLength: 1, opacity: 1 }}
                    transition={{ duration: 2.6, delay: 0.4, ease: 'easeInOut' }}
                />
                <defs>
                    <linearGradient id="growth" x1="0" x2="1440" y1="0" y2="0" gradientUnits="userSpaceOnUse">
                        <stop stopColor="#14b8a6" stopOpacity="0" />
                        <stop offset="0.3" stopColor="#14b8a6" stopOpacity="0.5" />
                        <stop offset="0.7" stopColor="#d97706" stopOpacity="0.5" />
                        <stop offset="1" stopColor="#d97706" stopOpacity="0" />
                    </linearGradient>
                </defs>
            </svg>
        </div>
    );
}
