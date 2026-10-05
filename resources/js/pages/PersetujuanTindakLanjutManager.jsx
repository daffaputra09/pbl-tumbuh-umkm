import { useState } from 'react';
import { HugeiconsIcon } from '@hugeicons/react';
import { CheckListIcon, Cancel01Icon, File02Icon, Calendar01Icon, Tick01Icon } from '@hugeicons/core-free-icons';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Textarea } from '@/components/ui/textarea';
import { cn } from '@/lib/utils';
import { motion, AnimatePresence } from 'motion/react';

const DUMMY_DATA = [
    {
        id: 1,
        umkm_name: "Warung Makan Berkah",
        recommended_program: "Pelatihan Digital Marketing",
        planned_activity: "Mengikuti workshop pemasaran online",
        planned_on: "2026-10-15",
        submission_note: "UMKM perlu meningkatkan penjualan online.",
        status: "pending"
    },
    {
        id: 2,
        umkm_name: "Kerajinan Rotan Indah",
        recommended_program: "Bantuan Kredit Usaha Rakyat",
        planned_activity: "Mengurus permohonan kredit ke bank",
        planned_on: "2026-10-20",
        submission_note: "Kapasitas produksi menurun karena kurang modal.",
        status: "pending"
    },
    {
        id: 3,
        umkm_name: "Kopi Desa",
        recommended_program: "Fasilitasi Sertifikasi Halal",
        planned_activity: "Melengkapi dokumen pengajuan sertifikasi",
        planned_on: "2026-10-25",
        submission_note: "Produk sudah siap jual ke ritel, butuh legalitas halal.",
        status: "pending"
    }
];

