import { cn } from '@/lib/utils';

function Input({ className, type = 'text', ...props }) {
    return (
        <input
            type={type}
            data-slot="input"
            className={cn(
                'flex h-11 w-full rounded-xl border border-border bg-white px-4 text-sm text-ink shadow-xs transition-colors',
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

export { Input };
