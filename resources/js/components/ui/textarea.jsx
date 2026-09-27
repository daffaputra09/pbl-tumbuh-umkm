import { cn } from '@/lib/utils';

function Textarea({ className, rows = 3, ...props }) {
    return (
        <textarea
            rows={rows}
            data-slot="textarea"
            className={cn(
                'flex w-full resize-none rounded-xl border border-border bg-white px-4 py-3 text-sm text-ink shadow-xs transition-colors',
                'placeholder:text-muted-foreground',
                'focus-visible:border-primary focus-visible:ring-[3px] focus-visible:ring-ring/25 focus-visible:outline-none',
                'disabled:cursor-not-allowed disabled:opacity-50',
                'aria-invalid:border-destructive aria-invalid:ring-[3px] aria-invalid:ring-destructive/15',
                className,
            )}
            {...props}
        />
    );
}

export { Textarea };
