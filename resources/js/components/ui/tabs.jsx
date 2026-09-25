import { Tabs as TabsPrimitive } from '@base-ui/react/tabs';
import { cn } from '@/lib/utils';

function Tabs({ className, ...props }) {
    return <TabsPrimitive.Root data-slot="tabs" className={cn('flex flex-col gap-6', className)} {...props} />;
}

function TabsList({ className, children, indicatorClassName, ...props }) {
    return (
        <TabsPrimitive.List
            data-slot="tabs-list"
            className={cn('relative z-0 inline-flex w-fit items-center gap-1 rounded-full border bg-white p-1 shadow-xs', className)}
            {...props}
        >
            {children}
            <TabsPrimitive.Indicator
                className={cn(
                    'absolute top-(--active-tab-top) bottom-(--active-tab-bottom) left-0 -z-10 w-(--active-tab-width) translate-x-(--active-tab-left) rounded-full bg-brand transition-all duration-300 ease-out',
                    indicatorClassName,
                )}
            />
        </TabsPrimitive.List>
    );
}

function TabsTrigger({ className, ...props }) {
    return (
        <TabsPrimitive.Tab
            data-slot="tabs-trigger"
            className={cn(
                'inline-flex h-11 cursor-pointer items-center justify-center gap-2 rounded-full px-4 text-sm font-semibold whitespace-nowrap text-muted-foreground transition-colors outline-none hover:text-ink focus-visible:ring-[3px] focus-visible:ring-ring/40 data-active:text-white data-active:hover:text-white [&_svg]:size-4 [&_svg]:shrink-0',
                className,
            )}
            {...props}
        />
    );
}

function TabsContent({ className, ...props }) {
    return <TabsPrimitive.Panel data-slot="tabs-content" className={cn('outline-none', className)} {...props} />;
}

export { Tabs, TabsList, TabsTrigger, TabsContent };
