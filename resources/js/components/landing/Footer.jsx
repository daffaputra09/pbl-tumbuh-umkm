import { Logo, navigationLinks } from '@/components/landing/shared';

export default function Footer() {
    return (
        <footer className="border-t bg-white">
            <div className="mx-auto flex max-w-6xl flex-col gap-8 px-5 py-10 lg:flex-row lg:items-center lg:justify-between">
                <div className="flex flex-col gap-2">
                    <Logo />
                    <p className="text-sm text-muted-foreground">Dari data, menjadi aksi untuk UMKM desa.</p>
                </div>
                <nav aria-label="Navigasi footer">
                    <ul className="flex flex-wrap gap-x-1 gap-y-1">
                        {navigationLinks.map((link) => (
                            <li key={link.href}>
                                <a
                                    href={link.href}
                                    className="inline-flex min-h-11 items-center rounded-lg px-3 text-sm font-medium text-slate-600 transition-colors hover:bg-brand-50 hover:text-brand-hover"
                                >
                                    {link.label}
                                </a>
                            </li>
                        ))}
                    </ul>
                </nav>
            </div>
            <div className="border-t">
                <p className="mx-auto max-w-6xl px-5 py-5 text-xs text-slate-600">© {new Date().getFullYear()} Tumbuh UMKM</p>
            </div>
        </footer>
    );
}
