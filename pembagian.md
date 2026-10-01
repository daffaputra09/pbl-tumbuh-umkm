# Pembagian Tugas TUMBUH UMKM

Dokumen ini dipakai sebagai panduan kerja tim. Tempel ke Google Docs apa adanya.

Anggota:

1. Daffa Putra Prasetya
2. Hernanda Rizka Utami
3. Nazwa Azahra Audina
4. Radith Ferdian Hibawan

Peran pengguna hanya tiga dan tidak akan bertambah:

| Nilai `users.role` | Nama tampilan |
| --- | --- |
| `business_owner` | Pemilik UMKM |
| `officer` | Petugas Desa |
| `village_head` | Kepala Desa |

Otorisasi diatur di kode (middleware dan policy). Tidak ada tabel `roles` atau `permissions`. Tidak ada halaman untuk menambah peran atau mengubah hak akses.

Tidak ada fitur kelola legalitas. Status legalitas masuk pertanyaan pada kategori Legalitas.

File ERD: `docs/erd-tumbuh-umkm.drawio`. Abaikan tabel `roles`, `permissions`, dan `permission_role` jika masih tampil di ERD.

---

# Bagian A. Tabel users

File: `database/migrations/0001_01_01_000000_create_users_table.php`

Tabel ini **belum sesuai schema**. Yang mengerjakan: **Daffa**.

## A.1 Kondisi saat ini

| Kolom | Kondisi saat ini | Target |
| --- | --- | --- |
| `id` | ada | tetap |
| `name` | ada | tetap |
| `email` | ada, unik | tetap |
| `email_verified_at` | ada | tetap |
| `password` | wajib | nullable, untuk SSO Google nanti |
| `role` | enum `petugas`, `pimpinan`, `umkm` | string: `business_owner`, `officer`, `village_head` |
| `status_akun` | string, default `aktif` | dihapus, diganti `is_active` |
| `phone` | belum ada | ditambah, nullable |
| `is_active` | belum ada | boolean, default true |
| `remember_token` | ada | tetap |
| `timestamps` | ada | tetap |

Kode yang masih memakai kolom lama (ikut diubah bersama migration `users`):

| File | Yang masih lama |
| --- | --- |
| `app/Models/User.php` | fillable `role` dan `status_akun` |
| `app/Http/Controllers/AuthController.php` | menulis `role = umkm` dan `status_akun = aktif`, redirect `petugas` / `pimpinan` / `umkm` |

Tabel stub `umkm` (`id_umkm`, `id_user`, `nama_usaha`) juga belum sesuai schema. Itu diganti Hernanda menjadi `businesses`. Jangan dilanjutkan.

## A.2 Target kolom `users`

Jangan menambah kolom baru di file `0001_01_01_000000` jika database lokal sudah pernah di-migrate. Pakai file baru.

| Kolom | Tipe Laravel | Keterangan |
| --- | --- | --- |
| `role` | `string()` | Hanya `business_owner`, `officer`, `village_head`. Dicek di kode, bukan di tabel terpisah |
| `password` | `string()->nullable()->change()` | Kosong jika login lewat Google |
| `phone` | `string()->nullable()` | |
| `is_active` | `boolean()->default(true)` | Pengganti `status_akun` |

Hapus kolom `status_akun`.

Nilai role lama yang diganti:

| Nilai lama | Nilai baru |
| --- | --- |
| `umkm` | `business_owner` |
| `petugas` | `officer` |
| `pimpinan` | `village_head` |

---

# Bagian B. Pembagian migration

Total 16 file. Tiap anggota 4 file.

| Anggota | Jumlah file | Modul |
| --- | --- | --- |
| Daffa | 4 | Akun, SSO, skor, rekomendasi |
| Hernanda | 4 | Usaha, produk, asesmen |
| Nazwa | 4 | Kategori, soal, program |
| Radith | 4 | Jawaban, tautan program, tindak lanjut, pembinaan |

Satu orang men-generate semua file `php artisan make:migration` sesuai urutan di Bagian C, lalu file kosong dibagikan. Tujuannya agar timestamp tidak tabrakan. `php artisan migrate` dijalankan dari satu mesin setelah file lengkap.

## B.1 Daffa (4 file)

### File 1. `add_columns_to_users_table`

Ubah `users` sesuai tabel di Bagian A.2.

### File 2. `create_social_accounts_table`

| Kolom | Tipe Laravel | Keterangan |
| --- | --- | --- |
| `id` | `id()` | |
| `user_id` | `foreignId()->constrained()->cascadeOnDelete()` | |
| `provider` | `string()` | Contoh: `google` |
| `provider_user_id` | `string()` | ID dari Google |
| `email` | `string()->nullable()` | |
| `avatar_url` | `string()->nullable()` | |
| | `unique(['provider', 'provider_user_id'])` | |
| | `unique(['user_id', 'provider'])` | |
| | `timestamps()` | |

