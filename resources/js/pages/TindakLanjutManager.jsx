import { useState } from 'react';
import { HugeiconsIcon } from '@hugeicons/react';
import { TeachingIcon, File02Icon, Calendar01Icon, Tag01Icon, Cancel01Icon } from '@hugeicons/core-free-icons';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { cn } from '@/lib/utils';
import { motion, AnimatePresence } from 'motion/react';
import { apiFetch } from '@/lib/api';

export default function TindakLanjutManager({ queue: initialQueue = [] }) {
    const [queue, setQueue] = useState(initialQueue);
    const [selectedItem, setSelectedItem] = useState(null);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [formData, setFormData] = useState({
        planned_activity: '',
        planned_on: '',
        submission_note: ''
    });

    function openModal(item) {
        setSelectedItem(item);
        setFormData({
            planned_activity: '',
            planned_on: '',
            submission_note: ''
        });
    }

    function closeModal() {
        if (isSubmitting) return;
        setSelectedItem(null);
    }

    function handleInputChange(e) {
        setFormData({ ...formData, [e.target.name]: e.target.value });
    }

    async function handleSubmit(e) {
        e.preventDefault();
        setIsSubmitting(true);
        try {
            await apiFetch('/petugas/tindak-lanjut', {
                method: 'POST',
                body: JSON.stringify({
                    business_id: selectedItem.business_id,
                    program_recommendation_id: selectedItem.program_recommendation_id,
                    planned_activity: formData.planned_activity,
                    planned_on: formData.planned_on,
                    submission_note: formData.submission_note,
                }),
            });

            setQueue((prev) => 
                prev.map(item => 
                    item.id === selectedItem.id ? { ...item, status: 'pending' } : item
                )
            );
            closeModal();
            alert('Tindak lanjut berhasil diajukan!');
        } catch (error) {
            alert(error.message);
        } finally {
            setIsSubmitting(false);
        }
    }

    return (
        <div className="space-y-6">
            <header>
                <div>
                    <h1 className="text-2xl font-bold tracking-tight text-ink sm:text-3xl">Ajukan Tindak Lanjut</h1>
                    <p className="mt-1 text-sm text-slate-600 max-w-3xl">
                        Tandai rekomendasi dan ajukan program pembinaan UMKM kepada Kepala Desa untuk disetujui.
                    </p>
                </div>
            </header>

                <Card>
                    <CardHeader className="flex-row items-center gap-3 space-y-0">
                        <span className="flex size-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-primary">
                            <HugeiconsIcon icon={TeachingIcon} size={20} strokeWidth={1.8} aria-hidden="true" />
                        </span>
                        <CardTitle>Antrean Rekomendasi</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div className="overflow-x-auto">
                            <table className="w-full text-left text-sm text-ink">
                                <thead className="bg-slate-50 text-xs text-muted-foreground uppercase">
                                    <tr>
                                        <th className="px-4 py-3 font-semibold rounded-tl-xl">UMKM & Pemilik</th>
                                        <th className="px-4 py-3 font-semibold">Kategori Kendala</th>
                                        <th className="px-4 py-3 font-semibold">Rekomendasi Program</th>
                                        <th className="px-4 py-3 font-semibold rounded-tr-xl">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-border">
                                    {queue.map(item => (
                                        <tr key={item.id} className="hover:bg-slate-50/50 transition-colors">
                                            <td className="px-4 py-3">
                                                <p className="font-semibold">{item.umkm_name}</p>
                                                <p className="text-xs text-muted-foreground">{item.owner_name}</p>
                                            </td>
                                            <td className="px-4 py-3">
                                                <span className="inline-flex items-center gap-1 rounded-md bg-slate-100 px-2 py-1 text-xs font-medium text-slate-600">
                                                    <HugeiconsIcon icon={Tag01Icon} size={14} />
                                                    {item.problem_category}
                                                </span>
                                            </td>
                                            <td className="px-4 py-3 text-sm font-medium">{item.recommended_program}</td>
                                            <td className="px-4 py-3">
                                                {item.status === 'ready' ? (
                                                    <Button size="sm" onClick={() => openModal(item)}>
                                                        Buat Pengajuan
                                                    </Button>
                                                ) : (
                                                    <span className="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-[11px] font-semibold text-amber-700">
                                                        Menunggu Persetujuan
                                                    </span>
                                                )}
                                            </td>
                                        </tr>
                                    ))}
                                    {queue.length === 0 && (
                                        <tr>
                                            <td colSpan="4" className="px-4 py-8 text-center text-muted-foreground">
                                                Tidak ada antrean rekomendasi saat ini.
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </CardContent>
                </Card>

            {/* Modal */}
            <AnimatePresence>
                {selectedItem && (
                    <div className="fixed inset-0 z-50 flex flex-col justify-end lg:items-center lg:justify-center">
                        <motion.button
                            type="button"
                            aria-label="Tutup modal"
                            className="absolute inset-0 bg-ink/40 backdrop-blur-sm"
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            exit={{ opacity: 0 }}
                            onClick={closeModal}
                        />
                        <motion.div
                            className="relative z-10 w-full rounded-t-2xl bg-white shadow-2xl lg:max-w-md lg:rounded-2xl"
                            initial={{ y: '100%' }}
                            animate={{ y: 0 }}
                            exit={{ y: '100%' }}
                            transition={{ type: 'spring', stiffness: 400, damping: 40 }}
                        >
                            <div className="flex items-center justify-between border-b border-border px-5 py-4">
                                <div>
                                    <h3 className="text-lg font-bold text-ink">Buat Pengajuan</h3>
                                    <p className="text-xs text-muted-foreground">UMKM: {selectedItem.umkm_name}</p>
                                </div>
                                <button
                                    type="button"
                                    onClick={closeModal}
                                    className="grid size-8 place-items-center rounded-lg text-slate-500 hover:bg-slate-100"
                                >
                                    <HugeiconsIcon icon={Cancel01Icon} size={20} />
                                </button>
                            </div>
                            <div className="px-5 py-5">
                                <form onSubmit={handleSubmit} className="space-y-4">
                                    <div className="space-y-2">
                                        <label className="text-sm font-semibold text-ink flex items-center gap-1.5">
                                            <HugeiconsIcon icon={File02Icon} size={16} /> Rencana Kegiatan
                                        </label>
                                        <Input
                                            name="planned_activity"
                                            value={formData.planned_activity}
                                            onChange={handleInputChange}
                                            placeholder="Contoh: Mengikuti workshop pemasaran..."
                                            required
                                        />
                                    </div>
                                    <div className="space-y-2">
                                        <label className="text-sm font-semibold text-ink flex items-center gap-1.5">
                                            <HugeiconsIcon icon={Calendar01Icon} size={16} /> Rencana Tanggal Pelaksanaan
                                        </label>
                                        <Input
                                            type="date"
                                            name="planned_on"
                                            value={formData.planned_on}
                                            onChange={handleInputChange}
                                            required
                                        />
                                    </div>
                                    <div className="space-y-2">
                                        <label className="text-sm font-semibold text-ink flex items-center gap-1.5">
                                            Catatan Petugas
                                        </label>
                                        <Textarea
                                            name="submission_note"
                                            value={formData.submission_note}
                                            onChange={handleInputChange}
                                            placeholder="Tuliskan catatan atau justifikasi untuk kepala desa..."
                                            rows={4}
                                            required
                                        />
                                    </div>
                                    <div className="pt-4">
                                        <Button type="submit" className="w-full" disabled={isSubmitting}>
                                            {isSubmitting ? 'Mengirim...' : 'Kirim Pengajuan'}
                                        </Button>
                                    </div>
                                </form>
                            </div>
                        </motion.div>
                    </div>
                )}
            </AnimatePresence>
        </div>
    );
}
