# Checkpoint review

7 Oktober 2026. Dibandingkan dengan `pembagian.md`, `docs/gantt-minggu-6-12.xlsx`, kartu Trello di Need Review dan Done (plus In Progress yang tenggatnya sudah lewat), dan kode di `main`.

Hanya yang belum sesuai. Kartu yang isinya cocok dengan pembagian tidak ditulis di sini.

## Lewat tenggat

### Nazwa: UC-05c Kelola kategori kendala

Laporan Selasa 6 Oktober mensyaratkan kategori kendala bisa dikelola. Di Gantt harian, batang tugas ini 5–7 Oktober, jadi hari ini hari terakhir.

Kartu masih di In Progress. Yang ada di repo hanya migrasi `obstacle_categories` dan `ObstacleCategorySeeder` (Modal, Pemasaran, Legalitas, Produksi, Digitalisasi). Tidak ada controller, route, atau halaman CRUD. Menu petugas "Kategori kendala" masih nonaktif.

File 6 (`assessment_questions`) dan File 7 (`assistance_programs`) sudah ada dan kolomnya sesuai pembagian. Yang belum adalah kelola kategorinya.

## Ditandai siap ditinjau, isi belum sesuai

### Daffa: otorisasi tiga peran

Kartu "Otorisasi 3 peran di kode" ada di Done. Middleware `role` sudah dipakai di route, dan ketiga peran tidak bisa ditambah lewat halaman. Itu bagian yang sesuai.

Policy belum ada. Tidak ada kelas di `app/Policies`, dan tidak ada `authorize()` atau `Gate` di kode. Pembagian dan kartu itu meminta policy untuk aksi yang beda antar peran, selain middleware.

### Hernanda: usaha, produk, dan asesmen

Kartu `[G3] create_businesses_table`, `[G4] create_products_table`, `[G4] create_assessments_table`, UC-04a, UC-04b, dan UC-05a ada di Need Review. Migrasi `businesses` memang punya `id` dan `user_id` yang boleh kosong, sesuai pembagian. Kode yang memakainya tidak mengikuti itu.

- `App\Models\Business` menjadikan `user_id` sebagai primary key, bukan `id`.
- Foreign key `products.business_id` dan `assessments.business_id` mengarah ke `businesses.user_id`, bukan `businesses.id`.
- UC-04b (`BusinessProfileController::officerSave`) menyimpan `user_id` null. Usaha hasil pendataan petugas tidak bisa punya produk atau asesmen, karena relasinya mengikuti `user_id` yang kosong.
- Halaman produk petugas mengirim kolom `id`, sementara route model binding mencari `user_id`. Buka kelola produk dari sisi petugas tidak ketemu usahanya.
- `ProductController::officerUpdate` membandingkan `$business->update_id`. Kolom itu tidak ada, jadi ubah produk oleh petugas selalu ditolak.
- Status default asesmen di migrasi adalah `draf`. Pembagian menulis `draft` dan `completed`. Service skor hanya berjalan kalau statusnya `completed`.

Stub `umkm` (`2026_09_27_160548_create_umkm_table.php`) juga belum di-drop. Kartu businesses meminta tabel itu diganti setelah `businesses` aman.

### Hernanda: dashboard pelaku UMKM

Kartu "Dashboard pelaku UMKM" ada di Need Review. Halaman masih data tetap di `resources/js/pages/UmkmDashboard.jsx` (`DUMMY_PROFILE`, `KENDALA_UTAMA`, `RECOMMENDATIONS`). Route-nya `Route::view`, tidak membaca `businesses`, skor, atau program. Status verifikasi, kendala utama, dan rekomendasi di layar itu bukan data akun yang login.

### Nazwa: detail kelola data UMKM

Daftar, cari, dan filter di `PetugasUmkmController` membaca tabel `businesses`. Itu sesuai UC-04c.

Halaman detail masih menampilkan blok legalitas yang tidak ada di skema: NIB, NPWP, dan sertifikasi halal (`punya_nib`, `nomor_nib`, `punya_npwp`, `status_halal` di `resources/views/petugas/umkm/detail.blade.php`). Pembagian menetapkan tidak ada fitur kelola legalitas. Karena field itu tidak ada, setiap usaha tampil sebagai belum punya NIB, NPWP, dan halal.

Blok "Kebutuhan & Kendala" juga membaca array `kendala` dan `catatan_kebutuhan`, bukan jawaban asesmen. Bagian itu selalu kosong.

### Radith: UC-10 dan UC-11

Kedua kartu ada di Need Review. Tenggat fiturnya minggu 10 (26 Oktober–1 November), jadi belum lewat jadwal. Isinya belum sesuai klaim kartu.

`TindakLanjutManager.jsx` dan `PersetujuanTindakLanjutManager.jsx` memakai `DUMMY_DATA` (Warung Makan Berkah, Kerajinan Rotan Indah, Kopi Desa). Submit hanya mengubah state di browser. Tidak ada tabel `follow_ups`, dan tidak ada penyimpanan ke server. Keputusan setujui atau tolak hilang kalau halaman dimuat ulang.
