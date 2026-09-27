import { HugeiconsIcon } from '@hugeicons/react';
import { Leaf01Icon } from '@hugeicons/core-free-icons';

export default function PageBackground() {
    return (
        <div aria-hidden className="pointer-events-none absolute inset-0 -z-10 overflow-hidden">
            <div className="absolute -top-24 left-1/4 h-[380px] w-[380px] rounded-full bg-brand-200/35 blur-[110px]" />
            <div className="absolute top-1/3 -right-24 h-72 w-72 rounded-full bg-sun-200/30 blur-[100px]" />
            <div className="absolute bottom-0 left-0 h-64 w-64 rounded-full bg-brand-100/40 blur-[100px]" />

            <HugeiconsIcon
                icon={Leaf01Icon}
                size={40}
                strokeWidth={1.4}
                className="animate-float-slow absolute top-28 left-[6%] hidden text-brand-300/50 lg:block"
            />
            <HugeiconsIcon
                icon={Leaf01Icon}
                size={30}
                strokeWidth={1.4}
                className="animate-float absolute right-[8%] bottom-32 hidden text-sun/40 lg:block"
            />
        </div>
    );
}
