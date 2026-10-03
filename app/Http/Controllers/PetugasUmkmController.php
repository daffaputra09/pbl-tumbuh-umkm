<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class PetugasUmkmController extends Controller
{
    /**
     * Data mock awal UMKM yang diselaraskan dengan schema target tabel businesses
     * (verification_status, verification_note, verified_by, verified_at, dll).
     */
    private function getInitialMockData(): array
    {
        return [
            [
                'id'                  => 1,
                'business_name'       => 'Keripik Tempe Barokah',
                'nama_usaha'          => 'Keripik Tempe Barokah',
                'owner_name'          => 'Siti Aminah',
                'nama_pemilik'        => 'Siti Aminah',
                'phone'               => '081234567890',
                'telepon'             => '081234567890',
                'email'               => 'siti.barokah@gmail.com',
                'address'             => 'RT 02/RW 01, Dusun Krajan, Desa Sukamaju',
                'alamat'              => 'RT 02/RW 01, Dusun Krajan, Desa Sukamaju',
                'hamlet'              => 'Krajan',
                'dusun'               => 'Krajan',
                'rt'                  => '02',
                'rw'                  => '01',
                'established_year'    => 2021,
                'employee_count'      => 3,
                'operational_status'  => 'active',
                'kategori_produk'     => 'Makanan & Minuman',
                'business_type'       => 'Makanan & Minuman',
                'kisaran_harga'       => 'Rp 10.000 – Rp 25.000',
                'deskripsi_produk'    => 'Keripik tempe renyah aneka rasa (original, balado, keju) dengan bahan baku kedelai lokal pilihan.',
                'punya_nib'           => true,
                'nomor_nib'           => '1234567890123',
                'punya_npwp'          => false,
                'nomor_npwp'          => null,
                'status_halal'        => 'proses',
                'kendala'             => [
                    'modal'        => ['Butuh tambahan modal usaha'],
                    'pemasaran'    => ['Belum paham cara promosi lewat internet', 'Jangkauan pembeli masih terbatas di sekitar rumah'],
                    'legalitas'    => ['Bingung cara mengurus sertifikasi halal'],
                    'produksi'     => ['Kemasan produk masih kurang menarik'],
                    'digitalisasi' => ['Belum menerima pembayaran lewat QRIS'],
                ],
                'catatan_kebutuhan'   => 'Ingin dibantu pembuatan kemasan standing pouch dan foto produk untuk jualan online.',
                'verification_status' => 'pending',
                'status_verifikasi'   => 'menunggu',
                'verification_note'   => null,
                'catatan_petugas'     => null,
                'verified_by'         => null,
                'verified_at'         => null,
                'tanggal_daftar'      => '2026-09-26',
                'created_at'          => '2026-09-26 09:30:00',
            ],
            [
                'id'                  => 2,
                'business_name'       => 'Batik Tulis Mekar Sari',
                'nama_usaha'          => 'Batik Tulis Mekar Sari',
                'owner_name'          => 'Budi Santoso',
                'nama_pemilik'        => 'Budi Santoso',
                'phone'               => '085698712345',
                'telepon'             => '085698712345',
                'email'               => 'batikmekarsari@gmail.com',
                'address'             => 'RT 05/RW 03, Dusun Sumber, Desa Sukamaju',
                'alamat'              => 'RT 05/RW 03, Dusun Sumber, Desa Sukamaju',
                'hamlet'              => 'Sumber',
                'dusun'               => 'Sumber',
                'rt'                  => '05',
                'rw'                  => '03',
                'established_year'    => 2018,
                'employee_count'      => 6,
                'operational_status'  => 'active',
                'kategori_produk'     => 'Kerajinan Tangan',
                'business_type'       => 'Kerajinan Tangan',
                'kisaran_harga'       => 'Rp 150.000 – Rp 500.000',
                'deskripsi_produk'    => 'Batik tulis tangan motif khas desa dengan pewarna alami dari daun indigo dan kulit kayu secang.',
                'punya_nib'           => true,
                'nomor_nib'           => '9876543210987',
                'punya_npwp'          => true,
                'nomor_npwp'          => '12.345.678.9-012.345',
                'status_halal'        => 'belum',
                'kendala'             => [
                    'modal'        => [],
                    'pemasaran'    => ['Jangkauan pembeli masih terbatas di sekitar rumah', 'Belum paham cara promosi lewat internet'],
                    'legalitas'    => [],
                    'produksi'     => ['Peralatan usaha masih terbatas', 'Bahan baku mahal atau susah dicari'],
                    'digitalisasi' => ['Belum punya akun media sosial untuk usaha'],
                ],
                'catatan_kebutuhan'   => 'Membutuhkan pelatihan fotografi produk dan cara memasarkan via Instagram & marketplace.',
                'verification_status' => 'verified',
                'status_verifikasi'   => 'terverifikasi',
                'verification_note'   => 'Data lengkap, legalitas NIB dan NPWP sudah ada, usaha aktif berjalan.',
                'catatan_petugas'     => 'Data lengkap, legalitas NIB dan NPWP sudah ada, usaha aktif berjalan.',
                'verified_by'         => 'Petugas Desa',
                'verified_at'         => '2026-09-22 14:15:00',
                'tanggal_daftar'      => '2026-09-20',
                'created_at'          => '2026-09-20 11:00:00',
            ],
            [
                'id'                  => 3,
                'business_name'       => 'Warung Makan Pak Soleh',
                'nama_usaha'          => 'Warung Makan Pak Soleh',
                'owner_name'          => 'Muhammad Soleh',
                'nama_pemilik'        => 'Muhammad Soleh',
                'phone'               => '082112345678',
                'telepon'             => '082112345678',
                'email'               => null,
                'address'             => 'RT 01/RW 02, Dusun Rejo, Desa Sukamaju',
                'alamat'              => 'RT 01/RW 02, Dusun Rejo, Desa Sukamaju',
                'hamlet'              => 'Rejo',
                'dusun'               => 'Rejo',
                'rt'                  => '01',
                'rw'                  => '02',
                'established_year'    => 2022,
                'employee_count'      => 2,
                'operational_status'  => 'active',
                'kategori_produk'     => 'Makanan & Minuman',
                'business_type'       => 'Makanan & Minuman',
                'kisaran_harga'       => 'Rp 8.000 – Rp 20.000',
                'deskripsi_produk'    => 'Warung makan rumahan menyajikan menu pecel, nasi campur, dan soto ayam setiap hari.',
                'punya_nib'           => false,
                'nomor_nib'           => null,
                'punya_npwp'          => false,
                'nomor_npwp'          => null,
                'status_halal'        => 'belum',
                'kendala'             => [
                    'modal'        => ['Butuh tambahan modal usaha', 'Kesulitan mengatur pembukuan atau catatan keuangan'],
                    'pemasaran'    => [],
                    'legalitas'    => ['Belum punya izin usaha (NIB)', 'Belum punya NPWP untuk usaha'],
                    'produksi'     => [],
                    'digitalisasi' => ['Belum menerima pembayaran lewat QRIS', 'Belum punya akun media sosial untuk usaha'],
                ],
                'catatan_kebutuhan'   => 'Perlu bantuan pengurusan NIB dan NPWP usaha. Belum pernah memanfaatkan teknologi digital.',
                'verification_status' => 'rejected',
                'status_verifikasi'   => 'ditolak',
                'verification_note'   => 'Data legalitas belum ada (tidak punya NIB/NPWP). Mohon lengkapi perizinan usaha terlebih dahulu.',
                'catatan_petugas'     => 'Data legalitas belum ada (tidak punya NIB/NPWP). Mohon lengkapi perizinan usaha terlebih dahulu.',
                'verified_by'         => 'Petugas Desa',
                'verified_at'         => '2026-09-24 10:20:00',
                'tanggal_daftar'      => '2026-09-22',
                'created_at'          => '2026-09-22 13:40:00',
            ],
            [
                'id'                  => 4,
                'business_name'       => 'Jahit Rapi Bu Kartini',
                'nama_usaha'          => 'Jahit Rapi Bu Kartini',
                'owner_name'          => 'Kartini Wulandari',
                'nama_pemilik'        => 'Kartini Wulandari',
                'phone'               => '089678901234',
                'telepon'             => '089678901234',
                'email'               => 'kartini.jahit@yahoo.com',
                'address'             => 'RT 03/RW 04, Dusun Lor, Desa Sukamaju',
                'alamat'              => 'RT 03/RW 04, Dusun Lor, Desa Sukamaju',
                'hamlet'              => 'Lor',
                'dusun'               => 'Lor',
                'rt'                  => '03',
                'rw'                  => '04',
                'established_year'    => 2019,
                'employee_count'      => 4,
                'operational_status'  => 'active',
                'kategori_produk'     => 'Fashion & Tekstil',
                'business_type'       => 'Fashion & Konveksi',
                'kisaran_harga'       => 'Rp 50.000 – Rp 200.000',
                'deskripsi_produk'    => 'Jasa jahit dan permak pakaian, serta memproduksi baju koko, gamis, dan seragam sekolah custom.',
                'punya_nib'           => true,
                'nomor_nib'           => '5566778899001',
                'punya_npwp'          => false,
                'nomor_npwp'          => null,
                'status_halal'        => 'sudah',
                'kendala'             => [
                    'modal'        => ['Sulit mengajukan pinjaman ke bank atau koperasi'],
                    'pemasaran'    => ['Jangkauan pembeli masih terbatas di sekitar rumah'],
                    'legalitas'    => [],
                    'produksi'     => ['Peralatan usaha masih terbatas'],
                    'digitalisasi' => ['Belum pernah coba jualan lewat marketplace online'],
                ],
                'catatan_kebutuhan'   => 'Ingin belajar memasarkan produk lewat Shopee dan Tokopedia. Mesin jahit sudah tua, butuh penggantian.',
                'verification_status' => 'pending',
                'status_verifikasi'   => 'menunggu',
                'verification_note'   => null,
                'catatan_petugas'     => null,
                'verified_by'         => null,
                'verified_at'         => null,
                'tanggal_daftar'      => '2026-09-27',
                'created_at'          => '2026-09-27 08:15:00',
            ],
            [
                'id'                  => 5,
                'business_name'       => 'Pupuk Organik Mas Agung',
                'nama_usaha'          => 'Pupuk Organik Mas Agung',
                'owner_name'          => 'Agung Prabowo',
                'nama_pemilik'        => 'Agung Prabowo',
                'phone'               => '081387654321',
                'telepon'             => '081387654321',
                'email'               => 'agung.pupuk@gmail.com',
                'address'             => 'RT 04/RW 01, Dusun Tengah, Desa Sukamaju',
                'alamat'              => 'RT 04/RW 01, Dusun Tengah, Desa Sukamaju',
                'hamlet'              => 'Tengah',
                'dusun'               => 'Tengah',
                'rt'                  => '04',
                'rw'                  => '01',
                'established_year'    => 2020,
                'employee_count'      => 5,
                'operational_status'  => 'active',
                'kategori_produk'     => 'Pertanian & Perkebunan',
                'business_type'       => 'Pertanian & Perkebunan',
                'kisaran_harga'       => 'Rp 20.000 – Rp 75.000',
                'deskripsi_produk'    => 'Produksi pupuk organik kompos dan pupuk cair dari limbah pertanian untuk sawah dan kebun sayur.',
                'punya_nib'           => true,
                'nomor_nib'           => '7788990011223',
                'punya_npwp'          => true,
                'nomor_npwp'          => '98.765.432.1-054.321',
                'status_halal'        => 'belum',
                'kendala'             => [
                    'modal'        => [],
                    'pemasaran'    => ['Jangkauan pembeli masih terbatas di sekitar rumah', 'Bingung menentukan harga jual yang pas'],
                    'legalitas'    => [],
                    'produksi'     => ['Bahan baku mahal atau susah dicari'],
                    'digitalisasi' => ['Belum menerima pembayaran lewat QRIS'],
                ],
                'catatan_kebutuhan'   => 'Butuh akses ke pasar yang lebih luas, terutama untuk menjual ke kelompok tani desa lain.',
                'verification_status' => 'verified',
                'status_verifikasi'   => 'terverifikasi',
                'verification_note'   => 'Data lengkap dan valid. Usaha aktif dengan NIB dan NPWP sudah terdaftar.',
                'catatan_petugas'     => 'Data lengkap dan valid. Usaha aktif dengan NIB dan NPWP sudah terdaftar.',
                'verified_by'         => 'Petugas Desa',
                'verified_at'         => '2026-09-20 16:00:00',
                'tanggal_daftar'      => '2026-09-18',
                'created_at'          => '2026-09-18 15:00:00',
            ],
        ];
    }

    /**
     * Mengambil seluruh data UMKM dari session (agar perubahan verifikasi/tolak tersimpan).
     */
    private function getAllData(): array
    {
        if (!session()->has('petugas_umkm_data')) {
            session(['petugas_umkm_data' => $this->getInitialMockData()]);
        }

        return session('petugas_umkm_data');
    }

    /**
     * Menyimpan pembaruan data UMKM ke dalam session.
     */
    private function saveAllData(array $data): void
    {
        session(['petugas_umkm_data' => array_values($data)]);
    }

    /**
     * Menampilkan halaman daftar UMKM (Verifikasi & Kelola Data UMKM).
     * Kolom aksi hanya menampilkan tombol "Detail".
     */
    public function verifikasi(Request $request)
    {
        $allData = $this->getAllData();

        // Filter berdasarkan status verifikasi
        $filterStatus = $request->query('status', 'semua');
        if ($filterStatus !== 'semua') {
            $allData = array_filter($allData, function ($u) use ($filterStatus) {
                // Mendukung padanan status bahasa indonesia & kode skema
                if ($filterStatus === 'menunggu' || $filterStatus === 'pending') {
                    return in_array($u['verification_status'], ['pending', 'menunggu'], true);
                }
                if ($filterStatus === 'terverifikasi' || $filterStatus === 'verified') {
                    return in_array($u['verification_status'], ['verified', 'terverifikasi'], true);
                }
                if ($filterStatus === 'ditolak' || $filterStatus === 'rejected') {
                    return in_array($u['verification_status'], ['rejected', 'ditolak'], true);
                }
                return false;
            });
            $allData = array_values($allData);
        }

        // Pencarian berdasarkan nama usaha atau nama pemilik
        $keyword = trim($request->query('q', ''));
        if ($keyword !== '') {
            $allData = array_filter($allData, function ($u) use ($keyword) {
                return str_contains(strtolower($u['business_name'] ?? $u['nama_usaha']), strtolower($keyword))
                    || str_contains(strtolower($u['owner_name'] ?? $u['nama_pemilik']), strtolower($keyword));
            });
            $allData = array_values($allData);
        }

        // Hitung ringkasan statistik dari total seluruh data (sebelum difilter)
        $allRaw = $this->getAllData();
        $stats = [
            'total'         => count($allRaw),
            'menunggu'      => count(array_filter($allRaw, fn($u) => in_array($u['verification_status'], ['pending', 'menunggu'], true))),
            'terverifikasi' => count(array_filter($allRaw, fn($u) => in_array($u['verification_status'], ['verified', 'terverifikasi'], true))),
            'ditolak'       => count(array_filter($allRaw, fn($u) => in_array($u['verification_status'], ['rejected', 'ditolak'], true))),
        ];

        return view('petugas.umkm.verifikasi', [
            'umkmList'     => $allData,
            'stats'        => $stats,
            'filterStatus' => $filterStatus,
            'keyword'      => $keyword,
        ]);
    }

    /**
     * Menampilkan halaman detail lengkap UMKM.
     * Tombol Verifikasi dan Tolak berada di halaman ini.
     */
    public function detail($id)
    {
        $allData = $this->getAllData();
        $umkm = collect($allData)->firstWhere('id', (int) $id);

        if (!$umkm) {
            abort(404, 'Data UMKM tidak ditemukan.');
        }

        return view('petugas.umkm.detail', [
            'umkm' => $umkm,
        ]);
    }

    /**
     * Memproses verifikasi data UMKM dari halaman detail.
     */
    public function prosesVerifikasi(Request $request, $id)
    {
        $allData = $this->getAllData();
        $index = collect($allData)->search(fn($u) => $u['id'] == (int) $id);

        if ($index === false) {
            return redirect()->route('petugas.umkm.verifikasi')->with('error', 'Data UMKM tidak ditemukan.');
        }

        $umkm = $allData[$index];

        // Validasi: hanya data yang berstatus pending/menunggu yang dapat diverifikasi
        if (!in_array($umkm['verification_status'], ['pending', 'menunggu'], true)) {
            return redirect()->route('petugas.umkm.detail', $id)
                ->with('error', 'UMKM ini sudah diproses sebelumnya dan tidak dapat diverifikasi ulang.');
        }

        // Update data verifikasi
        $umkm['verification_status'] = 'verified';
        $umkm['status_verifikasi']   = 'terverifikasi';
        $umkm['verified_at']         = Carbon::now()->toDateTimeString();
        $umkm['verified_by']         = 'Petugas Desa';
        $umkm['verification_note']   = $request->input('verification_note')
            ? trim($request->input('verification_note'))
            : 'Data usaha telah diverifikasi dan dinyatakan valid oleh petugas desa.';
        $umkm['catatan_petugas']     = $umkm['verification_note'];

        $allData[$index] = $umkm;
        $this->saveAllData($allData);

        return redirect()->route('petugas.umkm.detail', $id)
            ->with('success', 'Data UMKM "' . $umkm['nama_usaha'] . '" berhasil diverifikasi!');
    }

    /**
     * Memproses penolakan data UMKM dengan alasan/catatan dari halaman detail.
     */
    public function prosesTolak(Request $request, $id)
    {
        $request->validate([
            'verification_note' => 'required|string|min:5',
        ], [
            'verification_note.required' => 'Alasan penolakan wajib diisi agar pemilik UMKM mengetahui bagian yang perlu diperbaiki.',
            'verification_note.min'      => 'Alasan penolakan minimal berisi 5 karakter.',
        ]);

        $allData = $this->getAllData();
        $index = collect($allData)->search(fn($u) => $u['id'] == (int) $id);

        if ($index === false) {
            return redirect()->route('petugas.umkm.verifikasi')->with('error', 'Data UMKM tidak ditemukan.');
        }

        $umkm = $allData[$index];

        // Validasi: hanya data yang berstatus pending/menunggu yang dapat ditolak
        if (!in_array($umkm['verification_status'], ['pending', 'menunggu'], true)) {
            return redirect()->route('petugas.umkm.detail', $id)
                ->with('error', 'UMKM ini sudah diproses sebelumnya dan tidak dapat ditolak lagi.');
        }

        // Update data penolakan
        $umkm['verification_status'] = 'rejected';
        $umkm['status_verifikasi']   = 'ditolak';
        $umkm['verified_at']         = Carbon::now()->toDateTimeString();
        $umkm['verified_by']         = 'Petugas Desa';
        $umkm['verification_note']   = trim($request->input('verification_note'));
        $umkm['catatan_petugas']     = $umkm['verification_note'];

        $allData[$index] = $umkm;
        $this->saveAllData($allData);

        return redirect()->route('petugas.umkm.detail', $id)
            ->with('warning', 'Data UMKM "' . $umkm['nama_usaha'] . '" telah ditolak dengan catatan penolakan.');
    }
}
