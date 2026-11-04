# BAB II — PENGENALAN WEBSITE

[← BAB I](./01_Pendahuluan.md) | [Lanjut: BAB III →](./03_Persiapan_Penggunaan.md)

---

## 2.1 Gambaran Umum TUMBUH UMKM

**TUMBUH UMKM** adalah sistem informasi berbasis web yang dirancang untuk mendukung pengelolaan dan pengembangan Usaha Mikro, Kecil, dan Menengah (UMKM) di tingkat desa.

Nama **TUMBUH** mencerminkan visi sistem ini: mendorong pertumbuhan usaha mikro melalui pendataan yang akurat, penilaian kebutuhan yang berbasis data, dan pemberian rekomendasi program bantuan yang tepat sasaran.

Sistem ini bekerja dengan menghubungkan tiga pihak utama:
1. **Pelaku UMKM** — mendaftarkan data usaha dan mengisi kuesioner kebutuhan
2. **Petugas Desa** — memverifikasi data, mengelola program, dan merencanakan pembinaan
3. **Kepala Desa** — memantau kondisi UMKM dan menyetujui rencana tindak lanjut

---

## 2.2 Fungsi dan Kegunaan Website

Website TUMBUH UMKM menyediakan fungsi-fungsi utama berikut:

### Fungsi untuk Pelaku UMKM
| No. | Fungsi | Keterangan |
|-----|--------|-----------|
| 1 | **Profil Usaha** | Mendaftarkan dan memperbarui data identitas usaha |
| 2 | **Data Produk** | Mencatat produk yang dijual beserta harga |
| 3 | **Kuesioner Kendala** | Mengisi asesmen kebutuhan dan kendala usaha |
| 4 | **Rekomendasi Program** | Melihat dan mendaftarkan minat program bantuan |
| 5 | **Riwayat Pembinaan** | Memantau sesi pembinaan yang telah diikuti |

### Fungsi untuk Petugas Desa
| No. | Fungsi | Keterangan |
|-----|--------|-----------|
| 1 | **Verifikasi UMKM** | Memeriksa dan memverifikasi data profil usaha |
| 2 | **Pendataan UMKM** | Mendata UMKM yang belum memiliki akun sendiri |
| 3 | **Jenis Usaha** | Mengelola kategori/jenis usaha yang tersedia |
| 4 | **Kategori Kendala** | Mengelola kategori kendala usaha |
| 5 | **Bank Soal** | Mengelola pertanyaan kuesioner asesmen |
| 6 | **Program Bantuan** | Mengelola data program bantuan |
| 7 | **Tindak Lanjut** | Mengajukan rencana tindak lanjut pembinaan |
| 8 | **Catat Pembinaan** | Mencatat sesi pembinaan yang telah dilakukan |
| 9 | **Pengelompokan** | Melihat pengelompokan UMKM berdasarkan kendala |
| 10 | **Rekomendasi** | Melihat hasil rekomendasi program per UMKM |
| 11 | **Laporan** | Menghasilkan laporan ringkasan dan mengekspor PDF/Excel |

### Fungsi untuk Kepala Desa
| No. | Fungsi | Keterangan |
|-----|--------|-----------|
| 1 | **Dashboard** | Memantau statistik kondisi UMKM secara keseluruhan |
| 2 | **Pengelompokan** | Melihat pengelompokan UMKM berdasarkan kendala |
| 3 | **Rekomendasi** | Memantau distribusi rekomendasi program |
| 4 | **Laporan** | Mengunduh laporan kondisi UMKM (PDF/Excel) |
| 5 | **Persetujuan Tindak Lanjut** | Menyetujui atau menolak rencana pembinaan |
| 6 | **Riwayat Pembinaan** | Memantau riwayat seluruh sesi pembinaan |

---

## 2.3 Teknologi yang Digunakan

Website TUMBUH UMKM dibangun dengan teknologi modern berikut:

| Komponen | Teknologi | Keterangan |
|----------|-----------|-----------|
| **Backend** | Laravel 11 | Framework PHP untuk logika bisnis dan API |
| **Bahasa Pemrograman** | PHP 8.5 | Server-side scripting |
| **Frontend (Interaktif)** | React.js | Library JavaScript untuk komponen UI dinamis |
| **Tampilan** | Tailwind CSS | Framework CSS utility-first |
| **Template Engine** | Blade | Template engine bawaan Laravel |
| **Build Tool** | Vite | Bundler frontend modern |
| **Database** | SQLite | Basis data relasional |
| **Autentikasi OAuth** | Google OAuth 2.0 | Login menggunakan akun Google |
| **UI Components** | shadcn/ui (Radix UI) | Komponen UI yang dapat diakses |
| **Icons** | Hugeicons | Paket ikon antarmuka |

---

## 2.4 Jenis Pengguna dan Hak Akses

Sistem TUMBUH UMKM memiliki tiga peran pengguna (role) dengan hak akses yang berbeda:

### Tabel 1: Jenis Pengguna dan Hak Akses

