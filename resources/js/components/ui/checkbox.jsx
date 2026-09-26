import { HugeiconsIcon } from '@hugeicons/react';
import { Tick02Icon } from '@hugeicons/core-free-icons';
import { cn } from '@/lib/utils';

function Checkbox({ className, checked = false, ...props }) {
    return (
        <span
            className={cn(
                'relative mt-0.5 inline-flex size-5 shrink-0 items-center justify-center rounded-md border transition-colors',
                checked ? 'border-primary bg-primary' : 'border-border bg-white',
                className,
            )}
        >
            <input type="checkbox" checked={checked} className="absolute inset-0 size-full cursor-pointer opacity-0" {...props} />
            {checked && <HugeiconsIcon icon={Tick02Icon} size={13} strokeWidth={3} className="text-white" aria-hidden="true" />}
        </span>
    );
}

export { Checkbox };