export default function PersetujuanTindakLanjutManager() {
    const [queue, setQueue] = useState(DUMMY_DATA);
    const [selectedItem, setSelectedItem] = useState(null);
    const [actionType, setActionType] = useState(null); // 'approve' or 'reject'
    const [formData, setFormData] = useState({
        decision_note: ''
    });

    function openModal(item, type) {
        setSelectedItem(item);
        setActionType(type);
        setFormData({
            decision_note: ''
        });
    }

    function closeModal() {
        setSelectedItem(null);
        setActionType(null);
    }

    function handleInputChange(e) {
        setFormData({ ...formData, [e.target.name]: e.target.value });
    }

    function handleSubmit(e) {
        e.preventDefault();
        const finalStatus = actionType === 'approve' ? 'approved' : 'rejected';
        setQueue((prev) => 
            prev.map(item => 
                item.id === selectedItem.id ? { ...item, status: finalStatus } : item
            )
        );
        closeModal();
    }

    const todayDate = new Date().toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });

    return (
        <div className="relative isolate -mx-4 -mt-5 -mb-5 overflow-hidden bg-white sm:-mx-6 sm:-mt-6 sm:-mb-6 lg:-mx-8 lg:-mt-8 lg:-mb-8">
            <div className="mx-auto max-w-7xl px-4 py-10 sm:px-6 sm:py-14 lg:px-8">
                <header className="mb-8">
                    <p className="text-sm font-semibold text-primary">Keputusan</p>
                    <h1 className="mt-1 text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Persetujuan Tindak Lanjut</h1>
                    <p className="mt-2 text-sm leading-relaxed text-muted-foreground">
                        Tinjau dan berikan keputusan (Setujui atau Tolak) atas pengajuan program pembinaan UMKM dari Petugas Desa.
                    </p>
                </header>

                <Card>
                    <CardHeader className="flex-row items-center gap-3 space-y-0">
                        <span className="flex size-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-primary">
                            <HugeiconsIcon icon={CheckListIcon} size={20} strokeWidth={1.8} aria-hidden="true" />
                        </span>
                        <CardTitle>Antrean Persetujuan</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div className="overflow-x-auto">
                            <table className="w-full text-left text-sm text-ink">
                                <thead className="bg-slate-50 text-xs text-muted-foreground uppercase">
                                    <tr>
                                        <th className="px-4 py-3 font-semibold rounded-tl-xl">Nama UMKM</th>
                                        <th className="px-4 py-3 font-semibold">Program & Kegiatan</th>
                                        <th className="px-4 py-3 font-semibold">Tanggal Rencana</th>
                                        <th className="px-4 py-3 font-semibold">Catatan Petugas</th>
                                        <th className="px-4 py-3 font-semibold rounded-tr-xl">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-border">
                                    {queue.map(item => (
                                        <tr key={item.id} className="hover:bg-slate-50/50 transition-colors">
                                            <td className="px-4 py-3 font-semibold">{item.umkm_name}</td>
                                            <td className="px-4 py-3">
                                                <p className="font-medium text-brand">{item.recommended_program}</p>
                                                <p className="text-xs text-muted-foreground mt-0.5 flex items-center gap-1">
                                                    <HugeiconsIcon icon={File02Icon} size={12} />
                                                    {item.planned_activity}
                                                </p>
                                            </td>
                                            <td className="px-4 py-3">
                                                <span className="inline-flex items-center gap-1 rounded-md bg-slate-100 px-2 py-1 text-xs font-medium text-slate-600">
                                                    <HugeiconsIcon icon={Calendar01Icon} size={14} />
                                                    {item.planned_on}
                                                </span>
                                            </td>
                                            <td className="px-4 py-3 text-xs italic text-slate-600 max-w-[200px] truncate" title={item.submission_note}>
                                                "{item.submission_note}"
                                            </td>
                                            <td className="px-4 py-3">
                                                {item.status === 'pending' ? (
                                                    <div className="flex items-center gap-2">
                                                        <Button size="sm" onClick={() => openModal(item, 'approve')} className="bg-success hover:bg-success/90 text-white border-transparent">
                                                            Setujui
                                                        </Button>
                                                        <Button size="sm" variant="outline" onClick={() => openModal(item, 'reject')} className="text-red-600 border-red-200 hover:bg-red-50">
                                                            Tolak
                                                        </Button>
                                                    </div>
                                                ) : (
                                                    <span className={cn(
                                                        "inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-semibold",
                                                        item.status === 'approved' ? "bg-success/15 text-success" : "bg-red-100 text-red-700"
                                                    )}>
                                                        {item.status === 'approved' ? 'Disetujui' : 'Ditolak'}
                                                    </span>
                                                )}
                                            </td>
                                        </tr>
                                    ))}
                                    {queue.length === 0 && (
                                        <tr>
                                            <td colSpan="5" className="px-4 py-8 text-center text-muted-foreground">
                                                Tidak ada antrean persetujuan saat ini.
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </CardContent>
                </Card>
            </div>

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
                            className="relative z-10 w-full rounded-t-2xl bg-white shadow-2xl lg:max-w-md lg:rounded-2xl overflow-hidden"
                            initial={{ y: '100%' }}
                            animate={{ y: 0 }}
                            exit={{ y: '100%' }}
                            transition={{ type: 'spring', stiffness: 400, damping: 40 }}
                        >
                            <div className={cn(
                                "flex items-center justify-between border-b px-5 py-4",
                                actionType === 'approve' ? "border-success/20 bg-success/10" : "border-red-100 bg-red-50/50"
                            )}>
                                <div className="flex items-center gap-3">
                                    <span className={cn(
                                        "flex size-8 shrink-0 items-center justify-center rounded-full",
                                        actionType === 'approve' ? "bg-success/20 text-success" : "bg-red-100 text-red-600"
                                    )}>
                                        <HugeiconsIcon icon={actionType === 'approve' ? Tick01Icon : Cancel01Icon} size={18} strokeWidth={2} />
                                    </span>
                                    <div>
                                        <h3 className="text-lg font-bold text-ink">
                                            {actionType === 'approve' ? 'Setujui Pengajuan' : 'Tolak Pengajuan'}
                                        </h3>
                                        <p className="text-xs text-muted-foreground">UMKM: {selectedItem.umkm_name}</p>
                                    </div>
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
                                <form onSubmit={handleSubmit} className="space-y-5">
                                    <div className="rounded-xl bg-slate-50 p-3 text-sm">
                                        <div className="grid grid-cols-3 gap-2 mb-2 border-b border-slate-200 pb-2">
                                            <span className="text-muted-foreground">Program:</span>
                                            <span className="col-span-2 font-medium">{selectedItem.recommended_program}</span>
                                        </div>
                                        <div className="grid grid-cols-3 gap-2">
                                            <span className="text-muted-foreground">Tanggal:</span>
                                            <span className="col-span-2 font-medium">{selectedItem.planned_on}</span>
                                        </div>
                                    </div>
                                    
                                    <div className="space-y-2">
                                        <label className="text-sm font-semibold text-ink">
                                            Catatan Keputusan <span className="text-red-500">*</span>
                                        </label>
                                        <Textarea
                                            name="decision_note"
                                            value={formData.decision_note}
                                            onChange={handleInputChange}
                                            placeholder={actionType === 'approve' ? "Tambahkan catatan persetujuan..." : "Alasan penolakan..."}
                                            rows={3}
                                            required
                                        />
                                    </div>

                                    <div className="flex items-center gap-4 text-xs text-muted-foreground bg-slate-50 p-3 rounded-lg border border-slate-100">
                                        <div className="flex-1">
                                            <span className="block font-semibold text-slate-700">Ditetapkan oleh:</span>
                                            Bpk/Ibu Kepala Desa
                                        </div>
                                        <div className="w-px h-8 bg-slate-200"></div>
                                        <div className="flex-1">
                                            <span className="block font-semibold text-slate-700">Tanggal Keputusan:</span>
                                            {todayDate}
                                        </div>
                                    </div>

                                    <div className="pt-2">
                                        <Button 
                                            type="submit" 
                                            className={cn(
                                                "w-full border-transparent shadow-none",
                                                actionType === 'approve' 
                                                    ? "bg-success hover:bg-success/90 text-white" 
                                                    : "bg-red-600 hover:bg-red-700 text-white"
                                            )}
                                        >
                                            {actionType === 'approve' ? 'Konfirmasi Persetujuan' : 'Konfirmasi Penolakan'}
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