| Fitur | Pemilik UMKM | Petugas Desa | Kepala Desa |
|-------|:---:|:---:|:---:|
| Halaman Landing (publik) | ✅ | ✅ | ✅ |
| Registrasi akun | ✅ | — | — |
| Login | ✅ | ✅ | ✅ |
| Profil Akun | ✅ | ✅ | ✅ |
| Dashboard UMKM | ✅ | — | — |
| Profil Usaha (sendiri) | ✅ | — | — |
| Data Produk | ✅ | — | — |
| Kuesioner Kebutuhan & Kendala | ✅ | — | — |
| Rekomendasi Program (UMKM) | ✅ | — | — |
| Riwayat Pembinaan (UMKM) | ✅ | — | — |
| Dashboard Petugas/Pimpinan | — | ✅ | ✅ |
| Data UMKM (verifikasi) | — | ✅ | — |
| Pendataan UMKM (petugas) | — | ✅ | — |
| Jenis Usaha | — | ✅ | — |
| Kategori Kendala | — | ✅ | — |
| Bank Soal | — | ✅ | — |
| Program Bantuan | — | ✅ | — |
| Tindak Lanjut (ajukan) | — | ✅ | — |
| Catat Pembinaan | — | ✅ | — |
| Pengelompokan UMKM | — | ✅ | ✅ |
| Rekomendasi (pantau) | — | ✅ | ✅ |
| Laporan (PDF/Excel) | — | ✅ | ✅ |
| Persetujuan Tindak Lanjut | — | — | ✅ |
| Riwayat Pembinaan (pantau) | — | — | ✅ |

> [!NOTE]
> Petugas Desa dan Kepala Desa mendapatkan akun yang dibuat langsung oleh administrator sistem (tidak melalui registrasi publik). Hanya Pemilik UMKM yang dapat mendaftar sendiri melalui halaman registrasi.

---

## 2.5 Struktur Navigasi Website

### 2.5.1 Halaman Publik (Tanpa Login)

```
/  (Halaman Landing)
├── Beranda (Hero section)
├── Masalah yang Diselesaikan
├── Fitur Utama
├── Cara Kerja
├── Profiling Pengguna
├── Peran Pengguna
├── Dashboard Preview
├── FAQ
├── Call to Action
└── Footer
```

### 2.5.2 Halaman Autentikasi

```
/login          → Halaman Masuk
/register       → Halaman Daftar (Pemilik UMKM)
/forgot-password → Lupa Kata Sandi
/reset-password/{token} → Reset Kata Sandi
/auth/google    → Login dengan Google
```

### 2.5.3 Navigasi Pemilik UMKM (Setelah Login)

```
Dashboard UMKM (/umkm/dashboard)
│
├── UTAMA
│   └── Dashboard (/umkm/dashboard)
│
├── USAHA
│   ├── Profil Usaha (/umkm/profil)
│   ├── Produk (/umkm/produk)
│   └── Kebutuhan dan Kendala (/umkm/kebutuhan)
│
├── HASIL
│   ├── Rekomendasi Program (/umkm/rekomendasi)
│   └── Riwayat Pembinaan (/umkm/riwayat-pembinaan)
│
└── AKUN
    └── Profil Akun (/akun)
```

### 2.5.4 Navigasi Petugas Desa (Setelah Login)

```
Dashboard (/dashboard)
│
├── UTAMA
│   └── Dashboard (/dashboard)
│
├── PENGELOLAAN
│   ├── Data UMKM (/petugas/umkm)
│   ├── Pendataan UMKM (/petugas/umkm/pendataan-umkm)
│   ├── Jenis Usaha (/petugas/jenis-usaha)
│   ├── Kategori Kendala (/petugas/kategori-kendala)
│   ├── Bank Soal (/petugas/bank-soal)
│   └── Program Bantuan (/petugas/program-bantuan)
│
├── TINDAK LANJUT
│   ├── Ajukan Tindak Lanjut (/petugas/tindak-lanjut)
│   └── Riwayat Pembinaan (/petugas/catat-pembinaan)
│
├── ANALISIS
│   ├── Pengelompokan (/petugas/pengelompokan)
│   ├── Rekomendasi (/petugas/rekomendasi)
│   └── Laporan (/petugas/laporan)
│
└── AKUN
    └── Profil Akun (/akun)
```

### 2.5.5 Navigasi Kepala Desa (Setelah Login)

```
Dashboard (/dashboard)
│
├── UTAMA
│   └── Dashboard (/dashboard)
│
├── PEMANTAUAN
│   ├── Data UMKM [dalam pengembangan]
│   ├── Pengelompokan (/pimpinan/pengelompokan)
│   ├── Rekomendasi (/pimpinan/rekomendasi)
│   └── Laporan (/pimpinan/laporan)
│
├── KEPUTUSAN
│   ├── Persetujuan Tindak Lanjut (/pimpinan/persetujuan-tindak-lanjut)
│   └── Riwayat Pembinaan (/pimpinan/riwayat-pembinaan)
│
└── AKUN
    └── Profil Akun (/akun)
```

> [!IMPORTANT]
> Menu "Data UMKM" pada navigasi Kepala Desa saat ini **dalam pengembangan** dan belum dapat diakses. Menu tersebut ditampilkan dengan warna abu-abu dan tidak dapat diklik.

---

## 2.6 Penjelasan Menu dan Submenu

### 2.6.1 Elemen Navigasi Umum (Semua Peran)

**Sidebar (Panel Navigasi Kiri)**
- Menampilkan logo TumbuhUMKM di bagian atas
- Daftar menu dikelompokkan berdasarkan kategori
- Menu yang sedang aktif ditandai dengan latar belakang berwarna hijau (brand color)
- Menu yang dalam pengembangan ditampilkan dengan teks abu-abu dan tidak dapat diklik
- Tombol **Keluar** di bagian bawah sidebar untuk logout
- Informasi nama desa ditampilkan di bawah tombol Keluar

**Header Halaman**
- Judul halaman yang sedang diakses
- Nama peran pengguna yang sedang login

---

[← BAB I](./01_Pendahuluan.md) | [Lanjut: BAB III →](./03_Persiapan_Penggunaan.md)
