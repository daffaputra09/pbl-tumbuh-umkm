# BAB I — PENDAHULUAN

[← Halaman Awal](./00_Halaman_Awal.md) | [Lanjut: BAB II →](./02_Pengenalan_Website.md)

---

## 1.1 Latar Belakang

Usaha Mikro, Kecil, dan Menengah (UMKM) merupakan tulang punggung perekonomian masyarakat desa. Namun, pengelolaan data UMKM di tingkat desa seringkali masih dilakukan secara manual dan tidak terstruktur, sehingga menyulitkan petugas desa dalam memantau perkembangan, mengidentifikasi kendala, serta menentukan program bantuan yang tepat sasaran.

Beberapa permasalahan umum yang dihadapi dalam pengelolaan UMKM desa antara lain:

- **Pendataan tidak terstruktur:** Data UMKM tersebar di berbagai dokumen fisik atau spreadsheet yang sulit diakses dan diperbarui.
- **Verifikasi lambat:** Proses verifikasi data usaha dilakukan secara manual dan memakan waktu.
- **Rekomendasi tidak tepat sasaran:** Program bantuan sering kali tidak sesuai dengan kebutuhan riil pelaku UMKM karena keterbatasan data.
- **Pemantauan terbatas:** Kepala Desa kesulitan mendapatkan gambaran menyeluruh tentang kondisi UMKM di wilayahnya.
- **Tindak lanjut pembinaan tidak terdokumentasi:** Riwayat sesi pembinaan dan tindak lanjut tidak tercatat dengan baik.

Sebagai respons terhadap permasalahan tersebut, dikembangkanlah sistem informasi **TUMBUH UMKM** — sebuah platform digital yang mengintegrasikan proses pendataan, verifikasi, penilaian kebutuhan, pengelompokan, dan rekomendasi program bantuan dalam satu sistem yang terpadu dan mudah digunakan.

---

## 1.2 Tujuan Pembuatan Website

Website TUMBUH UMKM dikembangkan dengan tujuan-tujuan berikut:

1. **Digitalisasi Pendataan UMKM**  
   Menyediakan platform digital untuk mendaftarkan, memperbarui, dan mengelola data UMKM desa secara terpusat.

2. **Mempermudah Verifikasi Data**  
   Memberikan alat bagi Petugas Desa untuk memverifikasi keabsahan data usaha yang disampaikan oleh pelaku UMKM.

3. **Penilaian Kebutuhan Berbasis Kuesioner**  
   Mengidentifikasi kendala dan kebutuhan riil pelaku UMKM melalui asesmen kuesioner yang terstruktur.

4. **Pengelompokan Otomatis Berdasarkan Kendala**  
   Mengelompokkan UMKM berdasarkan kendala utama yang teridentifikasi untuk mempermudah penentuan prioritas pembinaan.

5. **Rekomendasi Program Bantuan Tepat Sasaran**  
   Mencocokkan kendala usaha dengan program bantuan yang tersedia secara otomatis dan berbasis data.

6. **Alur Pembinaan Terstruktur**  
   Mendokumentasikan rencana tindak lanjut, proses persetujuan, dan riwayat sesi pembinaan untuk setiap UMKM.

7. **Pelaporan dan Pemantauan Komprehensif**  
   Menyediakan laporan ringkasan kondisi UMKM yang dapat diekspor dalam format PDF atau Excel.

---

## 1.3 Manfaat Website

### Bagi Pelaku UMKM

- Dapat mendaftarkan dan memperbarui data usaha secara mandiri kapan saja.
- Mendapatkan rekomendasi program bantuan yang sesuai dengan kendala usaha.
- Dapat memantau status verifikasi data usahanya secara real-time.
- Mendapatkan riwayat sesi pembinaan yang telah diikuti.

### Bagi Petugas Desa

- Memiliki data UMKM yang terstruktur, terverifikasi, dan mudah diakses.
- Dapat mengelola program bantuan dan mencocokkannya dengan kebutuhan UMKM secara otomatis.
- Dapat mengajukan rencana tindak lanjut pembinaan kepada Kepala Desa.
- Dapat menghasilkan laporan kondisi UMKM secara cepat.

