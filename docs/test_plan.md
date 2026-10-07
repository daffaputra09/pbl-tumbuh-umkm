# Test Plan: TUMBUH UMKM

Pengujian fungsional aplikasi web TUMBUH UMKM untuk tiga peran: pemilik UMKM, petugas desa, dan kepala desa.

## Test Plan Identifier

TUMBUH-UMKM-TP-1.0

| | |
| --- | --- |
| Versi | 1.0 |
| Disusun oleh | Tim TUMBUH UMKM |
| Anggota | Daffa Putra Prasetya, Hernanda Rizka Utami, Nazwa Azahra Audina, Radith Ferdian Hibawan |

## References

Ketentuan yang diuji tertulis pada test plan ini. Langkah, data uji, dan hasil yang diharapkan ada pada daftar kasus uji manual tim.

Dua batas yang langsung mempengaruhi hasil uji:

- Legalitas tidak diuji sebagai formulir NIB, NPWP, atau izin terpisah. Legalitas diuji sebagai kategori soal pada kuesioner.
- Sistem tidak memakai fuzzy clustering. Pengelompokan dihitung sebagai skor kategori 0 sampai 100 dengan level `low`, `moderate`, dan `high`. Tampilan pemilik memakai kata, misalnya kebutuhan tinggi pada bidang pemasaran.

## Introduction

Test plan ini mencakup pengujian fungsional TUMBUH UMKM sampai demo minggu 12.

Tujuannya:

1. Menetapkan alat dan cara kerja pengujian: kasus manual pada daftar kasus uji, ditambah tes otomatis Pest untuk jalur inti.
2. Menyampaikan modul yang diuji, modul yang tidak masuk syarat lulus, jadwal, dan lingkungan uji kepada keempat anggota.
3. Menetapkan cara menjalankan kasus, kapan pengujian dijeda, dan syarat lulus sebelum demo 11 November 2026.

Pengujian manual belum dijalankan. Kolom Actual Result dan Status pada daftar kasus uji masih kosong. Pengujian satu modul dimulai setelah modul itu menyimpan data, bukan saat halaman masih menampilkan data contoh.

## Test Items

Yang diuji adalah aplikasi web TUMBUH UMKM, yaitu halaman untuk tiga peran dan pemrosesan data di server.

Peran dan nilai `users.role`:

| Peran di layar | Nilai `users.role` |
| --- | --- |
| Pemilik UMKM | `business_owner` |
| Petugas Desa | `officer` |
| Kepala Desa | `village_head` |

Modul yang masuk lingkungan uji:

- masuk, keluar, sesi, registrasi pemilik, dan profil akun
- profil usaha, produk, daftar UMKM, dan verifikasi
- kategori kendala, bank soal, dan kuesioner
- skor per kategori, rekomendasi program, dashboard
- pengajuan tindak lanjut, persetujuan kepala desa, dan catatan pembinaan
- laporan ringkasan, unduh PDF, dan unduh Excel

Pengujian dijalankan pada Chrome di komputer desktop, terhadap aplikasi yang berjalan di lingkungan uji tim. Tidak ada aplikasi mobile terpisah yang diuji.

## Features To Be Tested

Setiap baris menunjuk ke kasus pada daftar kasus uji. Hasil yang diharapkan tidak disalin ulang ke dokumen ini.

