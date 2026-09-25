# PRODUCT CONTEXT — TUMBUH UMKM

## 1. Identitas Produk

**Nama Produk:** TUMBUH UMKM

**Jenis Produk:** Sistem informasi berbasis web untuk pengelolaan dan pemetaan kondisi UMKM desa.

**Tagline/Positioning:**
> Dari Data, Menjadi Aksi untuk UMKM Desa.

TUMBUH UMKM adalah platform digital yang membantu pemerintah desa mendata, mengelola, memahami, dan melakukan pembinaan terhadap UMKM yang berada di wilayah desa.

Produk ini bukan sekadar aplikasi CRUD untuk menyimpan data UMKM. Fokus utama produk adalah mengubah data UMKM menjadi informasi yang dapat membantu pihak desa memahami:

- siapa saja UMKM yang ada di desa,
- bagaimana kondisi usaha mereka,
- apa kebutuhan dan kendala mereka,
- kelompok permasalahan apa yang paling banyak terjadi,
- program bantuan atau pembinaan apa yang relevan,
- dan tindak lanjut apa yang sudah diberikan kepada masing-masing UMKM.

---

# 2. Tujuan Utama Produk

TUMBUH UMKM dibuat untuk membantu desa berpindah dari proses pengelolaan UMKM yang bersifat manual dan tersebar menjadi pengelolaan yang:

- terpusat,
- terstruktur,
- mudah dipantau,
- berbasis data,
- dan dapat digunakan untuk menentukan tindak lanjut pembinaan.

Tujuan utama sistem:

1. Menyediakan database UMKM desa yang terpusat.
2. Memudahkan pelaku UMKM memberikan dan memperbarui informasi usahanya.
3. Membantu petugas desa memverifikasi dan mengelola data UMKM.
4. Mengidentifikasi kebutuhan dan kendala yang dialami UMKM.
5. Mengelompokkan UMKM berdasarkan karakteristik kebutuhan atau kendalanya.
6. Memberikan rekomendasi program bantuan/pembinaan yang relevan.
7. Menyimpan riwayat pembinaan dan tindak lanjut.
8. Memberikan dashboard dan laporan untuk membantu desa memahami kondisi UMKM secara keseluruhan.

---

# 3. Masalah yang Ingin Diselesaikan

UMKM di desa memiliki kondisi yang berbeda-beda.

Setiap UMKM dapat memiliki:

- jenis usaha berbeda,
- produk berbeda,
- tingkat legalitas berbeda,
- kondisi modal berbeda,
- kemampuan pemasaran berbeda,
- kemampuan digital berbeda,
- kendala produksi berbeda,
- dan kebutuhan pembinaan yang berbeda.

Jika data tersebut masih dicatat secara manual atau tersebar, pihak desa akan kesulitan menjawab pertanyaan seperti:

- Berapa jumlah UMKM yang aktif?
- UMKM apa saja yang ada di desa?
- UMKM mana yang belum memiliki NIB?
- Berapa banyak UMKM yang mengalami kendala modal?
- Masalah apa yang paling banyak dialami UMKM?
- UMKM mana yang membutuhkan pembinaan?
- Program apa yang relevan untuk UMKM tertentu?
- UMKM mana yang sudah pernah mendapatkan pembinaan?
- Apa tindak lanjut dari pembinaan sebelumnya?

TUMBUH UMKM menjadikan informasi tersebut tersedia dalam satu sistem.

---

# 4. Konsep Utama Produk

Konsep utama TUMBUH UMKM adalah:

> **Data → Profiling → Pengelompokan → Rekomendasi → Pembinaan → Monitoring**

Alur bisnis utamanya:

1. Data UMKM dikumpulkan.
2. Pelaku UMKM memberikan informasi mengenai usaha dan kondisi mereka.
3. Petugas desa memverifikasi dan mengelola data.
4. UMKM mengisi kebutuhan dan kendala.
5. Sistem melakukan analisis/pengelompokan berdasarkan kondisi UMKM.
6. Sistem menghasilkan profil kebutuhan atau kelompok permasalahan.
7. Sistem mencocokkan kebutuhan tersebut dengan program bantuan/pembinaan yang tersedia.
8. Petugas desa dapat melakukan tindak lanjut atau pembinaan.
9. Riwayat pembinaan disimpan.
10. Kepala desa dapat melihat ringkasan kondisi dan perkembangan UMKM melalui dashboard dan laporan.

---

# 5. Target Pengguna

TUMBUH UMKM memiliki tiga aktor utama:

## 5.1 Pelaku UMKM

Pelaku UMKM adalah pemilik usaha yang menjadi sumber data sekaligus penerima manfaat sistem.

Mereka menggunakan sistem untuk:

- memberikan data usaha,
- memperbarui informasi usaha,
- mengisi kondisi usaha,
- mengisi kebutuhan dan kendala,
- melihat status verifikasi data,
- dan melihat rekomendasi program yang relevan.

Pelaku UMKM tidak perlu memahami istilah teknis seperti clustering atau machine learning.

Hasil analisis harus ditampilkan dalam bahasa yang sederhana dan mudah dipahami.

Contoh:

> "Usaha Anda memiliki kebutuhan tinggi pada bidang pemasaran."

bukan:

