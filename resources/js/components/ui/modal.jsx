import { useEffect, useRef } from 'react';
import { HugeiconsIcon } from '@hugeicons/react';
import { Cancel01Icon } from '@hugeicons/core-free-icons';
import { cn } from '@/lib/utils';

function Modal({ open, title, onClose, children, className }) {
    const onCloseRef = useRef(onClose);
    onCloseRef.current = onClose;

    useEffect(() => {
        if (!open) {
            return undefined;
        }

        const previousOverflow = document.body.style.overflow;
        document.body.style.overflow = 'hidden';

        function handleKeyDown(event) {
            if (event.key === 'Escape') {
                onCloseRef.current();
            }
        }

        window.addEventListener('keydown', handleKeyDown);

        return () => {
            document.body.style.overflow = previousOverflow;
            window.removeEventListener('keydown', handleKeyDown);
        };
    }, [open]);

    if (!open) {
        return null;
    }

    function handleBackdropMouseDown(event) {
        if (event.target.dataset.modalBackdrop !== undefined) {
            onCloseRef.current();
        }
    }

    return (
        <div
            data-modal-backdrop
            className="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 p-4 backdrop-blur-sm sm:p-6"
            role="dialog"
            aria-modal="true"
            aria-label={title}
            onMouseDown={handleBackdropMouseDown}
        >
            <div data-modal-backdrop className="flex min-h-full items-center justify-center">
                <div className={cn('w-full max-w-lg rounded-2xl border border-slate-200 bg-white p-6 shadow-xl', className)}>
                    <div className="mb-5 flex items-center justify-between gap-3">
                        <h2 className="text-lg font-bold text-ink">{title}</h2>
                        <button
                            type="button"
                            onClick={() => onCloseRef.current()}
                            aria-label="Tutup"
                            className="grid size-8 place-items-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600"
                        >
                            <HugeiconsIcon icon={Cancel01Icon} size={18} aria-hidden="true" />
                        </button>
                    </div>

                    {children}
                </div>
            </div>
        </div>
    );
}

export { Modal };
