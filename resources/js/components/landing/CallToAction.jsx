import { Login01Icon, Store01Icon } from '@hugeicons/core-free-icons';
import { Icon, Reveal } from '@/components/landing/shared';

export default function CallToAction() {
    return (
        <section id="mulai" className="px-5 pb-20 sm:pb-28">
            <Reveal className="mx-auto max-w-6xl">
                <div className="grid gap-10 rounded-[2rem] bg-brand-950 px-6 py-12 text-white sm:px-12 sm:py-16 lg:grid-cols-[1.3fr_1fr] lg:items-center lg:gap-16">
                    <div className="flex flex-col gap-4">
                        <h2 className="text-[1.75rem] leading-[1.15] font-extrabold tracking-tight text-balance sm:text-4xl">
                            Punya usaha di desa? Daftarkan supaya desa tahu apa yang Anda butuhkan.
                        </h2>
                        <p className="max-w-lg text-base leading-relaxed text-brand-100">
                            Data yang Anda isi dipakai petugas desa untuk memilih program pembinaan yang sesuai. Pendaftaran tidak dipungut biaya.
                        </p>
                    </div>

                    <div className="flex flex-col gap-3">
                        <ActionButton href="/register" icon={Store01Icon} label="Daftarkan usaha" primary />
                        <ActionButton href="/login" icon={Login01Icon} label="Masuk sebagai pengguna" />
                    </div>
                </div>
            </Reveal>
        </section>
    );
}

function ActionButton({ href, icon, label, primary = false }) {
    return (
        <a
            href={href}
            className={
                primary
                    ? 'group flex min-h-12 w-full items-center justify-between gap-3 rounded-xl bg-white px-5 text-left font-semibold text-brand-950 transition duration-300 hover:bg-brand-50 hover:shadow-lg hover:-translate-y-0.5'
                    : 'group flex min-h-12 w-full items-center justify-between gap-3 rounded-xl px-5 text-left font-semibold text-white ring-1 ring-white/25 transition duration-300 hover:bg-white/10 hover:ring-white/50 hover:shadow-lg hover:-translate-y-0.5'
            }
        >
            <span className="flex items-center gap-2.5">
                <Icon icon={icon} size={18} className="transition-transform duration-300 group-hover:scale-110" />
                {label}
            </span>
            <span className="opacity-0 transition-all duration-300 group-hover:opacity-100 group-hover:translate-x-1">
                &rarr;
            </span>
        </a>
    );
}