| Kelompok | Yang dicek | Kasus | Jumlah |
| --- | --- | --- | --- |
| Halaman awal | Jalan ke masuk dan daftar | TC_LAND_001 sampai TC_LAND_002 | 2 |
| Registrasi pemilik | Akun `business_owner`, email kembar, field wajib, peran tidak bisa dipilih sendiri | TC_REG_001 sampai TC_REG_005 | 5 |
| Login dan sesi | Tiga peran masuk ke halaman yang sesuai, kata sandi salah, akun nonaktif, keluar, halaman tanpa sesi | TC_AUTH_001 sampai TC_AUTH_009 | 9 |
| Profil akun | Ubah nama, email, HP, dan kata sandi | TC_ACC_001 sampai TC_ACC_004 | 4 |
| Batas peran | Pemilik, petugas, dan kepala desa ditolak dari aksi yang bukan haknya. Pemilik tidak melihat usaha orang lain | TC_AUTHZ_001 sampai TC_AUTHZ_006 | 6 |
| Menu | Isi menu ketiga peran | TC_NAV_001 sampai TC_NAV_003 | 3 |
| Jenis usaha | Jenis aktif dan nonaktif, tambah jenis | TC_TYPE_001 sampai TC_TYPE_003 | 3 |
| Profil usaha | Profil milik sendiri, pendataan petugas tanpa akun, usaha tidak aktif, usaha terhapus | TC_BIZ_001 sampai TC_BIZ_007 | 7 |
| Daftar UMKM | Tabel, cari, saring, detail, hasil kosong | TC_LIST_001 sampai TC_LIST_006 | 6 |
| Produk | Lebih dari satu produk, ubah, nonaktif, harga kosong, foto, produk orang lain | TC_PROD_001 sampai TC_PROD_006 | 6 |
| Verifikasi | Menunggu, terverifikasi, perlu revisi, ditolak. Kepala desa dan pemilik tidak mengubah status | TC_VER_001 sampai TC_VER_006 | 6 |
| Kategori kendala | Lima kategori awal, ambang, kategori nonaktif, tidak ada menu legalitas terpisah | TC_CAT_001 sampai TC_CAT_006 | 6 |
| Bank soal | Likert lima opsi, pilihan tunggal, skor terbalik, bobot, soal nonaktif, skor di luar 0 sampai 100 | TC_Q_001 sampai TC_Q_006 | 6 |
| Kuesioner | Draf, satu jawaban per soal, selesai, soal kosong, satu asesmen berjalan, usaha orang lain | TC_ASM_001 sampai TC_ASM_006 | 6 |
| Skor | Rumus bobot, batas ambang, kategori utama, skor terbalik | TC_SCORE_001 sampai TC_SCORE_006 | 6 |
| Program bantuan | Draf, aktif, tautan kategori, tanggal, program yang tidak ikut rekomendasi | TC_PROG_001 sampai TC_PROG_006 | 6 |
| Rekomendasi | Cocok menurut level, urutan, bahasa pemilik, daftar petugas, keadaan kosong, kepala desa hanya melihat | TC_REC_001 sampai TC_REC_008 | 8 |
| Dashboard | Ringkasan pemilik, petugas, dan kepala desa dari data yang tersimpan | TC_DASH_001 sampai TC_DASH_005 | 5 |
| Tindak lanjut | Ajukan, setujui, tolak, pantau per UMKM | TC_FU_001 sampai TC_FU_006 | 6 |
| Pembinaan dan riwayat | Catat dari yang disetujui, satu sesi per pengajuan, selesai, batal, riwayat sesuai peran | TC_COA_001 sampai TC_COA_007 | 7 |
| Laporan dan ekspor | Sebaran kendala, riwayat pembinaan, saringan, PDF, Excel | TC_RPT_001 sampai TC_RPT_008 | 8 |

Jumlah kasus pada tabel ini 121. Tiga kasus login Google tidak masuk tabel karena belum menjadi syarat lulus.

Rumus yang dipakai TC_SCORE_001 sampai TC_SCORE_006:

`skor = jumlah(skor opsi × bobot) / jumlah(100 × bobot) × 100`

Level dibanding ambang kategori. Nilai bawaan ambang sedang 40 dan ambang tinggi 70.

- Skor di bawah ambang sedang: `low`
- Skor sama dengan ambang sedang, sampai di bawah ambang tinggi: `moderate`
- Skor sama dengan ambang tinggi atau lebih: `high`

Soal dengan skor terbalik memakai skor efektif `100 - skor opsi` sebelum rumus itu. Setelah kuesioner berstatus `completed`, penghitungan skor dan rekomendasi berjalan dari alur simpan, bukan dari tombol hitung terpisah.

