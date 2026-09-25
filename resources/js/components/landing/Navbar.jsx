import { useState } from 'react';
import { AnimatePresence, motion, useMotionValueEvent, useScroll } from 'motion/react';
import { ArrowRight02Icon, Cancel01Icon, Menu01Icon } from '@hugeicons/core-free-icons';
import { buttonVariants } from '@/components/ui/button';
import { Icon, Logo, navigationLinks } from '@/components/landing/shared';
import { cn } from '@/lib/utils';

export default function Navbar() {
    const [isScrolled, setIsScrolled] = useState(false);
    const [isMenuOpen, setIsMenuOpen] = useState(false);
    const { scrollY } = useScroll();

    useMotionValueEvent(scrollY, 'change', (latest) => {
        setIsScrolled(latest > 24);
    });

    return (
        <motion.header
            initial={{ y: -40, opacity: 0 }}
            animate={{ y: 0, opacity: 1 }}
            transition={{ duration: 0.6, ease: [0.22, 1, 0.36, 1] }}
            className="fixed inset-x-0 top-0 z-50 px-4 pt-4"
        >
            <nav
                className={cn(
                    'mx-auto flex max-w-6xl items-center justify-between gap-4 rounded-full border px-3 py-2 transition-all duration-500 sm:px-4',
                    isScrolled || isMenuOpen
                        ? 'border-slate-200/80 bg-white/80 shadow-[0_10px_30px_-12px_rgb(15_23_42/0.18)] backdrop-blur-xl'
                        : 'border-transparent bg-transparent',
                )}
            >
                <Logo className="pl-1" />

                <ul className="hidden items-center gap-1 lg:flex">
                    {navigationLinks.map((link) => (
                        <li key={link.href}>
                            <a
                                href={link.href}
                                className="rounded-full px-3.5 py-2 text-sm font-medium text-slate-600 transition-colors hover:bg-brand-50 hover:text-brand-hover"
                            >
                                {link.label}
                            </a>
                        </li>
                    ))}
                </ul>

                <div className="flex items-center gap-2">
                    <a href="#mulai" className={cn(buttonVariants({ variant: 'ghost', size: 'sm' }), 'hidden sm:inline-flex')}>
                        Masuk
                    </a>
                    <a href="#mulai" className={cn(buttonVariants({ size: 'sm' }), 'hidden sm:inline-flex')}>
                        Daftarkan Usaha
                        <Icon icon={ArrowRight02Icon} size={16} strokeWidth={2} className="transition-transform group-hover/button:translate-x-0.5" />
                    </a>
                    <button
                        type="button"
                        onClick={() => setIsMenuOpen((open) => !open)}
                        className="flex h-11 cursor-pointer items-center gap-1.5 rounded-full px-3 text-sm font-semibold text-ink transition-colors outline-none hover:bg-brand-50 focus-visible:ring-[3px] focus-visible:ring-ring/40 lg:hidden"
                        aria-expanded={isMenuOpen}
                    >
                        <Icon icon={isMenuOpen ? Cancel01Icon : Menu01Icon} size={20} />
                        {isMenuOpen ? 'Tutup' : 'Menu'}
                    </button>
                </div>
            </nav>

            <AnimatePresence>
                {isMenuOpen && (
                    <motion.div
                        initial={{ opacity: 0, y: -12, scale: 0.98 }}
                        animate={{ opacity: 1, y: 0, scale: 1 }}
                        exit={{ opacity: 0, y: -12, scale: 0.98 }}
                        transition={{ duration: 0.25, ease: 'easeOut' }}
                        className="mx-auto mt-2 max-w-6xl rounded-3xl border bg-white/95 p-3 shadow-xl backdrop-blur-xl lg:hidden"
                    >
                        <ul className="flex flex-col">
                            {navigationLinks.map((link) => (
                                <li key={link.href}>
                                    <a
                                        href={link.href}
                                        onClick={() => setIsMenuOpen(false)}
                                        className="flex rounded-2xl px-4 py-3 text-[15px] font-medium text-ink hover:bg-brand-50"
                                    >
                                        {link.label}
                                    </a>
                                </li>
                            ))}
                        </ul>
                        <div className="mt-2 grid grid-cols-2 gap-2 border-t pt-3">
                            <a href="#mulai" onClick={() => setIsMenuOpen(false)} className={buttonVariants({ variant: 'outline' })}>
                                Masuk
                            </a>
                            <a href="#mulai" onClick={() => setIsMenuOpen(false)} className={buttonVariants()}>
                                Daftarkan Usaha
                            </a>
                        </div>
                    </motion.div>
                )}
            </AnimatePresence>
        </motion.header>
    );
}