Login Google belum wajib hidup sekarang. Tabel ini disiapkan agar tidak perlu ubah `users` lagi nanti.

### File 3. `create_assessment_category_scores_table`

| Kolom | Tipe Laravel | Keterangan |
| --- | --- | --- |
| `id` | `id()` | |
| `assessment_id` | `foreignId()->constrained()->cascadeOnDelete()` | |
| `obstacle_category_id` | `foreignId()->constrained()->cascadeOnDelete()` | |
| `score` | `decimal('score', 5, 2)` | 0 sampai 100 |
| `level` | `string()` | `low`, `moderate`, `high` |
| | `unique(['assessment_id', 'obstacle_category_id'])` | |
| | `timestamps()` | |

### File 4. `create_program_recommendations_table`

| Kolom | Tipe Laravel | Keterangan |
| --- | --- | --- |
| `id` | `id()` | |
| `business_id` | `foreignId()->constrained()->cascadeOnDelete()` | |
| `assistance_program_id` | `foreignId()->constrained()->cascadeOnDelete()` | |
| `assessment_id` | `foreignId()->constrained()->cascadeOnDelete()` | |
| `obstacle_category_id` | `foreignId()->constrained()` | Kategori yang membuat program ini cocok |
| `score` | `decimal('score', 5, 2)` | Untuk urutan tampilan |
| `is_active` | `boolean()->default(true)` | False jika ada asesmen baru |
| | `unique(['assessment_id', 'assistance_program_id'])` | |
| | `timestamps()` | |

## B.2 Hernanda (4 file)

### File 1. `create_business_types_table`

| Kolom | Tipe Laravel |
| --- | --- |
| `id` | `id()` |
| `name` | `string()` |
| `slug` | `string()->unique()` |
| `is_active` | `boolean()->default(true)` |
| | `timestamps()` |

### File 2. `create_businesses_table`

Ganti stub `umkm`. Setelah tabel baru aman, drop `umkm`.

| Kolom | Tipe Laravel | Keterangan |
| --- | --- | --- |
| `id` | `id()` | |
| `user_id` | `foreignId()->nullable()->unique()->constrained()->nullOnDelete()` | Kosong jika didata petugas |
| `business_type_id` | `foreignId()->constrained()` | |
| `created_by` | `foreignId()->constrained('users')` | |
| `business_name` | `string()` | |
| `owner_name` | `string()` | |
| `phone` | `string()` | |
| `email` | `string()->nullable()` | |
| `address` | `text()` | |
| `hamlet` | `string()` | Dusun |
| `rt` | `string()->nullable()` | |
| `rw` | `string()->nullable()` | |
| `established_year` | `unsignedSmallInteger()` | |
| `employee_count` | `unsignedSmallInteger()` | |
| `description` | `text()->nullable()` | |
| `operational_status` | `string()->default('active')` | `active`, `inactive` |
| `verification_status` | `string()->default('pending')` | Dipakai halaman Nazwa |
| `verification_note` | `text()->nullable()` | |
| `verified_by` | `foreignId()->nullable()->constrained('users')` | |
| `verified_at` | `timestamp()->nullable()` | |
| | `timestamps()` | |
| | `softDeletes()` | |

### File 3. `create_products_table`

| Kolom | Tipe Laravel |
| --- | --- |
| `id` | `id()` |
| `business_id` | `foreignId()->constrained()->cascadeOnDelete()` |
| `name` | `string()` |
| `category` | `string()` |
| `description` | `text()->nullable()` |
| `price` | `unsignedInteger()->nullable()` |
| `unit` | `string()->nullable()` |
| `photo_path` | `string()->nullable()` |
| `is_active` | `boolean()->default(true)` |
| | `timestamps()` |

### File 4. `create_assessments_table`

| Kolom | Tipe Laravel | Keterangan |
| --- | --- | --- |
| `id` | `id()` | |
| `business_id` | `foreignId()->constrained()->cascadeOnDelete()` | |
| `filled_by` | `foreignId()->constrained('users')` | |
| `status` | `string()->default('draft')` | `draft`, `completed` |
| `is_current` | `boolean()->default(false)` | Hanya satu asesmen aktif per UMKM |
| `primary_obstacle_category_id` | `foreignId()->nullable()->constrained('obstacle_categories')` | Diisi setelah scoring |
| `other_obstacle` | `text()->nullable()` | |
| `completed_at` | `timestamp()->nullable()` | |
| | `timestamps()` | |

## B.3 Nazwa (4 file)

### File 1. `create_obstacle_categories_table`

Satu-satunya tabel kategori. Dipakai pertanyaan, skor UMKM, dan program bantuan.