> "Membership Cluster 2 = 0.82."

Nilai teknis dapat digunakan di sisi sistem/admin, tetapi tampilan untuk UMKM harus berorientasi pada pemahaman dan tindakan.

---

## 5.2 Petugas Desa

Petugas desa adalah pengguna utama dalam pengelolaan sistem.

Petugas bertanggung jawab untuk:

- mengelola data UMKM,
- memverifikasi data UMKM,
- mengelola produk,
- mengelola legalitas,
- mengelola kategori kebutuhan/kendala,
- melihat hasil pengelompokan UMKM,
- mengelola informasi program,
- melihat rekomendasi program,
- mengelola tindak lanjut pembinaan,
- melihat dashboard,
- dan membuat/mengunduh laporan.

Petugas desa merupakan pengguna yang paling banyak melakukan aktivitas administratif di sistem.

---

## 5.3 Kepala Desa

Kepala desa berperan sebagai pengguna untuk monitoring dan pengambilan keputusan tingkat desa.

Kepala desa tidak perlu melakukan seluruh aktivitas administratif seperti petugas.

Fokus utama Kepala Desa:

- melihat dashboard,
- melihat ringkasan data UMKM,
- melihat kondisi dan pengelompokan/kendala UMKM,
- melihat rekomendasi program,
- melihat laporan,
- melihat perkembangan pembinaan,
- dan menyetujui tindak lanjut pembinaan jika diperlukan.

Kepala Desa lebih berorientasi pada **monitoring dan keputusan**, bukan pengelolaan data harian.

---

# 6. Fitur Utama

## 6.1 Registrasi dan Login

Sistem menyediakan autentikasi untuk pengguna.

Pengguna memiliki akses yang berbeda berdasarkan role:

- Pelaku UMKM
- Petugas Desa
- Kepala Desa

Setiap role hanya dapat mengakses fitur yang sesuai dengan kewenangannya.

---

# 7. Modul Data UMKM

## 7.1 Profil UMKM

Data yang dapat disimpan antara lain:

- nama usaha,
- nama pemilik,
- alamat,
- kontak,
- jenis usaha,
- tahun mulai usaha,
- jumlah tenaga kerja,
- informasi usaha lainnya.

Tujuannya adalah membuat profil setiap UMKM secara terstruktur.

---

## 7.2 Data Produk

Setiap UMKM dapat memiliki satu atau lebih produk.

Informasi produk dapat mencakup:

- nama produk,
- kategori produk,
- deskripsi,
- harga,
- informasi pendukung lainnya.

Data produk menjadi bagian dari profil UMKM.

---

## 7.3 Data Legalitas

Sistem menyimpan status legalitas UMKM.

Contoh:

- NIB,
- NPWP,
- sertifikasi halal,
- PIRT/BPOM jika relevan,
- izin usaha lainnya.

Status legalitas dapat digunakan sebagai salah satu indikator untuk memahami kondisi UMKM.

---

# 8. Modul Kebutuhan dan Kendala UMKM

Ini merupakan salah satu modul terpenting dalam TUMBUH UMKM.

Pelaku UMKM dapat memberikan informasi mengenai kondisi dan kendala yang sedang dialami.

Contoh kategori kendala:

- Modal
- Pemasaran
- Legalitas
- Produksi
- Digitalisasi
- Kategori lain yang dapat ditambahkan sesuai kebutuhan sistem

Input sebaiknya menggunakan form terstruktur sehingga data dapat dianalisis.

Contoh indikator:

### Modal

- kondisi modal,
- kebutuhan tambahan modal,
- kesulitan mendapatkan pembiayaan,
- hambatan mendapatkan modal.

### Pemasaran

- kesulitan mendapatkan pelanggan,
- jangkauan pasar,
- penggunaan media sosial,
- penggunaan marketplace,
- kemampuan promosi.

### Legalitas

- kepemilikan NIB,
- kepemilikan sertifikasi,
- pemahaman prosedur legalitas,
- hambatan pengurusan legalitas.

### Produksi

- kapasitas produksi,
- ketersediaan bahan baku,
- peralatan,
- tenaga kerja,
- hambatan produksi.

### Digitalisasi

- penggunaan media sosial,
- WhatsApp Business,
- marketplace,
- pembukuan digital,
- pembayaran digital,
- kemampuan menggunakan teknologi.

---

# 9. Smart UMKM Profiling / Pengelompokan Otomatis

Salah satu fitur pembeda utama TUMBUH UMKM adalah kemampuan sistem melakukan profiling atau pengelompokan UMKM berdasarkan kondisi dan kebutuhan mereka.

Sistem tidak hanya menyimpan jawaban pengguna, tetapi mengolah data tersebut menjadi informasi mengenai karakteristik kebutuhan UMKM.

## Pendekatan yang dapat digunakan

Produk dirancang agar dapat menggunakan **Fuzzy Clustering**, khususnya **Fuzzy C-Means (FCM)**.

Alasan penggunaan fuzzy clustering adalah karena satu UMKM dapat mengalami beberapa masalah sekaligus.

Contoh:

```text
UMKM A

Modal       : 0.82
Pemasaran   : 0.65
Legalitas   : 0.20
Produksi    : 0.15
Digitalisasi: 0.55