import { cn } from '@/lib/utils';

function Card({ className, ...props }) {
    return (
        <div
            data-slot="card"
            className={cn('flex flex-col gap-4 rounded-2xl border bg-card p-6 text-card-foreground shadow-xs', className)}
            {...props}
        />
    );
}

function CardHeader({ className, ...props }) {
    return <div data-slot="card-header" className={cn('flex flex-col gap-1.5', className)} {...props} />;
}

function CardTitle({ className, ...props }) {
    return <h3 data-slot="card-title" className={cn('text-lg leading-snug font-bold text-ink', className)} {...props} />;
}

function CardDescription({ className, ...props }) {
    return <p data-slot="card-description" className={cn('text-sm leading-relaxed text-muted-foreground', className)} {...props} />;
}

function CardContent({ className, ...props }) {
    return <div data-slot="card-content" className={cn(className)} {...props} />;
}

export { Card, CardHeader, CardTitle, CardDescription, CardContent };