| Kolom | Tipe Laravel | Keterangan |
| --- | --- | --- |
| `id` | `id()` | |
| `name` | `string()` | Modal, Pemasaran, Legalitas, Produksi, Digitalisasi |
| `slug` | `string()->unique()` | |
| `description` | `text()->nullable()` | |
| `moderate_threshold` | `decimal('moderate_threshold', 5, 2)->default(40)` | |
| `high_threshold` | `decimal('high_threshold', 5, 2)->default(70)` | |
| `sort_order` | `unsignedSmallInteger()->default(0)` | |
| `is_active` | `boolean()->default(true)` | |
| | `timestamps()` | |

### File 2. `create_assessment_questions_table`

| Kolom | Tipe Laravel | Keterangan |
| --- | --- | --- |
| `id` | `id()` | |
| `obstacle_category_id` | `foreignId()->constrained()->cascadeOnDelete()` | |
| `type` | `string()->default('likert')` | `likert` atau `single_choice` |
| `prompt` | `text()` | Teks pertanyaan |
| `help_text` | `text()->nullable()` | |
| `weight` | `decimal('weight', 4, 2)->default(1)` | |
| `is_reverse_scored` | `boolean()->default(false)` | |
| `sort_order` | `unsignedSmallInteger()->default(0)` | |
| `is_active` | `boolean()->default(true)` | |
| | `timestamps()` | |

### File 3. `create_question_options_table`

| Kolom | Tipe Laravel | Keterangan |
| --- | --- | --- |
| `id` | `id()` | |
| `assessment_question_id` | `foreignId()->constrained()->cascadeOnDelete()` | |
| `label` | `string()` | Contoh: Sangat setuju |
| `value` | `unsignedTinyInteger()->nullable()` | 1 sampai 5 untuk Likert |
| `score` | `unsignedTinyInteger()` | 0 sampai 100, makin tinggi makin berat kendala |
| `sort_order` | `unsignedSmallInteger()->default(0)` | |
| | `timestamps()` | |

Likert cukup 5 opsi per pertanyaan. Kalau nanti bukan Likert, ganti `type` dan opsi. Rumus skor tetap sama.

### File 4. `create_assistance_programs_table`

| Kolom | Tipe Laravel | Keterangan |
| --- | --- | --- |
| `id` | `id()` | |
| `name` | `string()` | |
| `description` | `text()` | |
| `provider` | `string()` | Penyelenggara |
| `requirements` | `text()->nullable()` | |
| `url` | `string()->nullable()` | |
| `quota` | `unsignedInteger()->nullable()` | |
| `starts_on` | `date()->nullable()` | |
| `ends_on` | `date()->nullable()` | |
| `status` | `string()->default('draft')` | `draft`, `active`, `completed` |
| `created_by` | `foreignId()->constrained('users')` | |
| | `timestamps()` | |

## B.4 Radith (4 file)

### File 1. `create_assessment_answers_table`

| Kolom | Tipe Laravel | Keterangan |
| --- | --- | --- |
| `id` | `id()` | |
| `assessment_id` | `foreignId()->constrained()->cascadeOnDelete()` | |
| `assessment_question_id` | `foreignId()->constrained()` | |
| `question_option_id` | `foreignId()->constrained()` | |
| `score` | `unsignedTinyInteger()` | Salinan skor opsi |
| `weight` | `decimal('weight', 4, 2)` | Salinan bobot pertanyaan |
| | `unique(['assessment_id', 'assessment_question_id'])` | Satu jawaban per pertanyaan |
| | `timestamps()` | |

Form pengisian tetap dikerjakan Hernanda. File migration ini milik Radith.

### File 2. `create_assistance_program_obstacle_category_table`

| Kolom | Tipe Laravel | Keterangan |
| --- | --- | --- |
| `id` | `id()` | |
| `assistance_program_id` | `foreignId()->constrained()->cascadeOnDelete()` | |
| `obstacle_category_id` | `foreignId()->constrained()->cascadeOnDelete()` | |
| `minimum_level` | `string()->default('moderate')` | `moderate` atau `high` |
| | `unique(['assistance_program_id', 'obstacle_category_id'])` | |
| | `timestamps()` | |

Halaman taut kategori ke program tetap dikerjakan Nazwa. File migration ini milik Radith.

### File 3. `create_follow_ups_table`

| Kolom | Tipe Laravel | Keterangan |
| --- | --- | --- |
| `id` | `id()` | |
| `business_id` | `foreignId()->constrained()->cascadeOnDelete()` | |
| `program_recommendation_id` | `foreignId()->nullable()->constrained()->nullOnDelete()` | |
| `assistance_program_id` | `foreignId()->nullable()->constrained()->nullOnDelete()` | |
| `submitted_by` | `foreignId()->constrained('users')` | Petugas |
| `planned_activity` | `text()` | |
| `planned_on` | `date()->nullable()` | |
| `submission_note` | `text()->nullable()` | |
| `status` | `string()->default('pending')` | `pending`, `approved`, `rejected` |
| `decided_by` | `foreignId()->nullable()->constrained('users')` | Kepala desa |
| `decision_note` | `text()->nullable()` | |
| `decided_at` | `timestamp()->nullable()` | |
| | `timestamps()` | |