## Features Not To Be Tested

Bagian ini bukan daftar fitur yang dibatalkan. Ini batas agar hasil uji minggu 12 tidak menunggu pekerjaan di luar target.

- Login Google (TC_SSO_001 sampai TC_SSO_003). Tabel `social_accounts` disiapkan, tetapi masuk dengan Google tidak wajib hidup pada minggu 12. Kasus ini dijalankan hanya setelah tombol Google aktif. Hasilnya tidak masuk syarat lulus demo 11 November 2026.
- Halaman tambah peran atau ubah hak akses. Otorisasi ada di kode. Tidak ada halaman untuk menambah peran.
- Formulir kelola NIB, NPWP, sertifikasi, atau izin sebagai data tersendiri. Yang diuji adalah soal pada kategori Legalitas (TC_CAT_006).
- Fuzzy clustering. Sistem tidak memakai metode itu. Yang diuji pada pengelompokan adalah skor kategori dan level `low`, `moderate`, `high` (TC_SCORE_001 sampai TC_SCORE_006). Layar pemilik diuji lewat TC_REC_005: kategori dan tingkat dalam kata.
- Aplikasi mobile native. Yang diuji adalah aplikasi web di Chrome desktop.
- Firefox, Safari, dan Edge. Kegagalan yang hanya muncul di peramban selain Chrome tidak menahan demo, selama jalur Chrome lulus.
- Desa lain, pembayaran, atau peran keempat. Sistem ini untuk satu desa dan tiga peran yang sudah ditetapkan.

## Approach

Penguji menjalankan kasus sesuai urutan baris pada daftar kasus uji.

Untuk setiap kasus:

1. Penuhi pre-condition. Akun, usaha, atau asesmen yang disebut di kasus harus sudah ada.
2. Jalankan Test Steps dari atas ke bawah. Test Data di baris yang sama adalah isian langkah itu. Tulisan hijau pada daftar kasus adalah data yang sah. Tulisan merah adalah data yang diharapkan ditolak.
3. Bandingkan yang terjadi dengan Expected Result.
4. Isi Actual Result dengan yang tampil, termasuk pesan galat.
5. Isi Status dengan PASS atau FAIL. Kolom Status punya pilihan itu. Jangan mengisi PASS sebelum kasus dijalankan.

Jangan menyalakan filter pada daftar kasus. Satu kasus terdiri dari beberapa baris yang digabung. Filter memotong langkah dari kasusnya.

Penguji modul bukan satu-satunya orang yang menjalankan modul itu. Pasangan penguji ada di bagian Responsibilities. Pembuat modul tetap memperbaiki cacat di modulnya.

Tes otomatis Pest tidak menggantikan daftar kasus uji. Pest mencakup jalur inti minggu 12: masuk tiga peran, profil tersimpan, kuesioner dan skor, rekomendasi, pengajuan dan keputusan. Hasil Pest dilampirkan pada laporan uji.

Jika kasus FAIL, penguji menulis di Actual Result:

- peran yang dipakai
- langkah yang gagal
- yang tampil, dibanding hasil yang diharapkan

Cacat yang menghalangi jalur demo juga dicatat pada kartu Trello modul yang bersangkutan, supaya yang memperbaiki tidak hanya membaca daftar kasus.

Urutan menjalankan modul mengikuti urutan kerja tim. Kuesioner yang bisa diselesaikan diuji sebelum skor. Skor diuji sebelum rekomendasi. Rekomendasi diuji sebelum pengajuan. Pengajuan yang disetujui diuji sebelum catatan pembinaan. Laporan yang memuat pembinaan diuji setelah sesi pembinaan bisa disimpan.

## Pass/Fail Criteria

Satu kasus PASS jika yang terjadi sama dengan Expected Result pada daftar kasus, untuk data uji kasus itu.

Satu kasus FAIL jika hasilnya berbeda, jika langkah tidak bisa diselesaikan, atau jika sistem menyimpan data yang seharusnya ditolak.

