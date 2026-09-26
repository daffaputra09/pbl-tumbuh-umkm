import { HugeiconsIcon } from '@hugeicons/react';
import { ArrowDown01Icon } from '@hugeicons/core-free-icons';
import { cn } from '@/lib/utils';

function Select({ className, children, ...props }) {
    return (
        <div className="relative">
            <select
                data-slot="select"
                className={cn(
                    'h-11 w-full appearance-none rounded-xl border border-border bg-white px-4 pr-10 text-sm text-ink shadow-xs transition-colors',
                    'focus-visible:border-primary focus-visible:ring-[3px] focus-visible:ring-ring/25 focus-visible:outline-none',
                    'disabled:cursor-not-allowed disabled:opacity-50',
                    'aria-invalid:border-destructive aria-invalid:ring-[3px] aria-invalid:ring-destructive/15',
                    className,
                )}
                {...props}
            >
                {children}
            </select>
            <HugeiconsIcon
                icon={ArrowDown01Icon}
                size={16}
                strokeWidth={2}
                className="pointer-events-none absolute top-1/2 right-3.5 -translate-y-1/2 text-muted-foreground"
                aria-hidden="true"
            />
        </div>
    );
}

export { Select };