### File 4. `create_coaching_sessions_table`

| Kolom | Tipe Laravel | Keterangan |
| --- | --- | --- |
| `id` | `id()` | |
| `follow_up_id` | `foreignId()->unique()->constrained()->cascadeOnDelete()` | Hanya dari yang disetujui |
| `business_id` | `foreignId()->constrained()->cascadeOnDelete()` | |
| `assistance_program_id` | `foreignId()->nullable()->constrained()->nullOnDelete()` | |
| `recorded_by` | `foreignId()->constrained('users')` | |
| `title` | `string()` | |
| `held_on` | `date()` | |
| `description` | `text()` | |
| `outcome` | `text()->nullable()` | |
| `status` | `string()->default('scheduled')` | `scheduled`, `completed`, `cancelled` |
| | `timestamps()` | |

---

# Bagian C. Urutan migration

Jangan menukar nomor file. Anggota yang file-nya di gelombang bawah menunggu file di kolom "Menunggu".

## Gelombang 1. Paralel, tanpa menunggu anggota lain

| No | File | Pemilik | Menunggu |
| --- | --- | --- | --- |
| 1 | `add_columns_to_users_table` | Daffa | Tidak |
| 2 | `create_obstacle_categories_table` | Nazwa | Tidak |
| 3 | `create_business_types_table` | Hernanda | Tidak |

## Gelombang 2. Menunggu kolom `users` selesai

| No | File | Pemilik | Menunggu |
| --- | --- | --- | --- |
| 4 | `create_social_accounts_table` | Daffa | File 1 |

Setelah gelombang 2, login dengan `role` baru sudah bisa dikerjakan.

## Gelombang 3. Menunggu `users` dan master awal

| No | File | Pemilik | Menunggu |
| --- | --- | --- | --- |
| 5 | `create_businesses_table` | Hernanda | File 1 dan File 3 |
| 6 | `create_assessment_questions_table` | Nazwa | File 2 |
| 7 | `create_assistance_programs_table` | Nazwa | File 1 |

## Gelombang 4. Menunggu usaha, soal, dan program

| No | File | Pemilik | Menunggu |
| --- | --- | --- | --- |
| 8 | `create_products_table` | Hernanda | File 5 |
| 9 | `create_assessments_table` | Hernanda | File 5 dan File 2 |
| 10 | `create_question_options_table` | Nazwa | File 6 |
| 11 | `create_assistance_program_obstacle_category_table` | Radith | File 7 dan File 2 |

## Gelombang 5. Menunggu asesmen dan opsi

| No | File | Pemilik | Menunggu |
| --- | --- | --- | --- |
| 12 | `create_assessment_answers_table` | Radith | File 9 dan File 10 |

## Gelombang 6. Skor dan rekomendasi

| No | File | Pemilik | Menunggu |
| --- | --- | --- | --- |
| 13 | `create_assessment_category_scores_table` | Daffa | File 9 dan File 2 |
| 14 | `create_program_recommendations_table` | Daffa | File 5, File 7, dan File 9 |

## Gelombang 7. Tindak lanjut dan pembinaan

| No | File | Pemilik | Menunggu |
| --- | --- | --- | --- |
| 15 | `create_follow_ups_table` | Radith | File 5 dan File 14 |
| 16 | `create_coaching_sessions_table` | Radith | File 15 |

---

# Bagian D. Pembagian fitur

Sumber: proposal PBL 3 (UC-01 sampai UC-15) dan use case tiga aktor.

Tiap anggota mengerjakan 8 fitur. Kolom "Bisa mulai" dan "Menunggu" dipakai untuk menentukan kerja paralel.

## D.1 Daffa (8 fitur)

| Kode | Fitur | Halaman / hasil | Bisa mulai | Menunggu |
| --- | --- | --- | --- | --- |
| UC-01 | Login, logout, dan proteksi session | `login`, redirect sesuai `role` | Setelah File 1 | Kolom `users` sesuai schema |
| UC-02 | Registrasi pemilik UMKM | `register`, buat user + kerangka usaha | Setelah File 5 | `businesses` |
| UC-03 | Kelola profil akun | Ubah nama, email, HP, password | Setelah UC-01 | Login selesai |
| Authz | Otorisasi 3 peran di kode | Middleware dan policy. Role tidak bisa ditambah lewat UI | Setelah UC-01 | File 1 |
| SSO | Siapkan Socialite dan `social_accounts` | Tombol Google boleh belum aktif | Setelah File 4 | Tidak wajib live |
| UC-07 | Pengelompokan otomatis | Hitung skor per kategori, isi `level` dan `primary_obstacle_category_id` | Setelah kuesioner bisa disimpan | File 12 dan soal Nazwa |
| UC-09a | Generate rekomendasi | Cocokkan skor UMKM dengan kategori program | Setelah UC-07 dan program Nazwa | File 11 dan File 14 |
| UC-13 | Dashboard operasional petugas | Ringkasan UMKM, sebaran kendala, antrean | UI boleh paralel pakai data dummy | Data nyata setelah UC-07 |

