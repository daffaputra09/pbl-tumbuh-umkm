# BAB V — PANDUAN ALUR PENGGUNAAN

[← BAB IV](./04_Panduan_Penggunaan.md) | [Lanjut: BAB VI →](./06_Penanganan_Masalah.md)

---

## Pengantar

Bab ini menjelaskan alur penggunaan sistem TUMBUH UMKM berdasarkan skenario nyata yang melibatkan beberapa pengguna secara berurutan. Setiap alur digambarkan dengan diagram *flowchart* untuk mempermudah pemahaman.

---

## 5.1 Alur 1: Pendaftaran dan Pengisian Data UMKM

**Tujuan Alur**  
Pemilik UMKM berhasil mendaftarkan akun, melengkapi profil usaha, mengisi produk, dan kuesioner kebutuhan sehingga mendapatkan rekomendasi program bantuan.

**Pengguna yang Terlibat**  
Pemilik UMKM

**Persyaratan Awal**  
- Pemilik UMKM belum memiliki akun di sistem
- Pemilik UMKM memiliki email aktif dan nomor telepon

**Urutan Proses**

```mermaid
flowchart TD
    A([Mulai]) --> B[Akses website TUMBUH UMKM]
    B --> C[Klik tombol Daftar]
    C --> D[Isi formulir registrasi:\nNama, Email, Nama Usaha, Kata Sandi]
    D --> E{Validasi\nberhasil?}
    E -- Tidak --> D
    E -- Ya --> F[Akun berhasil dibuat]
    F --> G[Diarahkan ke halaman\nProfil Usaha]
    G --> H[Isi formulir profil usaha\nlengkap]
    H --> I{Semua field\nwajib terisi?}
    I -- Tidak --> H
    I -- Ya --> J[Klik Simpan Profil Usaha]
    J --> K[Profil tersimpan\nStatus: Menunggu Verifikasi]
    K --> L[Diarahkan ke\nDashboard UMKM]
    L --> M[Isi data Produk\nmelalui menu Produk]
    M --> N[Isi Kuesioner\nKebutuhan dan Kendala]
    N --> O[Kirim Jawaban\nKuesioner]
    O --> P[Rekomendasi program\nmuncul di Dashboard]
    P --> Q([Selesai])
```

**Hasil Akhir**  
Pemilik UMKM memiliki profil usaha yang terisi lengkap, daftar produk, dan rekomendasi program bantuan yang sesuai dengan kendala usaha.

---

## 5.2 Alur 2: Verifikasi Data UMKM oleh Petugas

**Tujuan Alur**  
Petugas Desa melakukan verifikasi data profil UMKM yang sudah didaftarkan oleh pemilik usaha.

**Pengguna yang Terlibat**  
- Pemilik UMKM (mengirimkan data)
- Petugas Desa (memverifikasi)

**Persyaratan Awal**  
- Pemilik UMKM sudah mengisi dan menyimpan profil usaha
- Status profil: "Menunggu Verifikasi"

**Urutan Proses**

```mermaid
flowchart TD
    A([Mulai]) --> B[Petugas login ke sistem]
    B --> C[Buka menu Data UMKM]
    C --> D[Cari atau filter UMKM\ndengan status Menunggu Verifikasi]
    D --> E[Klik tombol Detail\npada UMKM yang dipilih]
    E --> F[Periksa Profil Usaha\ndan Data Pemilik]
    F --> G[Periksa Data Produk\ndan Deskripsi Usaha]
    G --> H[Periksa Hasil Kuesioner\njika sudah diisi]
    H --> I{Data\nlengkap\ndan valid?}
    I -- Ya --> J[Pilih status: Terverifikasi]
    I -- Tidak --> K[Pilih status: Perlu Perbaikan]
    J --> L[Isi catatan opsional]
    K --> M[Isi catatan perbaikan\nWAJIB minimal 5 karakter]
    L --> N[Klik Simpan\nKeputusan Verifikasi]
    M --> N
    N --> O{Keputusan?}
    O -- Terverifikasi --> P[Status UMKM\nberubah: Terverifikasi]
    O -- Perlu Perbaikan --> Q[Status UMKM: Perlu Perbaikan\nPemilik menerima catatan]
    P --> R([Selesai])
    Q --> S[Pemilik UMKM\nmemperbaiki data]
    S --> T[Pemilik simpan ulang profil]
    T --> U[Status kembali:\nMenunggu Verifikasi]
    U --> D
```

