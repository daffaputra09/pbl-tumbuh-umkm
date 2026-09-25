import { Accordion as AccordionPrimitive } from '@base-ui/react/accordion';
import { HugeiconsIcon } from '@hugeicons/react';
import { PlusSignIcon } from '@hugeicons/core-free-icons';
import { cn } from '@/lib/utils';

function Accordion({ className, ...props }) {
    return <AccordionPrimitive.Root data-slot="accordion" className={cn('flex w-full flex-col', className)} {...props} />;
}

function AccordionItem({ className, ...props }) {
    return <AccordionPrimitive.Item data-slot="accordion-item" className={cn('border-b last:border-b-0', className)} {...props} />;
}

function AccordionTrigger({ className, children, ...props }) {
    return (
        <AccordionPrimitive.Header className="flex">
            <AccordionPrimitive.Trigger
                data-slot="accordion-trigger"
                className={cn(
                    'group/trigger flex flex-1 cursor-pointer items-center justify-between gap-6 rounded-lg py-5 text-left text-base font-semibold text-ink transition-colors outline-none hover:text-brand focus-visible:ring-[3px] focus-visible:ring-ring/40',
                    className,
                )}
                {...props}
            >
                {children}
                <span className="grid size-8 shrink-0 place-items-center rounded-full border bg-white text-brand transition-all duration-300 group-data-[panel-open]/trigger:rotate-45 group-data-[panel-open]/trigger:border-brand group-data-[panel-open]/trigger:bg-brand group-data-[panel-open]/trigger:text-white">
                    <HugeiconsIcon icon={PlusSignIcon} size={16} strokeWidth={2} />
                </span>
            </AccordionPrimitive.Trigger>
        </AccordionPrimitive.Header>
    );
}

function AccordionContent({ className, children, ...props }) {
    return (
        <AccordionPrimitive.Panel
            data-slot="accordion-content"
            className="h-(--accordion-panel-height) overflow-hidden text-[15px] leading-relaxed text-muted-foreground transition-[height] duration-300 ease-out data-ending-style:h-0 data-starting-style:h-0"
            {...props}
        >
            <div className={cn('pr-12 pb-5', className)}>{children}</div>
        </AccordionPrimitive.Panel>
    );
}

export { Accordion, AccordionItem, AccordionTrigger, AccordionContent };