Rumus skor kategori:

`skor = jumlah(score_opsi * bobot) / jumlah(100 * bobot) * 100`

Tingkat `low` / `moderate` / `high` dibanding `moderate_threshold` dan `high_threshold`.

Setelah kuesioner berstatus `completed`, service pengelompokan dipanggil dari alur simpan jawaban.

## D.2 Hernanda (8 fitur)

| Kode | Fitur | Halaman / hasil | Bisa mulai | Menunggu |
| --- | --- | --- | --- | --- |
| Master | Jenis usaha | Seeder / kelola `business_types` | Setelah File 3 | Tidak |
| UC-04a | Profil usaha milik sendiri | Form pelaku UMKM | Setelah File 5 | Auth untuk simpan sebagai user |
| UC-04b | Pendataan usaha oleh petugas | Form yang sama, untuk UMKM yang belum punya akun | Setelah File 5 | Authz 3 peran |
| UC-05a | Kelola produk | CRUD produk | Setelah File 8 | `products` |
| UC-05b | Isi kebutuhan dan kendala | Form Likert / pilihan | Setelah File 10 dan File 12 | Bank soal Nazwa dan tabel jawaban |
| UC-06b | Lihat status verifikasi | Badge menunggu / terverifikasi / ditolak | Setelah UC-04a | Kolom verifikasi di `businesses` |
| Dashboard UMKM | Ringkasan usaha sendiri | Status, kendala utama, program yang cocok | UI boleh paralel | Data nyata setelah UC-05b |
| UC-09b | Lihat rekomendasi milik sendiri | Bahasa sederhana, bukan angka teknis | Setelah rekomendasi digenerate | UC-09a |

Hernanda menyimpan jawaban. Perhitungan skor tidak ditulis di form Hernanda. Setelah status `completed`, panggil service pengelompokan.

## D.3 Nazwa (8 fitur)

| Kode | Fitur | Halaman / hasil | Bisa mulai | Menunggu |
| --- | --- | --- | --- | --- |
| UC-04c | Kelola seluruh data UMKM | Tabel master petugas, cari, filter | Setelah File 5 | `businesses` |
| UC-06 | Verifikasi data mandiri | `pending` / `verified` / `needs_revision` / `rejected` | Setelah UC-04c | Data masuk dari form Hernanda |
| UC-05c | Kelola kategori kendala | CRUD `obstacle_categories` | Setelah File 2 | Tidak |
| Bank soal | Kelola pertanyaan dan opsi | CRUD soal per kategori, termasuk Likert | Setelah File 6 dan File 10 | Kategori |
| UC-08 | Kelola program bantuan | CRUD program + tautkan kategori | Setelah File 7 dan File 11 | Kategori dan tabel tautan |
| UC-09c | Lihat rekomendasi seluruh UMKM | Tampilan petugas, data dari hasil pencocokan | Setelah UC-09a | Generate rekomendasi |
| UC-14b | Laporan ringkasan | Filter periode / kategori | Setelah ada data skor | UC-07 |
| UC-15 | Ekspor laporan PDF atau Excel | Tombol unduh | Setelah UC-14b | Laporan bisa ditampilkan |

Nazwa memakai kolom verifikasi di tabel `businesses`. Jangan buat tabel verifikasi baru.

## D.4 Radith (8 fitur)

| Kode | Fitur | Halaman / hasil | Bisa mulai | Menunggu |
| --- | --- | --- | --- | --- |
| Navigasi | Shell menu 3 peran | Sidebar / header sesuai `role` | Setelah UC-01 | Login |
| UC-10 | Ajukan tindak lanjut | Petugas menandai rekomendasi | Setelah File 15 | Rekomendasi |
| UC-11 | Setujui atau tolak tindak lanjut | Halaman kepala desa | Setelah UC-10 | `follow_ups` |
| UC-12 | Catat pembinaan | Form kegiatan dan hasil | Setelah File 16 | Tindak lanjut berstatus `approved` |
| Riwayat petugas | Lihat seluruh pembinaan | Daftar operasional petugas | Setelah UC-12 | `coaching_sessions` |
| Riwayat UMKM | Lihat pembinaan milik sendiri | Halaman pelaku UMKM | Setelah UC-12 | `coaching_sessions` |
| UC-14a | Dashboard pimpinan | Antrean persetujuan dan progres pembinaan | UI boleh paralel | Data nyata setelah UC-11 |
| Monitoring | Pantau tindak lanjut per UMKM | Detail status pengajuan dan hasil | Setelah UC-11 | `follow_ups` |

---

# Bagian E. Urutan pengerjaan fitur