Syarat lulus pengujian untuk demo Rabu, 11 November 2026:

1. Jalur demo tiga peran di bawah ini berstatus PASS.
2. TC_AUTHZ_006 PASS, sehingga pemilik tidak melihat data UMKM lain.
3. TC_SCORE_001 PASS, sehingga skor mengikuti rumus bobot.
4. Tidak ada cacat terbuka yang menghentikan salah satu peran menyelesaikan jalur demo.
5. Sisa kasus pada tabel Features To Be Tested sudah dijalankan. Jika ada yang FAIL, cacatnya tertulis di Actual Result dan tidak memotong jalur demo.
6. Tes Pest untuk jalur inti lulus, dan hasilnya bisa ditunjukkan bersama satu kasus manual.

TC_SSO_001 sampai TC_SSO_003 tidak masuk hitungan lulus atau gagal demo.

Jalur demo yang wajib PASS:

| Demo | Kasus |
| --- | --- |
| Pemilik: daftar, profil, produk, kuesioner, rekomendasi, riwayat | TC_REG_001, TC_AUTH_001, TC_BIZ_001, TC_PROD_001, TC_ASM_003, TC_REC_005, TC_COA_006 |
| Petugas: daftar, verifikasi, program, ajukan, catat pembinaan, laporan | TC_AUTH_002, TC_LIST_001, TC_VER_002, TC_PROG_002, TC_PROG_003, TC_FU_001, TC_COA_001, TC_RPT_001, TC_RPT_005 |
| Kepala desa: setujui, tolak, pantau | TC_AUTH_003, TC_FU_003, TC_FU_004, TC_FU_006, TC_DASH_004 |

TC_RPT_006 (unduh Excel) tetap dijalankan sebelum 11 November 2026. Jika PDF pada TC_RPT_005 sudah PASS dan Excel belum selesai, demo tetap dapat memakai PDF. Excel yang belum PASS ditulis terbuka pada laporan.

## Suspension Criteria

Pengujian dijeda, bukan ditandai PASS, jika kondisi di bawah ini terjadi.

- Masuk untuk peran yang sedang diuji gagal, atau sesi habis pada setiap halaman. Kasus peran itu ditunda sampai masuk dan keluar untuk peran tersebut PASS (TC_AUTH_001, TC_AUTH_002, atau TC_AUTH_003, sesuai peran).
- Profil usaha, produk, atau jawaban kuesioner tidak tersimpan. Kasus yang membutuhkan data itu ditunda.
- Kuesioner tidak bisa mencapai status `completed`. Kasus skor, rekomendasi, dashboard data nyata, dan laporan sebaran kendala ditunda.
- Rekomendasi tidak terbentuk setelah kuesioner selesai. Kasus pengajuan dari rekomendasi ditunda.
- Keputusan setujui atau tolak hilang setelah halaman dimuat ulang. Kasus pembinaan ditunda.
- Halaman yang diuji masih menampilkan data contoh tetap, bukan data akun yang masuk. Kasus yang membandingkan angka dengan data sumber ditunda.

Pengujian modul lain yang tidak tergantung pada kerusakan itu boleh dilanjutkan.

## Test Deliverables

| Hasil | Keterangan |
| --- | --- |
| Test plan ini | Ketentuan pengujian |
| Daftar kasus uji manual | Actual Result dan Status diisi saat kasus dijalankan |
| Hasil tes Pest jalur inti | Dilampirkan pada laporan uji, sebelum demo 11 November 2026 |
| Laporan ringkasan uji | Disusun pada minggu 12 dan dibawa ke demo 11 November 2026 |

Laporan ringkasan memuat jumlah PASS, FAIL, dan kasus yang ditunda, daftar kasus jalur demo, cacat yang masih terbuka, serta hasil Pest.

## Testing Tasks

