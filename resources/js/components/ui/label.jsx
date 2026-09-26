import { cn } from '@/lib/utils';

function Label({ className, ...props }) {
    return <label data-slot="label" className={cn('text-sm font-semibold text-ink', className)} {...props} />;
}

export { Label };