**Hasil Akhir**  
Data UMKM berhasil diverifikasi dan status diperbarui. Jika "Perlu Perbaikan", pemilik memperbaiki data dan siklus verifikasi berulang.

---

## 5.3 Alur 3: Rekomendasi dan Pendaftaran Minat Program Bantuan

**Tujuan Alur**  
UMKM mendapatkan rekomendasi program bantuan dan mendaftarkan minat untuk mengikuti program tersebut.

**Pengguna yang Terlibat**  
- Pemilik UMKM
- Petugas Desa (mengelola program)

**Persyaratan Awal**  
- UMKM sudah mengisi kuesioner kebutuhan dan kendala
- Petugas sudah menginput program bantuan yang terkait dengan kategori kendala

**Urutan Proses**

```mermaid
flowchart TD
    A([Mulai]) --> B[Pemilik UMKM buka\nDashboard UMKM]
    B --> C[Lihat kartu Rekomendasi Program]
    C --> D{Ada program\nyang cocok?}
    D -- Tidak --> E[Isi/perbarui kuesioner\nKebutuhan dan Kendala]
    E --> F[Hubungi Petugas untuk\nprogram tambahan]
    D -- Ya --> G[Klik Lihat detail\npada program yang diminati]
    G --> H[Baca informasi program\nlengkap]
    H --> I{Berminat\nmengikuti?}
    I -- Tidak --> C
    I -- Ya --> J[Klik tombol\nDaftarkan minat]
    J --> K[Konfirmasi pendaftaran minat]
    K --> L[Status program:\nSudah didaftarkan]
    L --> M[Petugas menerima\ndata minat UMKM]
    M --> N([Selesai - Lanjut ke\nAlur 4: Tindak Lanjut])
```

**Hasil Akhir**  
Pemilik UMKM berhasil mendaftarkan minat pada program bantuan dan data tersedia bagi Petugas untuk ditindaklanjuti.

---

## 5.4 Alur 4: Tindak Lanjut dan Pembinaan UMKM

**Tujuan Alur**  
Petugas Desa membuat rencana tindak lanjut pembinaan, mendapatkan persetujuan Kepala Desa, dan mencatat hasil pelaksanaan pembinaan.

**Pengguna yang Terlibat**  
- Petugas Desa
- Kepala Desa
- Pemilik UMKM (penerima pembinaan)

**Persyaratan Awal**  
- UMKM sudah mendaftarkan minat pada program bantuan
- Petugas memiliki akses ke menu Ajukan Tindak Lanjut

**Urutan Proses**

```mermaid
flowchart TD
    A([Mulai]) --> B[Petugas buka menu\nAjukan Tindak Lanjut]
    B --> C[Lihat daftar UMKM\nyang mendaftar minat]
    C --> D[Pilih UMKM dan program\nyang akan ditindaklanjuti]
    D --> E[Isi formulir rencana:\nKegiatan dan tanggal rencana]
    E --> F[Klik Ajukan]
    F --> G[Status rencana:\nMenunggu Persetujuan]
    G --> H[Kepala Desa buka\nPersetujuan Tindak Lanjut]
    H --> I[Tinjau rencana\ntindak lanjut]
    I --> J{Keputusan\nKepala Desa?}
    J -- Setujui --> K[Status: Disetujui]
    J -- Tolak --> L[Status: Ditolak\nPetugas diberi tahu]
    L --> M[Petugas revisi rencana\natau ajukan ulang]
    M --> F
    K --> N[Petugas laksanakan\nsesi pembinaan]
    N --> O[Petugas buka menu\nCatat Pembinaan]
    O --> P[Isi formulir sesi:\nJudul, tanggal, hasil pembinaan]
    P --> Q[Simpan catatan sesi]
    Q --> R[Riwayat pembinaan\ntersimpan]
    R --> S[Pemilik UMKM dapat melihat\nriwayat di menu Riwayat Pembinaan]
    S --> T([Selesai])
```