- Test plan disusun.
- Kasus manual tersedia pada daftar kasus uji.
- Lingkungan uji siap: aplikasi berjalan, migrasi terbaru, data uji, dan tiga akun peran.
- Kasus dijalankan sesuai jadwal di bagian Schedule.
- Actual Result dan Status diisi pada daftar kasus uji.
- Cacat jalur demo dicatat di kartu Trello modul terkait.
- Tes Pest jalur inti dijalankan dan hasilnya disimpan.
- Laporan ringkasan uji disusun sebelum demo.

## Environmental Needs

- Aplikasi TUMBUH UMKM berjalan di mesin uji, dengan basis data yang sudah dimigrasi ke skema yang dipakai aplikasi.
- Chrome di komputer desktop.
- Seeder jenis usaha dan lima kategori kendala: Modal, Pemasaran, Legalitas, Produksi, Digitalisasi. Ambang bawaan sedang 40 dan tinggi 70.
- Minimal satu set soal Likert per kategori sebelum kasus kuesioner dan skor dijalankan.
- Akun uji berikut disiapkan sebelum kasus login dijalankan. Kata sandi pada daftar kasus adalah Rahasia123.

| Email | Peran | Pemakaian |
| --- | --- | --- |
| pemilik@desa.test | `business_owner` | Kasus pemilik |
| petugas@desa.test | `officer` | Kasus petugas |
| kades@desa.test | `village_head` | Kasus kepala desa |

Akun `nonaktif@desa.test` dengan `is_active` false disiapkan khusus untuk TC_AUTH_007. Akun lain yang disebut pada satu kasus, misalnya email baru saat registrasi, dibuat pada saat kasus itu dijalankan.

Usaha contoh pada daftar kasus, Keripik Bu Sari, diisi lewat TC_BIZ_001. Kasus sesudahnya boleh memakai usaha itu selama pre-condition kasus terpenuhi. Jangan memakai usaha yang sama untuk dua kasus yang saling mengubah status tanpa membaca post-condition kasus sebelumnya.

## Responsibilities

Daffa Putra Prasetya mengumpulkan hasil uji dan menjaga tes Pest jalur inti pada minggu 12. Perbaikan cacat tetap di anggota yang memiliki modulnya.

| Modul | Yang memperbaiki | Yang menjalankan kasus manual |
| --- | --- | --- |
| Masuk, sesi, profil akun, skor, rekomendasi, dashboard petugas | Daffa Putra Prasetya | Hernanda Rizka Utami |
| Profil usaha, produk, kuesioner, tampilan rekomendasi pemilik, dashboard pemilik | Hernanda Rizka Utami | Nazwa Azahra Audina |
| Daftar UMKM, verifikasi, kategori, bank soal, program, laporan dan ekspor | Nazwa Azahra Audina | Radith Ferdian Hibawan |
| Menu, pengajuan, persetujuan, pembinaan, riwayat, dashboard kepala desa | Radith Ferdian Hibawan | Daffa Putra Prasetya |

Pasangan itu saling menukar hasil. Jika penguji berhalangan pada minggu yang terjadwal, anggota yang tersisa menjalankan kasus itu dan menulis namanya di Actual Result.

## Staffing And Training Needs

Pengujian dikerjakan keempat anggota. Tidak ada penguji di luar tim.

Sebelum kasus minggu 9 dijalankan, tiap penguji sudah bisa:

- masuk sebagai ketiga peran dan mengenali menu masing-masing
- membaca rumus skor dan tiga level pada bagian Features To Be Tested
- mengisi Actual Result tanpa menyalakan filter pada daftar kasus

Tidak ada pelatihan alat terpisah. Kasus yang membutuhkan data khusus, misalnya soal dengan bobot yang sudah dihitung, menyertakan angka itu di kolom Test Data.

## Schedule

Pengujian modul yang datanya sudah tersimpan dapat dimulai lebih awal. Target di bawah ini adalah yang dilaporkan ke dosen.

