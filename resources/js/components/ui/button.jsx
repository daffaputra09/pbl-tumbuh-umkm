import { Button as ButtonPrimitive } from '@base-ui/react/button';
import { cva } from 'class-variance-authority';
import { cn } from '@/lib/utils';

const buttonVariants = cva(
    "group/button inline-flex shrink-0 cursor-pointer items-center justify-center rounded-full border border-transparent bg-clip-padding text-sm font-semibold whitespace-nowrap transition-all outline-none select-none focus-visible:ring-[3px] focus-visible:ring-ring/40 active:translate-y-px disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:shrink-0 [&_svg:not([class*='size-'])]:size-4",
    {
        variants: {
            variant: {
                default:
                    'bg-primary text-primary-foreground shadow-[0_1px_0_0_rgb(255_255_255/0.2)_inset,0_8px_20px_-8px_rgb(15_118_110/0.7)] hover:bg-brand-hover',
                accent: 'bg-sun text-white shadow-[0_8px_20px_-8px_rgb(217_119_6/0.7)] hover:bg-sun-700',
                outline: 'border-border bg-white text-ink shadow-xs hover:border-brand-200 hover:bg-brand-50',
                secondary: 'bg-secondary text-secondary-foreground hover:bg-brand-100',
                ghost: 'text-ink hover:bg-brand-50 hover:text-brand-hover',
                light: 'bg-white text-brand-hover hover:bg-brand-50',
                'outline-light': 'border-white/30 bg-white/5 text-white hover:bg-white/15',
                link: 'text-primary underline-offset-4 hover:underline',
            },
            size: {
                default: 'h-11 gap-2 px-5',
                sm: 'h-9 gap-1.5 px-4',
                lg: 'h-12 gap-2 px-6 text-[15px]',
                icon: 'size-10',
            },
        },
        defaultVariants: {
            variant: 'default',
            size: 'default',
        },
    },
);

function Button({ className, variant = 'default', size = 'default', ...props }) {
    return <ButtonPrimitive data-slot="button" className={cn(buttonVariants({ variant, size, className }))} {...props} />;
}

export { Button, buttonVariants };