### Bagi Kepala Desa

- Mendapatkan gambaran menyeluruh tentang kondisi UMKM di desa melalui dashboard dan laporan.
- Dapat memantau distribusi kendala usaha dan rekomendasi program yang diberikan.
- Dapat menyetujui atau menolak rencana tindak lanjut yang diajukan petugas.
- Dapat mengambil keputusan berbasis data untuk kebijakan pengembangan UMKM.

---

## 1.4 Ruang Lingkup Sistem

Sistem TUMBUH UMKM mencakup ruang lingkup fungsional berikut:

### Modul Autentikasi
- Registrasi akun bagi pelaku UMKM (email/password atau Google OAuth)
- Login dan logout
- Pemulihan kata sandi via email

### Modul Profil Usaha
- Pengisian dan pembaruan profil UMKM oleh pemilik
- Pendataan UMKM oleh petugas (untuk UMKM yang belum memiliki akun)
- Verifikasi dan pengelolaan status data oleh petugas

### Modul Produk
- Pencatatan produk yang dijual UMKM
- Pengelolaan status aktif/nonaktif produk

### Modul Asesmen Kebutuhan & Kendala
- Pengisian kuesioner kebutuhan dan kendala usaha
- Penilaian skor kendala per kategori secara otomatis
- Pengelolaan bank soal dan kategori kendala oleh petugas

### Modul Program Bantuan & Rekomendasi
- Pengelolaan data program bantuan oleh petugas
- Pencocokan otomatis program dengan kendala UMKM
- Pendaftaran minat peserta oleh UMKM

### Modul Tindak Lanjut & Pembinaan
- Pengajuan rencana tindak lanjut oleh petugas
- Persetujuan/penolakan oleh Kepala Desa
- Pencatatan dan riwayat sesi pembinaan

### Modul Analisis & Laporan
- Dashboard kondisi UMKM (petugas & kepala desa)
- Pengelompokan UMKM berdasarkan kendala
- Laporan ringkasan dengan ekspor PDF dan Excel

---

## 1.5 Sasaran Pengguna

Buku panduan ini ditujukan kepada tiga kelompok pengguna:

| No. | Pengguna | Deskripsi |
|-----|---------|-----------|
| 1 | **Petugas Desa** | Aparatur desa yang bertugas mengelola dan memverifikasi data UMKM, menyusun rencana pembinaan, dan menghasilkan laporan. |
| 2 | **Pelaku UMKM** | Pemilik atau pengelola usaha mikro, kecil, dan menengah yang terdaftar di desa dan menggunakan sistem untuk mendaftarkan serta memperbarui data usahanya. |
| 3 | **Kepala Desa** | Pimpinan desa yang memantau kondisi UMKM secara keseluruhan dan mengambil keputusan terkait tindak lanjut pembinaan. |

---

## 1.6 Struktur Buku Panduan

Buku panduan ini disusun dalam tujuh bab sebagai berikut:

| Bab | Judul | Isi |
|-----|-------|-----|
| **BAB I** | Pendahuluan | Latar belakang, tujuan, manfaat, ruang lingkup, dan sasaran pengguna |
| **BAB II** | Pengenalan Website | Gambaran umum sistem, teknologi, jenis pengguna, dan struktur navigasi |
| **BAB III** | Persiapan Penggunaan | Spesifikasi perangkat, browser, dan cara mengakses website |
| **BAB IV** | Panduan Penggunaan | Panduan lengkap per fitur untuk setiap peran pengguna |
| **BAB V** | Alur Penggunaan | Alur penggunaan berdasarkan skenario nyata dengan diagram |
| **BAB VI** | Penanganan Masalah | Tabel permasalahan umum beserta solusinya |
| **BAB VII** | Penutup | Kesimpulan, saran, dan informasi kontak |
| **Lampiran** | — | Glosarium, daftar menu, matriks hak akses, dan diagram sistem |

---

[← Halaman Awal](./00_Halaman_Awal.md) | [Lanjut: BAB II →](./02_Pengenalan_Website.md)
