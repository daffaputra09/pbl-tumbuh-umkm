<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PetugasUmkmController extends Controller
{
    private function getMockData(): array
    {
        return [
            [
                'id'                => 1,
                'nama_usaha'        => 'Keripik Tempe Barokah',
                'nama_pemilik'      => 'Siti Aminah',
                'telepon'           => '081234567890',
                'alamat'            => 'RT 02/RW 01, Dusun Krajan, Desa Sukamaju',
                'kategori_produk'   => 'Makanan & Minuman',
                'kisaran_harga'     => 'Rp 10.000 – Rp 25.000',
                'deskripsi_produk'  => 'Keripik tempe renyah aneka rasa (original, balado, keju) dengan bahan baku kedelai lokal pilihan.',
                'punya_nib'         => true,
                'nomor_nib'         => '1234567890123',
                'punya_npwp'        => false,
                'nomor_npwp'        => null,
                'status_halal'      => 'proses',
                'kendala'           => [
                    'modal'        => ['Butuh tambahan modal usaha'],
                    'pemasaran'    => ['Belum paham cara promosi lewat internet', 'Jangkauan pembeli masih terbatas di sekitar rumah'],
                    'legalitas'    => ['Bingung cara mengurus sertifikasi halal'],
                    'produksi'     => ['Kemasan produk masih kurang menarik'],
                    'digitalisasi' => ['Belum menerima pembayaran lewat QRIS'],
                ],
                'catatan_kebutuhan' => 'Ingin dibantu pembuatan kemasan standing pouch dan foto produk untuk jualan online.',
                'status_verifikasi' => 'menunggu',
                'catatan_petugas'   => null,
                'tanggal_daftar'    => '2026-09-26',
            ],
            [
                'id'                => 2,
                'nama_usaha'        => 'Batik Tulis Mekar Sari',
                'nama_pemilik'      => 'Budi Santoso',
                'telepon'           => '085698712345',
                'alamat'            => 'RT 05/RW 03, Dusun Sumber, Desa Sukamaju',
                'kategori_produk'   => 'Kerajinan Tangan',
                'kisaran_harga'     => 'Rp 150.000 – Rp 500.000',
                'deskripsi_produk'  => 'Batik tulis tangan motif khas desa dengan pewarna alami dari daun indigo dan kulit kayu secang.',
                'punya_nib'         => true,
                'nomor_nib'         => '9876543210987',
                'punya_npwp'        => true,
                'nomor_npwp'        => '12.345.678.9-012.345',
                'status_halal'      => 'belum',
                'kendala'           => [
                    'modal'        => [],
                    'pemasaran'    => ['Jangkauan pembeli masih terbatas di sekitar rumah', 'Belum paham cara promosi lewat internet'],
                    'legalitas'    => [],
                    'produksi'     => ['Peralatan usaha masih terbatas', 'Bahan baku mahal atau susah dicari'],
                    'digitalisasi' => ['Belum punya akun media sosial untuk usaha'],
                ],
                'catatan_kebutuhan' => 'Membutuhkan pelatihan fotografi produk dan cara memasarkan via Instagram & marketplace.',
                'status_verifikasi' => 'terverifikasi',
                'catatan_petugas'   => 'Data lengkap, legalitas NIB dan NPWP sudah ada, usaha aktif berjalan.',
                'tanggal_daftar'    => '2026-09-20',
            ],
            [
                'id'                => 3,
                'nama_usaha'        => 'Warung Makan Pak Soleh',
                'nama_pemilik'      => 'Muhammad Soleh',
                'telepon'           => '082112345678',
                'alamat'            => 'RT 01/RW 02, Dusun Rejo, Desa Sukamaju',
                'kategori_produk'   => 'Makanan & Minuman',
                'kisaran_harga'     => 'Rp 8.000 – Rp 20.000',
                'deskripsi_produk'  => 'Warung makan rumahan menyajikan menu pecel, nasi campur, dan soto ayam setiap hari.',
                'punya_nib'         => false,
                'nomor_nib'         => null,
                'punya_npwp'        => false,
                'nomor_npwp'        => null,
                'status_halal'      => 'belum',
                'kendala'           => [
                    'modal'        => ['Butuh tambahan modal usaha', 'Kesulitan mengatur pembukuan atau catatan keuangan'],
                    'pemasaran'    => [],
                    'legalitas'    => ['Belum punya izin usaha (NIB)', 'Belum punya NPWP untuk usaha'],
                    'produksi'     => [],
                    'digitalisasi' => ['Belum menerima pembayaran lewat QRIS', 'Belum punya akun media sosial untuk usaha'],
                ],
                'catatan_kebutuhan' => 'Perlu bantuan pengurusan NIB dan NPWP usaha. Belum pernah memanfaatkan teknologi digital.',
                'status_verifikasi' => 'ditolak',
                'catatan_petugas'   => 'Data legalitas belum ada (tidak punya NIB/NPWP). Mohon lengkapi terlebih dahulu sebelum diverifikasi.',
                'tanggal_daftar'    => '2026-09-22',
            ],
            [
                'id'                => 4,
                'nama_usaha'        => 'Jahit Rapi Bu Kartini',
                'nama_pemilik'      => 'Kartini Wulandari',
                'telepon'           => '089678901234',
                'alamat'            => 'RT 03/RW 04, Dusun Lor, Desa Sukamaju',
                'kategori_produk'   => 'Fashion & Tekstil',
                'kisaran_harga'     => 'Rp 50.000 – Rp 200.000',
                'deskripsi_produk'  => 'Jasa jahit dan permak pakaian, serta memproduksi baju koko, gamis, dan seragam sekolah custom.',
                'punya_nib'         => true,
                'nomor_nib'         => '5566778899001',
                'punya_npwp'        => false,
                'nomor_npwp'        => null,
                'status_halal'      => 'sudah',
                'kendala'           => [
                    'modal'        => ['Sulit mengajukan pinjaman ke bank atau koperasi'],
                    'pemasaran'    => ['Jangkauan pembeli masih terbatas di sekitar rumah'],
                    'legalitas'    => [],
                    'produksi'     => ['Peralatan usaha masih terbatas'],
                    'digitalisasi' => ['Belum pernah coba jualan lewat marketplace online'],
                ],
                'catatan_kebutuhan' => 'Ingin belajar memasarkan produk lewat Shopee dan Tokopedia. Mesin jahit sudah tua, butuh penggantian.',
                'status_verifikasi' => 'menunggu',
                'catatan_petugas'   => null,
                'tanggal_daftar'    => '2026-09-27',
            ],
            [
                'id'                => 5,
                'nama_usaha'        => 'Pupuk Organik Mas Agung',
                'nama_pemilik'      => 'Agung Prabowo',
                'telepon'           => '081387654321',
                'alamat'            => 'RT 04/RW 01, Dusun Tengah, Desa Sukamaju',
                'kategori_produk'   => 'Pertanian & Perkebunan',
                'kisaran_harga'     => 'Rp 20.000 – Rp 75.000',
                'deskripsi_produk'  => 'Produksi pupuk organik kompos dan pupuk cair dari limbah pertanian untuk sawah dan kebun sayur.',
                'punya_nib'         => true,
                'nomor_nib'         => '7788990011223',
                'punya_npwp'        => true,
                'nomor_npwp'        => '98.765.432.1-054.321',
                'status_halal'      => 'belum',
                'kendala'           => [
                    'modal'        => [],
                    'pemasaran'    => ['Jangkauan pembeli masih terbatas di sekitar rumah', 'Bingung menentukan harga jual yang pas'],
                    'legalitas'    => [],
                    'produksi'     => ['Bahan baku mahal atau susah dicari'],
                    'digitalisasi' => ['Belum menerima pembayaran lewat QRIS'],
                ],
                'catatan_kebutuhan' => 'Butuh akses ke pasar yang lebih luas, terutama untuk menjual ke kelompok tani desa lain.',
                'status_verifikasi' => 'terverifikasi',
                'catatan_petugas'   => 'Data lengkap dan valid. Usaha aktif dengan NIB dan NPWP sudah terdaftar.',
                'tanggal_daftar'    => '2026-09-18',
            ],
        ];
    }

    /**
     * Menampilkan halaman daftar & verifikasi data UMKM.
     */
    public function verifikasi(Request $request)
    {
        $allData = $this->getMockData();

        $filterStatus = $request->query('status', 'semua');
        if ($filterStatus !== 'semua') {
            $allData = array_filter($allData, fn($u) => $u['status_verifikasi'] === $filterStatus);
            $allData = array_values($allData);
        }

        $keyword = trim($request->query('q', ''));
        if ($keyword !== '') {
            $allData = array_filter($allData, function ($u) use ($keyword) {
                return str_contains(strtolower($u['nama_usaha']), strtolower($keyword))
                    || str_contains(strtolower($u['nama_pemilik']), strtolower($keyword));
            });
            $allData = array_values($allData);
        }

        $allRaw = $this->getMockData();
        $stats = [
            'total'          => count($allRaw),
            'menunggu'       => count(array_filter($allRaw, fn($u) => $u['status_verifikasi'] === 'menunggu')),
            'terverifikasi'  => count(array_filter($allRaw, fn($u) => $u['status_verifikasi'] === 'terverifikasi')),
            'ditolak'        => count(array_filter($allRaw, fn($u) => $u['status_verifikasi'] === 'ditolak')),
        ];

        return view('petugas.umkm.verifikasi', [
            'umkmList'      => $allData,
            'stats'         => $stats,
            'filterStatus'  => $filterStatus,
            'keyword'       => $keyword,
        ]);
    }
}