**Hasil Akhir**  
Sesi pembinaan terlaksana dan terdokumentasi dalam sistem. Pemilik UMKM dapat melihat riwayat pembinaan yang telah diikuti.

---

## 5.5 Alur 5: Pemantauan dan Pelaporan oleh Kepala Desa

**Tujuan Alur**  
Kepala Desa memantau kondisi UMKM secara keseluruhan dan mengunduh laporan untuk keperluan pelaporan atau pengambilan keputusan.

**Pengguna yang Terlibat**  
Kepala Desa

**Persyaratan Awal**  
- Kepala Desa memiliki akun dan sudah login
- Sudah ada data UMKM yang terisi di sistem

**Urutan Proses**

```mermaid
flowchart TD
    A([Mulai]) --> B[Kepala Desa login\nke sistem]
    B --> C[Dashboard menampilkan\nstatistik umum UMKM]
    C --> D{Ingin\npemantauan\ndetail?}
    D -- Pengelompokan --> E[Buka menu Pengelompokan\nLihat distribusi kendala per UMKM]
    D -- Rekomendasi --> F[Buka menu Rekomendasi\nPantau program yang direkomendasikan]
    D -- Laporan --> G[Buka menu Laporan]
    G --> H[Terapkan filter:\nPeriode dan/atau Kategori Kendala]
    H --> I[Tinjau data:\nRingkasan, Sebaran Kendala,\nPerkembangan Pembinaan]
    I --> J{Perlu\ndiekspor?}
    J -- PDF --> K[Klik Ekspor PDF\nUnduh file PDF]
    J -- Excel --> L[Klik Ekspor Excel\nUnduh file Excel]
    J -- Tidak --> M[Selesai review]
    K --> M
    L --> M
    E --> M
    F --> M
    M --> N([Selesai])
```

**Hasil Akhir**  
Kepala Desa mendapatkan gambaran lengkap kondisi UMKM dan memiliki dokumen laporan untuk referensi.

---

## 5.6 Alur 6: Pendataan UMKM oleh Petugas (UMKM tanpa Akun)

**Tujuan Alur**  
Petugas Desa mendata UMKM yang pemiliknya belum memiliki akun di sistem.

**Pengguna yang Terlibat**  
Petugas Desa

**Persyaratan Awal**  
- Petugas sudah login ke sistem
- Data UMKM yang akan didata sudah tersedia (formulir fisik atau wawancara langsung)

**Urutan Proses**

```mermaid
flowchart TD
    A([Mulai]) --> B[Petugas buka menu\nPendataan UMKM]
    B --> C[Formulir data usaha tampil]
    C --> D[Isi seluruh data UMKM\nyang akan didaftarkan]
    D --> E{Semua field\nwajib terisi?}
    E -- Tidak --> D
    E -- Ya --> F[Klik Simpan Data UMKM]
    F --> G{Berhasil?}
    G -- Tidak --> H[Periksa pesan error\ndan perbaiki data]
    H --> D
    G -- Ya --> I[Pesan sukses tampil\nFormulir dikosongkan otomatis]
    I --> J{Masih ada\nUMKM lain\nyang didata?}
    J -- Ya --> D
    J -- Tidak --> K([Selesai])
```

**Hasil Akhir**  
Data UMKM berhasil tersimpan di sistem dan dapat ditemukan di halaman Data UMKM untuk proses verifikasi selanjutnya.

---

[← BAB IV](./04_Panduan_Penggunaan.md) | [Lanjut: BAB VI →](./06_Penanganan_Masalah.md)