| Minggu | Tanggal | Laporan | Pengujian |
| --- | --- | --- | --- |
| 7 | 5–11 Oktober 2026 | Rabu, 7 Oktober | Test plan selesai. Kasus belum dijalankan sebagai hasil uji |
| 8 | 12–18 Oktober 2026 | Rabu, 14 Oktober | Demo Milestone 1: daftar, profil, produk, daftar UMKM, kategori, soal, program, dan dashboard kepala desa. Belum wajib menguji kuesioner tersimpan, skor, rekomendasi, pengajuan, atau ekspor |
| 9 | 19–25 Oktober 2026 | Rabu, 21 Oktober | TC_AUTH, TC_REG, TC_ACC, TC_AUTHZ, dan simpan profil usaha. Pest: masuk tiga peran dan simpan usaha. Satu UMKM menyelesaikan kuesioner, level kendala tampil, program yang cocok tampil, petugas dapat memverifikasi |
| 10 | 26 Oktober–1 November 2026 | Rabu, 28 Oktober | TC_ASM, TC_SCORE, TC_REC, TC_VER, TC_DASH_003. Petugas mengajukan program, kepala desa menyetujui atau menolak, pemilik melihat program di dashboard |
| 11 | 2–8 November 2026 | Rabu, 4 November | TC_FU, TC_COA, TC_RPT_001 sampai TC_RPT_004. Jalur dari pengajuan yang disetujui sampai sesi pembinaan tersimpan. Laporan dapat dibuka. Ekspor boleh masih kasar |
| 12 | 9–15 November 2026 | Rabu, 11 November | Sisa kasus pada Features To Be Tested, termasuk TC_RPT_005 dan TC_RPT_006. Demo produk dan hasil uji. Tidak menambah fitur baru pada minggu ini |

Kasus login Google tidak punya slot wajib pada jadwal ini.

## Risks And Contingencies

Jika masuk satu peran gagal, seluruh kasus peran itu tidak bisa dijalankan. Penguji menjeda peran tersebut sesuai Suspension Criteria, dan Daffa memperbaiki masuk serta middleware sebelum jadwal modul di atasnya dilanjutkan.

Jika kuesioner tidak mencapai `completed` pada minggu 9, skor, rekomendasi, dan laporan sebaran tidak bisa diuji pada minggu 10. Hernanda dan Daffa mendahulukan alur simpan jawaban sampai skor tampil, sebelum kasus tampilan lain ditambah.

Jika keputusan setujui atau tolak hanya berubah di layar dan hilang setelah dimuat ulang, kasus pembinaan tidak dijalankan. Radith memperbaiki penyimpanan pengajuan sebelum TC_COA dijalankan.

Jika TC_TYPE_003 belum punya halaman kelola pada saat diuji, kasus itu ditunda. TC_TYPE_001 tetap dijalankan memakai jenis usaha dari seeder. Penundaan TC_TYPE_003 tidak menggagalkan jalur demo.

Jika penguji menjalankan modul yang ia buat sendiri karena pasangan berhalangan, nama penguji ditulis di Actual Result. Kasus jalur demo tetap dijalankan ulang oleh anggota lain sebelum 11 November 2026.

Jika perbaikan minggu 9 sampai 11 meleset ke minggu 12, tim tidak menambah kasus baru. Yang dikerjakan adalah cacat yang menghalangi jalur demo tiga peran.

## Approvals

Pengujian minggu 12 dianggap selesai untuk demo jika syarat pada Pass/Fail Criteria terpenuhi dan keempat anggota menandatangani laporan ringkasan. Tanda tangan di bawah ini mengesahkan test plan, bukan hasil uji. Hasil uji disetujui terpisah pada minggu 12.

| Nama | Peran pada pengujian | Tanggal | Tanda tangan |
| --- | --- | --- | --- |
| Daffa Putra Prasetya | Pengumpul hasil uji dan tes Pest | | |
| Hernanda Rizka Utami | Penguji modul akun, skor, dan rekomendasi | | |
| Nazwa Azahra Audina | Penguji modul profil, produk, dan kuesioner | | |
| Radith Ferdian Hibawan | Penguji modul verifikasi, soal, program, dan laporan | | |