Pakai ini saat sprint mingguan.

## Minggu 1. Fondasi

Bisa paralel:

- Daffa: kolom `users`, login, logout, middleware 3 peran
- Nazwa: CRUD kategori (setelah File 2)
- Hernanda: desain form profil dan seeder jenis usaha
- Radith: shell navigasi statis

Belum dikerjakan:

- Registrasi lengkap (menunggu `businesses`)
- Kuesioner (menunggu soal)
- Scoring (menunggu jawaban)

## Minggu 2. Data masuk

Bisa paralel setelah gelombang 3:

- Daffa: registrasi dan proteksi route
- Hernanda: simpan profil dan produk
- Nazwa: bank soal Likert, program bantuan, tabel master UMKM
- Radith: tautan program-kategori, wireframe tindak lanjut

Belum dikerjakan:

- Generate rekomendasi
- Ajukan tindak lanjut

## Minggu 3. Analisis

Urutan wajib:

1. Hernanda: UMKM bisa menyelesaikan kuesioner
2. Daffa: hitung skor dan pengelompokan
3. Daffa: generate rekomendasi
4. Hernanda: tampilkan rekomendasi di dashboard UMKM
5. Nazwa: laporan memakai skor

Bisa paralel di minggu ini:

- Nazwa: verifikasi data
- Daffa: sambungkan dashboard petugas ke data nyata
- Radith: siapkan UI persetujuan

## Minggu 4. Tindak lanjut

Urutan wajib:

1. Radith: petugas mengajukan tindak lanjut
2. Radith: kepala desa setujui atau tolak
3. Radith: petugas mencatat pembinaan
4. Nazwa: ekspor laporan termasuk riwayat pembinaan
5. Radith: tampilkan riwayat di sisi petugas dan pemilik UMKM

---

# Bagian F. Batas tanggung jawab

| Topik | Yang mengerjakan | Yang memakai hasilnya |
| --- | --- | --- |
| Password, session, kolom `role`, middleware, Google SSO | Daffa | Anggota lain memakai middleware yang sudah ada |
| Rumus skor dan pencocokan program | Daffa | Hernanda menyimpan jawaban. Nazwa mengatur soal dan program |
| Form profil, produk, kuesioner | Hernanda | Nazwa memverifikasi data yang masuk |
| Verifikasi, kategori, soal, program, ekspor | Nazwa | Dashboard dan rekomendasi membaca data ini |
| Pengajuan, persetujuan, pembinaan, navigasi | Radith | Kepala desa tidak mengubah data operasional UMKM |

Kalau satu orang terhambat, kerjakan baris yang kolom "Bisa mulai"-nya sudah terpenuhi. Jangan menukar timestamp migration sendiri.

---

# Bagian G. Timeline semester (minggu 6–12)

Pakai kalender 16 minggu mata kuliah. Laporan progres ke dosen **setiap Rabu**.

Hari ini Kamis 1 Oktober 2026 = **minggu ke-6**. Target produk fungsional + hasil uji: **minggu ke-12**.

Bagian E di atas (Minggu 1–4) adalah urutan kerja teknis. Bagian ini adalah minggu semester.

| Minggu semester | Tanggal (Senin–Minggu) | Laporan ke dosen | Titik kalender |
| --- | --- | --- | --- |
| 5–7 | 21 Sep – 11 Okt 2026 | Rabu 7 Okt | Pembangunan fitur inti, iterasi 1 |
| 8 | 12–18 Okt 2026 | Rabu 14 Okt | **Milestone 1.** Demo progres tengah semester |
| 9–11 | 19 Okt – 8 Nov 2026 | Rabu 21 Okt, 28 Okt, 4 Nov | Integrasi, pengujian mulai |
| 12 | 9–15 Nov 2026 | Rabu 11 Nov | **Milestone 2.** Demo produk fungsional + hasil uji |

Minggu 13–16 (perbaikan, dokumentasi, expo) di luar target ini. SSO Google tidak wajib hidup di minggu 12.

## G.1 Status 1 Oktober 2026

Sudah ada di `main` (UI, belum schema baru): landing, login/register, profil UMKM, kebutuhan/kendala, dashboard petugas dummy, dashboard UMKM dummy, verifikasi petugas.

Sedang jalan:

| Tugas | Pemilik | Status |
| --- | --- | --- |
| File 1 `users` | Daffa | In progress |
| File 3 `business_types` + master jenis usaha | Hernanda | Need review, PR sudah masuk |

Card To Do yang UI-nya sudah jadi **tidak ditandai selesai**. Yang tersisa: simpan data ke tabel baru, otorisasi, scoring, rekomendasi, tindak lanjut, uji.

## G.2 Yang harus selesai tiap minggu

Kolom "Siap dilaporkan Rabu" = yang boleh didemo ke dosen hari itu.

### Minggu 6. 28 Sep – 4 Okt 2026

