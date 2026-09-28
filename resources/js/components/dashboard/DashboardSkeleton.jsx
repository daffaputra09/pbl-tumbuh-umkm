function Block({ className }) {
    return <div className={`animate-pulse rounded-2xl bg-slate-200/70 ${className}`} />;
}

/**
 * Placeholder ringan selama bundle dashboard (termasuk library chart) dimuat.
 */
export default function DashboardSkeleton() {
    return (
        <div className="min-h-screen bg-background lg:pl-64" aria-busy="true" aria-label="Memuat dashboard">
            <div className="fixed inset-y-0 left-0 hidden w-64 border-r border-border bg-white lg:block" />
            <div className="h-16 border-b border-border bg-white/80" />
            <div className="mx-auto flex max-w-[1400px] flex-col gap-5 p-4 sm:p-6 lg:p-8">
                <Block className="h-36" />
                <Block className="h-16" />
                <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <Block className="h-36" />
                    <Block className="h-36" />
                    <Block className="h-36" />
                    <Block className="h-36" />
                </div>
                <div className="grid gap-4 lg:grid-cols-3">
                    <Block className="h-80 lg:col-span-2" />
                    <Block className="h-80" />
                </div>
            </div>
        </div>
    );
}
