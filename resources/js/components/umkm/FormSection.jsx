import { HugeiconsIcon } from '@hugeicons/react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { cn } from '@/lib/utils';

/**
 * Pembungkus tiap bagian form (Identitas Usaha, Data Produk, Legalitas)
 * supaya tampilannya konsisten: ikon + judul + deskripsi singkat di atas,
 * field-field form disusun dalam grid 2 kolom di bawahnya.
 */
export default function FormSection({ icon, title, description, children, className }) {
    return (
        <Card className={cn('gap-6', className)}>
            <CardHeader className="flex-row items-start gap-3 space-y-0">
                <span className="flex size-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-primary">
                    <HugeiconsIcon icon={icon} size={20} strokeWidth={1.8} aria-hidden="true" />
                </span>
                <div className="flex flex-col gap-1">
                    <CardTitle>{title}</CardTitle>
                    {description && <CardDescription>{description}</CardDescription>}
                </div>
            </CardHeader>
            <CardContent className="grid gap-5 sm:grid-cols-2">{children}</CardContent>
        </Card>
    );
}