Rabu 30 Sep sudah lewat. Sisa kerja: Kamis 1 Okt sampai Minggu 4 Okt.

Harus selesai minggu ini:

| Pemilik | Tugas |
| --- | --- |
| Daffa | File 1 `users` (role baru, `phone`, `is_active`, password nullable). File 4 `social_accounts` |
| Hernanda | File 3 + seeder jenis usaha sudah di-merge. Mulai File 5 `businesses` begitu File 1 masuk |
| Nazwa | File 2 `obstacle_categories` + seeder 5 kategori |
| Radith | Siapkan shell navigasi (boleh statis). Jangan nunggu File 11 kalau File 7 belum ada |

Belum wajib: login role baru, form simpan ke `businesses`.

### Minggu 7. 5–11 Okt 2026 — laporan Rabu 7 Oktober

Akhir iterasi 1. Semua migration G1–G4 ada di repo. Satu orang yang men-generate timestamp, `php artisan migrate` dari satu mesin.

Harus selesai sebelum Rabu 7 Okt (laporan pertama setelah hari ini):

| Pemilik | Tugas |
| --- | --- |
| Daffa | UC-01 login/logout pakai UI Radith, role `business_owner` / `officer` / `village_head`. Authz middleware 3 peran |
| Nazwa | UC-05c CRUD kategori. File 6 `assessment_questions`, File 7 `assistance_programs` |
| Hernanda | File 5 `businesses` (ganti stub `umkm`). File 8 `products`, File 9 `assessments` |
| Radith | Shell menu 3 peran memakai middleware Daffa. File 11 pivot program–kategori (setelah File 7) |

Sisa 7–11 Okt (setelah laporan):

| Pemilik | Tugas |
| --- | --- |
| Daffa | UC-03 profil akun. Siapkan File 13 dan File 14 (boleh kosong dulu, timestamp tetap urut) |
| Nazwa | File 10 `question_options`. Seeder 1 set Likert per kategori |
| Hernanda | Sambungkan form profil UI ke `businesses` (UC-04a, draf) |
| Radith | File 12 `assessment_answers` begitu File 9 dan File 10 ada |

**Siap dilaporkan Rabu 7 Okt:** tiga akun (pelaku, petugas, kepala desa) login dan masuk halaman berbeda. Kategori kendala bisa dilihat. Jenis usaha terisi.

### Minggu 8. 12–18 Okt 2026 — laporan Rabu 14 Oktober — Milestone 1

Demo tengah semester: data UMKM masuk, petugas melihat daftar, bukan hanya UI dummy.

Harus selesai:

| Pemilik | Tugas |
| --- | --- |
| Daffa | UC-02 registrasi: user + kerangka `businesses`. Proteksi route. SSO boleh belum hidup |
| Hernanda | UC-04a profil milik sendiri tersimpan. UC-04b pendataan oleh petugas. UC-05a CRUD produk. UC-06b badge verifikasi (baca kolom, belum wajib diisi petugas) |
| Nazwa | UC-04c tabel master UMKM dari `businesses`. Bank soal bisa dikelola. UC-08 CRUD program + taut kategori |
| Radith | UC-14a dashboard pimpinan UI (dummy antrean boleh). File 15 `follow_ups` setelah File 14 ada; kalau File 14 belum, kerjakan UI pengajuan dulu |

**Demo Milestone 1 (Rabu 14 Okt):**

1. Daftar sebagai pelaku UMKM, isi profil dan satu produk.
2. Petugas buka daftar UMKM dan detail.
3. Petugas kelola kategori, soal, dan satu program bantuan.
4. Login kepala desa, lihat dashboard (boleh masih kosong).

Belum wajib: kuesioner tersimpan, skor, rekomendasi, pengajuan, ekspor.

### Minggu 9. 19–25 Okt 2026 — laporan Rabu 21 Oktober

Alur kuesioner → skor → rekomendasi. Mulai tes otomatis.

Harus selesai:

| Pemilik | Tugas |
| --- | --- |
| Hernanda | UC-05b simpan jawaban (Likert). Status `completed` memanggil service Daffa |
| Daffa | File 13 dan File 14. UC-07 rumus skor. UC-09a generate rekomendasi |
| Nazwa | UC-06 verifikasi (`pending` / `verified` / `needs_revision` / `rejected`) |
| Radith | File 16 `coaching_sessions`. UI UC-10 siap, boleh belum terhubung rekomendasi sampai UC-09a masuk |

Tes (siapa saja yang pegang Pest, utamakan Daffa + 1 orang lain): login 3 peran, simpan `businesses`.

**Siap dilaporkan Rabu 21 Okt:** satu UMKM selesai kuesioner, muncul tingkat kendala, muncul daftar program. Petugas bisa verifikasi.

### Minggu 10. 26 Okt – 1 Nov 2026 — laporan Rabu 28 Oktober

