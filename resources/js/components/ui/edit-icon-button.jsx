import { cn } from '@/lib/utils';

function EditIconButton({ label, className, ...props }) {
    return (
        <button
            type="button"
            title={label}
            aria-label={label}
            className={cn(
                'grid size-8 place-items-center rounded-lg border border-slate-200 text-slate-600 transition-colors hover:border-primary hover:bg-brand-50 hover:text-primary',
                className,
            )}
            {...props}
        >
            <svg
                width="15"
                height="15"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                strokeWidth="2"
                strokeLinecap="round"
                strokeLinejoin="round"
                aria-hidden="true"
            >
                <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />
                <path d="m15 5 4 4" />
            </svg>
        </button>
    );
}

export { EditIconButton };
