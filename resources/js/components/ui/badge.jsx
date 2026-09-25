import { cva } from 'class-variance-authority';
import { cn } from '@/lib/utils';

const badgeVariants = cva(
    'inline-flex w-fit shrink-0 items-center justify-center gap-1.5 rounded-full border px-3 py-1 text-xs font-semibold whitespace-nowrap [&_svg]:pointer-events-none [&_svg]:size-3.5',
    {
        variants: {
            variant: {
                default: 'border-brand-200 bg-brand-50 text-brand-hover',
                accent: 'border-sun-200 bg-sun-50 text-sun-700',
                success: 'border-green-200 bg-green-50 text-success',
                outline: 'border-border bg-white text-ink',
                dark: 'border-white/15 bg-white/10 text-white',
            },
        },
        defaultVariants: {
            variant: 'default',
        },
    },
);

function Badge({ className, variant, ...props }) {
    return <span data-slot="badge" className={cn(badgeVariants({ variant }), className)} {...props} />;
}

export { Badge, badgeVariants };