Integrasi tampilan. Dashboard dummy diganti data nyata.

Harus selesai:

| Pemilik | Tugas |
| --- | --- |
| Hernanda | UC-09b rekomendasi milik sendiri. Dashboard UMKM baca skor dan program |
| Daffa | UC-13 dashboard petugas baca UMKM, sebaran kategori, antrean verifikasi |
| Nazwa | UC-09c rekomendasi seluruh UMKM. UC-14b laporan ringkasan (filter periode / kategori) |
| Radith | UC-10 ajukan tindak lanjut dari rekomendasi. UC-11 setujui atau tolak |

Tes: kuesioner → skor → rekomendasi (satu happy path).

**Siap dilaporkan Rabu 28 Okt:** petugas ajukan program, kepala desa setujui atau tolak, pelaku lihat program di dashboardnya.

### Minggu 11. 2–8 Nov 2026 — laporan Rabu 4 November

Pembinaan dan laporan. Tutup lubang integrasi.

Harus selesai:

| Pemilik | Tugas |
| --- | --- |
| Radith | UC-12 catat pembinaan. Monitoring per UMKM. Riwayat petugas dan riwayat milik UMKM |
| Nazwa | UC-15 ekspor PDF atau Excel (cukup salah satu dulu, yang kedua menyusul sebelum Rabu 11 Nov) |
| Hernanda | Rapikan form pelaku (profil, produk, kuesioner) setelah data nyata masuk. Pelaku tidak melihat UMKM lain |
| Daffa | Perbaiki bug auth/scoring yang muncul di minggu 9–10. SSO tetap boleh nonaktif |

Tes: follow-up `approved` → sesi pembinaan tersimpan.

**Siap dilaporkan Rabu 4 Nov:** alur pengajuan sampai catatan pembinaan. Laporan bisa dibuka. Ekspor boleh masih kasar.

### Minggu 12. 9–15 Nov 2026 — laporan Rabu 11 November — Milestone 2

Produk fungsional + hasil uji. Tidak menambah fitur baru.

Harus selesai:

| Pemilik | Tugas |
| --- | --- |
| Semua | Bug yang menghalangi demo 3 aktor |
| Radith | Riwayat UMKM selesai. Dashboard pimpinan pakai antrean nyata |
| Nazwa | Ekspor PDF dan Excel. Laporan memuat sebaran kendala + riwayat pembinaan |
| Daffa | Kumpulan tes inti di repo (Pest): login 3 peran, profil tersimpan, kuesioner + skor, rekomendasi, pengajuan + keputusan |
| Hernanda | Cek form pelaku (profil, produk, kuesioner, rekomendasi, riwayat) tanpa data UMKM lain bocor |

**Demo Milestone 2 (Rabu 11 Nov):**

1. Pelaku: daftar, profil, produk, kuesioner, lihat rekomendasi dan riwayat.
2. Petugas: daftar UMKM, verifikasi, program, ajukan tindak lanjut, catat pembinaan, lihat laporan/ekspor.
3. Kepala desa: setujui/tolak, pantau progres.
4. Tunjukkan hasil uji (tes otomatis yang lulus + 1 skenario uji manual).

## G.3 Ringkas per orang sampai minggu 12

| Pemilik | M6–M7 | M8 (Milestone 1) | M9–M10 | M11–M12 (Milestone 2) |
| --- | --- | --- | --- | --- |
| Daffa | File 1, File 4, login, middleware | Registrasi, proteksi route | Skor, generate rekomendasi, dashboard petugas nyata | Tes inti, perbaiki scoring/auth |
| Hernanda | File 5, 8, 9, seeder jenis | Profil, pendataan petugas, produk | Kuesioner simpan, rekomendasi sendiri, dashboard UMKM nyata | Form pelaku rapat, tidak bocor data UMKM lain |
| Nazwa | File 2, 6, 7, 10, CRUD kategori | Master UMKM, bank soal, program | Verifikasi, rekomendasi semua UMKM, laporan | Ekspor, laporan lengkap |
| Radith | Shell, File 11, File 12 | Dashboard pimpinan UI, File 15 | Ajukan, setujui/tolak, File 16 | Pembinaan, monitoring, riwayat, dashboard pimpinan nyata |

## G.4 Kalau molor

| Yang molor | Geser ke | Jangan digeser |
| --- | --- | --- |
| SSO Google | Setelah minggu 12 | Login email/password |
| Ekspor Excel (kalau PDF sudah ada) | Minggu 12 Kamis–Minggu | Laporan tampil di layar |
| CRUD jenis usaha lengkap | Minggu 9 | Seeder jenis usaha |
| Tes otomatis selain happy path | Minggu 12 sisa hari | Satu alur 3 aktor bisa didemo |

Jangan menukar urutan migration. Kalau File 5 belum siap, Nazwa dan Radith kerjakan bank soal / shell / program, jangan mengarang tabel `businesses`.
