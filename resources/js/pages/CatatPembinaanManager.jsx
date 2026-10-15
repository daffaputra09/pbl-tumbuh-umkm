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

const DEMO_ITEMS = [
    {
        id: 'demo-1',
        is_dummy: true,
        business_id: null,
        umkm_name: 'Kripik Kentang Mak Mur',
        owner_name: 'Nazwa Azahra',
        program_name: 'Pelatihan Pemasaran Digital',
        planned_activity: 'Pelatihan Pemasaran Digital Lanjutan',
        planned_on: '2026-10-18',
    },
    {
        id: 'demo-2',
        is_dummy: true,
        business_id: null,
        umkm_name: 'Kerajinan Bambu Lestari',
        owner_name: 'Bapak Ponijan',
        program_name: 'Pengembangan Usaha',
        planned_activity: 'Workshop Manajemen Keuangan Dasar',
        planned_on: '2026-10-22',
    },
];

export default function CatatPembinaanManager({ queue: initialQueue = [] }) {
    const [queue, setQueue] = useState(
        initialQueue && initialQueue.length > 0 ? initialQueue : DEMO_ITEMS
    );
    const [selectedItem, setSelectedItem] = useState(null);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [formData, setFormData] = useState({
        title: '',
        held_on: '',
        description: '',
        outcome: ''
    });

    function openModal(item) {
        setSelectedItem(item);
        setFormData({
            title: item.planned_activity || '',
            held_on: item.planned_on || '',
            description: '',
            outcome: ''
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
            if (selectedItem?.is_dummy) {
                // Simulasi demo tanpa menyimpan ke database
                await new Promise((resolve) => setTimeout(resolve, 500));
            } else {
                await apiFetch('/petugas/catat-pembinaan', {
                    method: 'POST',
                    body: JSON.stringify({
                        follow_up_id: selectedItem.id,
                        title: formData.title,
                        held_on: formData.held_on,
                        description: formData.description,
                        outcome: formData.outcome,
                    }),
                });
            }

            setQueue((prev) => prev.filter(item => item.id !== selectedItem.id));
            closeModal();
            alert('Pembinaan berhasil dicatat!');
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
                    <h1 className="text-2xl font-bold tracking-tight text-ink sm:text-3xl">Catat Pembinaan</h1>
                    <p className="mt-1 text-sm text-slate-600 max-w-3xl">
                        Catat riwayat dan hasil pembinaan UMKM yang telah disetujui.
                    </p>
                </div>
            </header>

            <Card>
                <CardHeader className="flex-row items-center gap-3 space-y-0">
                    <span className="flex size-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-primary">
                        <HugeiconsIcon icon={TeachingIcon} size={20} strokeWidth={1.8} aria-hidden="true" />
                    </span>
                    <CardTitle>Daftar Tindak Lanjut Disetujui</CardTitle>
                </CardHeader>
                <CardContent>
                    <div className="overflow-x-auto">
                        <table className="w-full text-left text-sm text-ink">
                            <thead className="bg-slate-50 text-xs text-muted-foreground uppercase">
                                <tr>
                                    <th className="px-4 py-3 font-semibold rounded-tl-xl">UMKM & Pemilik</th>
                                    <th className="px-4 py-3 font-semibold">Program Disetujui</th>
                                    <th className="px-4 py-3 font-semibold">Rencana Kegiatan</th>
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
                                                {item.program_name}
                                            </span>
                                        </td>
                                        <td className="px-4 py-3">
                                            <p className="font-medium text-sm">{item.planned_activity}</p>
                                            <p className="text-xs text-muted-foreground">{item.planned_on || '-'}</p>
                                        </td>
                                        <td className="px-4 py-3">
                                            <Button size="sm" onClick={() => openModal(item)}>
                                                Catat Pembinaan
                                            </Button>
                                        </td>
                                    </tr>
                                ))}
                                {queue.length === 0 && (
                                    <tr>
                                        <td colSpan="4" className="px-4 py-8 text-center text-muted-foreground">
                                            <p className="mb-3">Tidak ada data tindak lanjut yang menunggu dicatat.</p>
                                            <Button
                                                type="button"
                                                variant="outline"
                                                size="sm"
                                                onClick={() => setQueue(DEMO_ITEMS)}
                                            >
                                                Muat Contoh Data Demo
                                            </Button>
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>

            {/* Modal Form */}
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
                            className="relative z-10 w-full rounded-t-2xl bg-white shadow-2xl lg:max-w-xl lg:rounded-2xl max-h-[90vh] overflow-y-auto"
                            initial={{ y: '100%' }}
                            animate={{ y: 0 }}
                            exit={{ y: '100%' }}
                            transition={{ type: 'spring', stiffness: 400, damping: 40 }}
                        >
                            <div className="flex items-center justify-between border-b border-border px-5 py-4 sticky top-0 bg-white z-10">
                                <div>
                                    <h3 className="text-lg font-bold text-ink">Catat Pembinaan</h3>
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
                                            <HugeiconsIcon icon={File02Icon} size={16} /> Nama Kegiatan
                                        </label>
                                        <Input
                                            name="title"
                                            value={formData.title}
                                            onChange={handleInputChange}
                                            placeholder="Contoh: Mengikuti pelatihan digital marketing..."
                                            required
                                        />
                                    </div>
                                    <div className="space-y-2">
                                        <label className="text-sm font-semibold text-ink flex items-center gap-1.5">
                                            <HugeiconsIcon icon={Calendar01Icon} size={16} /> Tanggal
                                        </label>
                                        <Input
                                            type="date"
                                            name="held_on"
                                            value={formData.held_on}
                                            onChange={handleInputChange}
                                            required
                                        />
                                    </div>
                                    <div className="space-y-2">
                                        <label className="text-sm font-semibold text-ink flex items-center gap-1.5">
                                            <HugeiconsIcon icon={File02Icon} size={16} /> Deskripsi
                                        </label>
                                        <Textarea
                                            name="description"
                                            value={formData.description}
                                            onChange={handleInputChange}
                                            placeholder="Deskripsikan proses pembinaan yang telah dilakukan..."
                                            rows={3}
                                            required
                                        />
                                    </div>
                                    <div className="space-y-2">
                                        <label className="text-sm font-semibold text-ink flex items-center gap-1.5">
                                            Hasil (Outcome)
                                        </label>
                                        <Textarea
                                            name="outcome"
                                            value={formData.outcome}
                                            onChange={handleInputChange}
                                            placeholder="Deskripsikan hasil yang dicapai (opsional)..."
                                            rows={3}
                                        />
                                    </div>
                                    <div className="pt-4 sticky bottom-0 bg-white pb-2">
                                        <Button type="submit" className="w-full" disabled={isSubmitting}>
                                            {isSubmitting ? 'Menyimpan...' : 'Simpan Data Pembinaan'}
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
