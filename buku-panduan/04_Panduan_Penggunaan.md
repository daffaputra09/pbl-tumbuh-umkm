# BAB IV — PANDUAN PENGGUNAAN WEBSITE

[← BAB III](./03_Persiapan_Penggunaan.md) | [Lanjut: BAB V →](./05_Alur_Penggunaan.md)

---

## 4.1 Panduan Pengunjung (Tanpa Login)

### 4.1.1 Menjelajahi Halaman Landing

**A. Deskripsi Fitur**  
Halaman landing adalah halaman pertama yang dilihat pengunjung ketika mengakses website TUMBUH UMKM. Halaman ini bersifat publik dan dapat diakses tanpa login. Halaman ini berisi informasi tentang sistem, fitur, cara kerja, dan ajakan untuk mendaftar.

**B. Lokasi Menu**  
Dapat diakses langsung melalui URL utama website.

**C. Tampilan Halaman**  
- Navbar dengan logo dan tombol navigasi
- Berbagai section informatif yang disusun secara vertikal
- Desain modern dengan animasi transisi halus
- Tombol-tombol ajakan tindakan (CTA)

**D. Langkah Penggunaan**
1. Buka browser dan akses URL website TUMBUH UMKM.
2. Halaman landing akan tampil secara otomatis.
3. Gulir halaman ke bawah untuk membaca informasi lebih lanjut tentang sistem.
4. Klik tombol **"Daftar"** (di navbar atau di hero section) untuk mendaftar sebagai Pemilik UMKM.
5. Klik tombol **"Masuk"** untuk mengakses sistem dengan akun yang sudah ada.
6. Klik bagian FAQ untuk membuka jawaban atas pertanyaan umum.

**E. Fungsi Komponen**
- **Tombol "Daftar"**: Mengarahkan ke halaman pendaftaran Pemilik UMKM
- **Tombol "Masuk"**: Mengarahkan ke halaman login
- **FAQ Accordion**: Klik pertanyaan untuk menampilkan atau menyembunyikan jawaban
- **Tombol CTA "Mulai Sekarang"**: Mengarahkan ke halaman pendaftaran

**F. Hasil Penggunaan**  
Pengunjung mendapatkan informasi lengkap tentang sistem TUMBUH UMKM dan dapat memilih untuk mendaftar atau masuk ke sistem.

---

## 4.2 Panduan Registrasi dan Login

### 4.2.1 Mendaftar Sebagai Pemilik UMKM

**A. Deskripsi Fitur**  
Fitur registrasi memungkinkan pemilik usaha untuk membuat akun baru di sistem TUMBUH UMKM. Registrasi hanya tersedia untuk Pemilik UMKM. Petugas Desa dan Kepala Desa mendapatkan akun dari administrator sistem.

**B. Lokasi Menu**  
Halaman Landing → Tombol **"Daftar"** / URL: `/register`

**C. Tampilan Halaman**  
- Judul "Daftar"
- Sub-judul: *"Buat akun pelaku usaha. Nama usaha dicatat sekarang, data lainnya dilengkapi pada langkah berikutnya."*
- Formulir pendaftaran
- Tombol daftar dengan Google

**D. Langkah Penggunaan (Daftar dengan Email)**
1. Klik tombol **"Daftar"** di halaman landing.
2. Halaman pendaftaran akan tampil.
3. Isi formulir berikut:
   - **Nama Lengkap**: Masukkan nama lengkap Anda
   - **Email**: Masukkan alamat email aktif
   - **Nama Usaha**: Masukkan nama usaha Anda
   - **Kata Sandi**: Buat kata sandi minimal 8 karakter
4. Klik tombol **"Buat Akun"**.
5. Jika berhasil, Anda akan diarahkan ke halaman **Profil Usaha** untuk melengkapi data usaha.

**E. Langkah Penggunaan (Daftar dengan Google)**
1. Klik tombol **"Daftar dengan Google"**.
2. Pilih akun Google yang ingin digunakan.
3. Setujui izin akses yang diminta.
4. Jika berhasil, Anda akan diarahkan ke halaman **Profil Usaha** untuk melengkapi data usaha.

**F. Fungsi Komponen**
| Komponen | Fungsi |
|----------|--------|
| Field "Nama Lengkap" | Memasukkan nama pemilik akun |
| Field "Email" | Memasukkan alamat email untuk login |
| Field "Nama Usaha" | Memasukkan nama usaha yang akan didaftarkan |
| Field "Kata Sandi" | Membuat kata sandi akun (minimal 8 karakter) |
| Tombol "Buat Akun" | Memproses pendaftaran dengan data formulir |
| Tombol "Daftar dengan Google" | Mendaftar menggunakan akun Google |
| Link "Masuk" | Mengarahkan ke halaman login jika sudah punya akun |

**G. Hasil Penggunaan**  
Akun baru berhasil dibuat dan pengguna diarahkan ke halaman Profil Usaha untuk melengkapi data.

**H. Catatan Penting**
> [!IMPORTANT]
> - Email yang digunakan harus aktif dan belum terdaftar sebelumnya di sistem.
> - Kata sandi minimal 8 karakter.
> - Setelah mendaftar, Anda harus melengkapi data profil usaha terlebih dahulu sebelum dapat mengakses fitur lainnya.
> - Registrasi publik hanya tersedia untuk Pemilik UMKM.

---

### 4.2.2 Masuk ke Sistem (Login)

**A. Deskripsi Fitur**  
Fitur login memungkinkan pengguna yang sudah memiliki akun untuk mengakses sistem TUMBUH UMKM.

**B. Lokasi Menu**  
Halaman Landing → Tombol **"Masuk"** / URL: `/login`

**C. Tampilan Halaman**  
- Judul "Masuk"
- Sub-judul: *"Lanjutkan ke data usaha, verifikasi, atau pemantauan desa."*
- Formulir login (email/username dan kata sandi)
- Link "Lupa kata sandi?"
- Tombol masuk dengan Google

**D. Langkah Penggunaan (Login dengan Email)**
1. Klik tombol **"Masuk"** di halaman landing.
2. Halaman login akan tampil.
3. Masukkan **Email atau nama pengguna** di kolom pertama.
4. Masukkan **Kata Sandi** di kolom kedua.
5. Klik tombol **"Masuk"**.
6. Sistem akan mengarahkan Anda ke halaman beranda sesuai peran Anda:
   - **Pemilik UMKM**: Diarahkan ke Dashboard UMKM (atau Profil Usaha jika belum lengkap)
   - **Petugas Desa**: Diarahkan ke Dashboard Petugas
   - **Kepala Desa**: Diarahkan ke Dashboard Kepala Desa

**E. Langkah Penggunaan (Login dengan Google)**
1. Klik tombol **"Lanjutkan dengan Google"**.
2. Pilih akun Google yang terdaftar.
3. Sistem akan memverifikasi akun dan mengarahkan ke halaman beranda sesuai peran.

**F. Fungsi Komponen**
| Komponen | Fungsi |
|----------|--------|
| Field "Email atau nama pengguna" | Memasukkan email atau username akun |
| Field "Kata Sandi" | Memasukkan kata sandi akun |
| Link "Lupa kata sandi?" | Mengarahkan ke halaman pemulihan kata sandi |
| Tombol "Masuk" | Memproses login dengan email/kata sandi |
| Tombol "Lanjutkan dengan Google" | Login menggunakan akun Google |
| Link "Daftar sebagai pelaku usaha" | Mengarahkan ke halaman pendaftaran |

**G. Catatan Penting**
> [!WARNING]
> Jika akun Anda dinonaktifkan oleh administrator, Anda tidak akan dapat masuk ke sistem meskipun email dan kata sandi benar. Hubungi administrator untuk informasi lebih lanjut.

---

### 4.2.3 Lupa Kata Sandi

**A. Deskripsi Fitur**  
Fitur pemulihan kata sandi memungkinkan pengguna yang lupa kata sandinya untuk membuat kata sandi baru melalui email.

**B. Lokasi Menu**  
Halaman Login → Link **"Lupa kata sandi?"** / URL: `/forgot-password`

**C. Langkah Penggunaan**
1. Pada halaman login, klik link **"Lupa kata sandi?"**.
2. Halaman pemulihan kata sandi akan tampil.
3. Masukkan **Email** akun Anda.
4. Klik tombol **"Kirim tautan"**.
5. Periksa kotak masuk email Anda.
6. Klik tautan reset kata sandi yang diterima di email.
7. Halaman reset kata sandi akan tampil.
8. Masukkan **kata sandi baru** dan **konfirmasi kata sandi**.
9. Klik tombol untuk menyimpan kata sandi baru.
10. Login menggunakan kata sandi baru.

**D. Catatan Penting**
> [!NOTE]
> Tautan reset kata sandi yang dikirim melalui email memiliki masa berlaku terbatas. Jika tautan sudah kadaluarsa, ulangi proses dari langkah pertama.

---

### 4.2.4 Keluar dari Sistem (Logout)

**A. Deskripsi Fitur**  
Fitur logout memungkinkan pengguna untuk mengakhiri sesi aktif dan keluar dari sistem dengan aman.

**B. Lokasi Menu**  
Sidebar → Bagian bawah → Tombol **"Keluar"**

**C. Langkah Penggunaan**
1. Pada panel sidebar di sisi kiri, gulir ke bagian bawah.
2. Klik tombol **"Keluar"**.
3. Sesi Anda akan diakhiri dan Anda akan diarahkan ke halaman login.

> [!IMPORTANT]
> Selalu lakukan logout setelah selesai menggunakan sistem, terutama jika menggunakan perangkat yang digunakan bersama orang lain.

---

## 4.3 Panduan Pengguna UMKM

### 4.3.1 Profil Usaha

**A. Deskripsi Fitur**  
Halaman Profil Usaha adalah formulir utama untuk mendaftarkan dan memperbarui data identitas UMKM. Halaman ini **wajib** dilengkapi sebelum pengguna UMKM dapat mengakses fitur lainnya.

**B. Lokasi Menu**  
Sidebar → **USAHA** → **Profil Usaha** / URL: `/umkm/profil`

**C. Tampilan Halaman**  
- Judul "Profil UMKM" / "Pendaftaran & Pembaruan Data"
- Formulir data identitas usaha (satu bagian)
- Pesan peringatan jika profil belum lengkap atau perlu perbaikan

**D. Langkah Penggunaan**
1. Setelah login, klik menu **"Profil usaha"** di sidebar.
2. Formulir profil usaha akan tampil.
3. Isi seluruh kolom yang ditandai dengan tanda bintang merah (*):

   **Bagian Data Identitas Usaha:**
   - **Jenis Usaha** *(wajib)*: Pilih dari dropdown jenis usaha yang tersedia
   - **Nama Usaha** *(wajib)*: Masukkan nama usaha
   - **Nama Pemilik** *(wajib)*: Masukkan nama lengkap pemilik
   - **Nomor Telepon/WhatsApp** *(wajib)*: Format 08xxxxxxxxxx atau +62xxxxxxxxxx
   - **Email** *(opsional)*: Alamat email usaha
   - **Alamat** *(wajib)*: Alamat lengkap tempat usaha beroperasi
   - **Dusun** *(wajib)*: Nama dusun tempat usaha
   - **RT** *(opsional)*: Nomor RT
   - **RW** *(opsional)*: Nomor RW
   - **Tahun Berdiri** *(wajib)*: Klik untuk membuka pemilih tahun (1900 - tahun sekarang)
   - **Jumlah Pekerja** *(wajib)*: Angka 0 atau lebih
   - **Deskripsi Usaha** *(opsional)*: Cerita singkat tentang usaha

4. Klik tombol **"Simpan Profil Usaha"**.
5. Jika berhasil, Anda akan diarahkan ke halaman Dashboard UMKM.

**E. Fungsi Komponen**
| Komponen | Fungsi |
|----------|--------|
| Dropdown "Jenis Usaha" | Memilih kategori/jenis usaha dari daftar yang dikelola petugas |
| Field "Nama Usaha" | Memasukkan nama resmi usaha |
| Field "Nama Pemilik" | Memasukkan nama lengkap pemilik usaha |
| Field "Nomor Telepon/WhatsApp" | Memasukkan kontak yang bisa dihubungi |
| Field "Email" | Memasukkan email usaha (opsional) |
| Textarea "Alamat" | Memasukkan alamat lengkap usaha |
| Field "Dusun" | Memasukkan nama dusun |
| Field "RT" / "RW" | Memasukkan nomor RT dan RW (opsional) |
| Pemilih Tahun | Memilih tahun berdirinya usaha dari antarmuka kalender tahun |
| Field "Jumlah Pekerja" | Memasukkan jumlah tenaga kerja |
| Textarea "Deskripsi" | Memasukkan deskripsi singkat usaha (opsional) |
| Tombol "Simpan Profil Usaha" | Menyimpan data dan mengarahkan ke dashboard |

**F. Hasil Penggunaan**  
Data profil usaha tersimpan dengan status **"Menunggu Verifikasi"** dan pengguna diarahkan ke Dashboard UMKM.

**G. Catatan Penting**
> [!NOTE]
> - Data yang disimpan akan berstatus "Menunggu Verifikasi" hingga Petugas Desa melakukan verifikasi.
> - Jika Petugas Desa mengembalikan data dengan status "Perlu Perbaikan", pesan perbaikan akan ditampilkan di bagian atas halaman ini.
> - Setelah memperbaiki dan menyimpan ulang, status otomatis kembali ke "Menunggu Verifikasi".
> - Formulir ini dapat diisi ulang kapan saja untuk memperbarui data usaha.

---

### 4.3.2 Produk

**A. Deskripsi Fitur**  
Halaman Produk memungkinkan Pemilik UMKM untuk mencatat daftar produk atau jasa yang dijual oleh usaha mereka.

**B. Lokasi Menu**  
Sidebar → **USAHA** → **Produk** / URL: `/umkm/produk`

**C. Tampilan Halaman**  
- Judul halaman produk
- Tombol tambah produk
- Tabel/daftar produk yang sudah didaftarkan
- Status aktif/nonaktif setiap produk

**D. Langkah Penggunaan (Menambah Produk)**
1. Klik menu **"Produk"** di sidebar.
2. Klik tombol **"Tambah Produk"** atau ikon tambah.
3. Formulir tambah produk akan muncul (panel atau modal).
4. Isi data produk:
   - **Nama Produk** *(wajib)*
   - **Kategori Produk** *(wajib)*
   - **Harga** *(opsional)*: Harga satuan produk
   - **Deskripsi** *(opsional)*
5. Klik tombol **"Simpan"** untuk menyimpan produk.

**E. Langkah Penggunaan (Mengaktifkan/Menonaktifkan Produk)**
1. Pada daftar produk, temukan produk yang ingin diubah statusnya.
2. Klik tombol toggle/sakelar di kolom status.
3. Status produk akan berubah antara **Aktif** dan **Nonaktif**.

**F. Fungsi Komponen**
| Komponen | Fungsi |
|----------|--------|
| Tombol "Tambah Produk" | Membuka formulir penambahan produk baru |
| Field "Nama Produk" | Memasukkan nama produk/jasa |
| Field "Kategori" | Memilih atau memasukkan kategori produk |
| Field "Harga" | Memasukkan harga satuan (opsional) |
| Toggle Status | Mengaktifkan atau menonaktifkan produk |
| Tombol Edit | Mengubah data produk yang sudah ada |
| Tombol Simpan | Menyimpan data produk |

**G. Hasil Penggunaan**  
Daftar produk UMKM tersimpan dan dapat dilihat oleh Petugas Desa saat memeriksa detail UMKM.

---

### 4.3.3 Kebutuhan dan Kendala Usaha

**A. Deskripsi Fitur**  
Halaman Kebutuhan dan Kendala adalah formulir kuesioner asesmen yang mengidentifikasi kendala-kendala yang dihadapi UMKM dalam menjalankan usahanya. Data dari kuesioner ini digunakan oleh sistem untuk memberikan rekomendasi program bantuan yang sesuai.

**B. Lokasi Menu**  
Sidebar → **USAHA** → **Kebutuhan dan Kendala** / URL: `/umkm/kebutuhan`

**C. Tampilan Halaman**  
- Judul "Kebutuhan & Kendala Usaha"
- Sub-judul "Ceritakan Kesulitan Usahamu"
- Progress bar menunjukkan kemajuan pengisian
- Kartu pertanyaan yang berganti-ganti
- Tombol navigasi (Kembali/Lanjut)

**D. Langkah Penggunaan**

Kuesioner terdiri dari beberapa **fase** untuk setiap **kategori kendala** (misalnya: Modal, Pemasaran, Digitalisasi, Legalitas, Produksi):

**Fase 1 — Gate Screen (Pertanyaan Pembuka)**
1. Kartu pertanyaan akan menampilkan satu kategori kendala.
2. Baca pertanyaan: *"Apakah kamu mengalami kendala di bidang [kategori]?"*
3. Pilih salah satu:
   - Klik **"Tidak"** → Sistem melanjutkan ke kategori berikutnya secara otomatis
   - Klik **"Ya, ada"** → Sistem menampilkan pertanyaan detail untuk kategori tersebut

**Fase 2 — Detail Screen (Pertanyaan Detail)**
4. Jika memilih "Ya, ada", Anda akan melihat pertanyaan-pertanyaan detail.
5. Setiap pertanyaan memiliki dua tipe:
   - **Skala Likert**: Pilih angka 1-5 yang mewakili tingkat kendala (dari rendah ke tinggi)
   - **Pilihan Kalimat**: Pilih satu kalimat yang paling menggambarkan kondisi usaha Anda
6. Jawab **semua pertanyaan** dalam kategori ini.
7. Klik tombol **"Lanjut"** untuk melanjutkan ke kategori berikutnya.

**Fase 3 — Review Screen (Halaman Ringkasan)**
8. Setelah semua kategori selesai, halaman ringkasan akan tampil.
9. Anda dapat melihat ringkasan kategori yang diisi dan yang tidak.
10. *(Opsional)* Isi kolom "Ada kendala lain di luar bidang di atas?" jika ada kendala tambahan.
11. Klik tombol **"Kirim Jawaban"** untuk menyimpan kuesioner.
12. Setelah berhasil, Anda diarahkan ke Dashboard UMKM.

**E. Fungsi Komponen**
| Komponen | Fungsi |
|----------|--------|
| Progress Bar | Menunjukkan posisi kategori saat ini dari total kategori |
| Tombol "Tidak" | Melewati kategori (tandai tidak ada kendala) |
| Tombol "Ya, ada" | Membuka pertanyaan detail untuk kategori |
| Skala Lingkaran (1-5) | Memilih tingkat kendala pada pertanyaan tipe Likert |
| Tombol Pilihan Kalimat | Memilih jawaban pada pertanyaan tipe pilihan |
| Tombol "Kembali" | Kembali ke pertanyaan sebelumnya |
| Tombol "Lanjut" | Melanjutkan ke kategori berikutnya |
| Textarea "Kendala Lain" | Memasukkan kendala di luar kategori yang tersedia |
| Tombol "Kirim Jawaban" | Menyimpan seluruh jawaban kuesioner |

**F. Hasil Penggunaan**  
Jawaban kuesioner tersimpan dan sistem secara otomatis menghitung skor kendala per kategori. Rekomendasi program bantuan yang sesuai akan muncul di Dashboard UMKM dan halaman Rekomendasi.

**G. Catatan Penting**
> [!IMPORTANT]
> - Kuesioner harus diisi dengan jujur agar rekomendasi yang diberikan sesuai dengan kondisi sebenarnya.
> - Minimal harus ada **satu bidang** yang dijawab "Ya, ada" untuk dapat mengirim kuesioner.
> - Semua pertanyaan dalam satu bidang harus dijawab sebelum bisa melanjutkan.
> - Jika kuesioner sudah diisi sebelumnya dan Anda mengisi ulang, data lama akan ditimpa.

---

### 4.3.4 Dashboard UMKM

**A. Deskripsi Fitur**  
Dashboard UMKM adalah halaman utama yang memberikan ringkasan kondisi dan status usaha Anda secara sekilas.

**B. Lokasi Menu**  
Sidebar → **UTAMA** → **Dashboard** / URL: `/umkm/dashboard`

**C. Tampilan Halaman**  
- Banner selamat datang dengan nama pemilik dan tanggal
- Dua tombol aksi cepat: "Cek Status Verifikasi" dan "Lihat Rekomendasi Program"
- Tiga kartu ringkasan: Status Verifikasi, Kendala Utama, Program yang Cocok
- Kartu detail status verifikasi
- Grid kartu rekomendasi program

**D. Informasi yang Ditampilkan**

| Informasi | Keterangan |
|-----------|-----------|
| **Tanggal Hari Ini** | Ditampilkan dalam format hari, tanggal, bulan, tahun |
| **Sapaan Personal** | "Selamat datang, [Nama Pemilik]!" |
| **Status Verifikasi** | Menunggu Cek / Terverifikasi / Perlu Diperbaiki |
| **Kendala Utama** | Kategori kendala dengan skor tertinggi dari kuesioner |
| **Program yang Cocok** | Jumlah program bantuan yang direkomendasikan |
| **Rekomendasi Program** | Daftar kartu program bantuan yang sesuai |

**E. Status Verifikasi**

| Status | Warna | Keterangan |
|--------|-------|-----------|
| **Menunggu Cek** | Kuning | Data usaha sudah dikirim dan menunggu ditinjau petugas |
| **Terverifikasi** | Hijau | Data usaha sudah lengkap dan valid |
| **Perlu Diperbaiki** | Merah | Petugas meminta perbaikan data |

**F. Fungsi Komponen**
| Komponen | Fungsi |
|----------|--------|
| Tombol "Cek Status Verifikasi" | Scroll otomatis ke kartu status verifikasi |
| Tombol "Lihat Rekomendasi Program" | Scroll otomatis ke seksi rekomendasi |
| Kartu "Status Verifikasi" | Menampilkan status dan catatan dari petugas |
| Kartu "Kendala Utama" | Menampilkan kategori kendala paling dominan |
| Kartu "Program yang Cocok" | Menampilkan jumlah program yang sesuai |
| Tombol "Lihat detail" (pada program) | Membuka halaman detail program bantuan |
| Link "Buka kebutuhan dan kendala" | Mengarahkan ke halaman kuesioner jika belum diisi |

**G. Catatan Penting**
> [!NOTE]
> - Jika belum mengisi kuesioner kebutuhan dan kendala, bagian "Kendala Utama" dan "Program yang Cocok" akan menampilkan pesan "Belum ada" dengan panduan untuk mengisi kuesioner.
> - Rekomendasi program muncul otomatis berdasarkan hasil kuesioner tanpa perlu tindakan tambahan.

---

### 4.3.5 Rekomendasi Program

**A. Deskripsi Fitur**  
Halaman Rekomendasi Program menampilkan daftar program bantuan yang direkomendasikan oleh sistem berdasarkan hasil kuesioner kebutuhan dan kendala usaha.

**B. Lokasi Menu**  
Sidebar → **HASIL** → **Rekomendasi Program** / URL: `/umkm/rekomendasi`

**C. Tampilan Halaman**  
- Judul "Rekomendasi program"
- Deskripsi singkat tentang cara program ini dipilih
- Tabel daftar program rekomendasi

**D. Informasi dalam Tabel**

| Kolom | Keterangan |
|-------|-----------|
| **Program** | Nama program dan nama penyelenggara |
| **Kategori Kendala** | Kategori kendala yang ditangani program |
| **Minat** | Status pendaftaran minat (Sudah/Belum didaftarkan) |
| **Aksi** | Tombol "Lihat detail" dan "Daftarkan minat" |

**E. Langkah Penggunaan (Mendaftarkan Minat)**
1. Buka halaman **Rekomendasi Program**.
2. Pilih program yang ingin diikuti.
3. Klik tombol **"Lihat detail"** untuk membaca informasi lengkap program.
4. Jika berminat, klik tombol **"Daftarkan minat"** pada baris program tersebut.
5. Konfirmasi akan muncul; klik untuk menyetujui.
6. Status berubah menjadi **"Sudah didaftarkan"** (badge hijau).
7. Petugas desa akan menerima notifikasi dan dapat menindaklanjuti pendaftaran minat Anda.

**F. Catatan Penting**
> [!NOTE]
> - Mendaftarkan minat tidak berarti Anda langsung mengikuti program. Petugas Desa akan menindaklanjuti dan menjadwalkan pembinaan.
> - Tombol "Daftarkan minat" hanya muncul untuk program yang belum didaftarkan.
> - Jika tabel kosong, berarti belum ada program yang cocok dengan kendala usaha Anda atau Anda belum mengisi kuesioner kebutuhan.

---

### 4.3.6 Riwayat Pembinaan (UMKM)

**A. Deskripsi Fitur**  
Halaman Riwayat Pembinaan menampilkan catatan sesi pembinaan yang telah dilakukan untuk UMKM Anda.

**B. Lokasi Menu**  
Sidebar → **HASIL** → **Riwayat Pembinaan** / URL: `/umkm/riwayat-pembinaan`

**C. Tampilan Halaman**  
- Judul halaman riwayat pembinaan
- Daftar/tabel sesi pembinaan yang telah dilakukan
- Informasi program, judul sesi, tanggal, dan hasil pembinaan

**D. Informasi yang Ditampilkan**

| Kolom | Keterangan |
|-------|-----------|
| **Program Pembinaan** | Nama program bantuan yang terkait |
| **Judul Sesi** | Judul kegiatan pembinaan |
| **Tanggal Pelaksanaan** | Tanggal dan waktu sesi pembinaan |
| **Hasil Pembinaan** | Catatan hasil atau pencapaian sesi |

**E. Catatan Penting**
> [!NOTE]
> Halaman ini hanya menampilkan; Anda tidak dapat mengubah atau menambah data riwayat pembinaan dari halaman ini. Data diisi oleh Petugas Desa setelah sesi pembinaan selesai.

---

### 4.3.7 Profil Akun (UMKM)

**A. Deskripsi Fitur**  
Halaman Profil Akun memungkinkan pengguna untuk memperbarui informasi pribadi akun dan mengubah kata sandi.

**B. Lokasi Menu**  
Sidebar → **AKUN** → **Profil Akun** / URL: `/akun`

**C. Tampilan Halaman**  
- Panel sisi kiri: avatar inisial, peran, nama, dan email
- Panel formulir kanan: data akun dan kata sandi

**D. Langkah Penggunaan (Memperbarui Data Akun)**
1. Klik menu **"Profil Akun"** di sidebar.
2. Di panel formulir, ubah data yang perlu diperbarui:
   - **Nama**: Nama lengkap pengguna
   - **Email**: Alamat email akun
   - **Nomor HP**: Nomor telepon pengguna
3. Klik tombol **"Simpan perubahan"**.

**E. Langkah Penggunaan (Mengubah Kata Sandi)**
1. Di halaman Profil Akun, gulir ke bagian **"Ganti kata sandi"**.
2. Jika sudah memiliki kata sandi, isi **"Kata sandi saat ini"**.
3. Isi **"Kata sandi baru"** (minimal 8 karakter).
4. Isi **"Ulangi kata sandi baru"** dengan kata sandi yang sama.
5. Klik tombol **"Simpan perubahan"**.

**F. Catatan Penting**
> [!NOTE]
> - Jika akun didaftarkan melalui Google dan belum memiliki kata sandi, bagian ini bertuliskan "Buat kata sandi".
> - Peran akun (Pemilik UMKM, Petugas Desa, Kepala Desa) tidak dapat diubah dari halaman ini.

---

## 4.4 Panduan Petugas Desa

### 4.4.1 Dashboard Petugas

**A. Deskripsi Fitur**  
Dashboard adalah halaman utama untuk Petugas Desa yang menampilkan statistik dan kondisi UMKM secara keseluruhan.

**B. Lokasi Menu**  
Sidebar → **UTAMA** → **Dashboard** / URL: `/dashboard`

**C. Informasi yang Ditampilkan**  
Dashboard Petugas menampilkan berbagai statistik kondisi UMKM desa, termasuk:
- Jumlah total UMKM terdaftar
- Distribusi status verifikasi
- Distribusi kendala UMKM
- Perkembangan program dan pembinaan
- Grafik dan visualisasi data

**D. Catatan Penting**
> [!NOTE]
> Dashboard Petugas dan Dashboard Kepala Desa menggunakan tampilan yang sama (berbagi halaman `/dashboard`), namun menu navigasi di sidebar berbeda sesuai peran.

---

### 4.4.2 Data UMKM (Verifikasi)

**A. Deskripsi Fitur**  
Halaman Data UMKM menampilkan daftar seluruh UMKM yang terdaftar di sistem beserta status verifikasinya. Petugas dapat mencari, memfilter, dan melihat detail setiap UMKM untuk melakukan verifikasi data.

**B. Lokasi Menu**  
Sidebar → **PENGELOLAAN** → **Data UMKM** / URL: `/petugas/umkm`

**C. Tampilan Halaman**  
- Judul "Kelola Data UMKM"
- 4 kartu statistik: Total UMKM, Menunggu Verifikasi, Terverifikasi, Perlu Perbaikan
- Kotak pencarian
- Filter status (Semua / Menunggu Verifikasi / Terverifikasi / Perlu Perbaikan)
- Tabel daftar UMKM

**D. Kolom Tabel**

| Kolom | Keterangan |
|-------|-----------|
| No. | Nomor urut |
| Nama Usaha & Pemilik | Nama usaha dan nama pemilik |
| Kategori / Jenis Usaha | Jenis usaha yang dipilih |
| Kontak | Nomor telepon |
| Tanggal Masuk | Tanggal pendaftaran |
| Status Verifikasi | Menunggu / Terverifikasi / Perlu Perbaikan |
| Aksi | Tombol "Detail" |

**E. Langkah Penggunaan (Mencari UMKM)**
1. Buka halaman **Data UMKM**.
2. Ketikkan nama usaha, nama pemilik, atau kontak di kotak pencarian.
3. Daftar akan otomatis difilter sesuai kata kunci.

**F. Langkah Penggunaan (Memfilter Berdasarkan Status)**
1. Buka halaman **Data UMKM**.
2. Klik salah satu tombol filter status di bawah kotak pencarian:
   - **Semua**: Menampilkan semua UMKM
   - **Menunggu Verifikasi**: Menampilkan UMKM yang menunggu diverifikasi
   - **Terverifikasi**: Menampilkan UMKM yang sudah diverifikasi
   - **Perlu Perbaikan**: Menampilkan UMKM yang diminta memperbaiki data

---

### 4.4.3 Detail dan Verifikasi UMKM

**A. Deskripsi Fitur**  
Halaman Detail UMKM menampilkan informasi lengkap satu UMKM dan memberikan formulir keputusan verifikasi kepada Petugas Desa.

**B. Lokasi Menu**  
Data UMKM → Tombol **"Detail"** / URL: `/petugas/umkm/{id}`

**C. Tampilan Halaman**  
- Header dengan nama usaha, pemilik, kategori, tanggal masuk, dan badge status
- Kartu "Profil Usaha & Pemilik" (data identitas lengkap)
- Kartu "Data Produk & Usaha" (kategori, deskripsi, daftar produk)
- Kartu "Kebutuhan & Kendala Usaha" (hasil kuesioner, skor per kategori)
- Kartu "Catatan Verifikasi Petugas" (jika ada catatan sebelumnya)
- Formulir "Keputusan Verifikasi"

**D. Langkah Penggunaan (Melakukan Verifikasi)**
1. Buka halaman **Data UMKM**.
2. Klik tombol **"Detail"** pada UMKM yang ingin diverifikasi.
3. Periksa seluruh data yang ditampilkan:
   - Profil usaha dan pemilik
   - Data produk dan deskripsi usaha
   - Hasil kuesioner kebutuhan dan kendala (jika sudah diisi)
4. Gulir ke bagian **"Formulir Keputusan Verifikasi"**.
5. Pilih keputusan verifikasi:
   - **Terverifikasi**: Jika data valid dan disetujui
   - **Perlu Perbaikan**: Jika ada data yang perlu diperbaiki oleh pemilik
6. Isi **Catatan Verifikasi**:
   - Jika memilih **"Terverifikasi"**: Catatan bersifat opsional
   - Jika memilih **"Perlu Perbaikan"**: Catatan **wajib** diisi (minimal 5 karakter) agar pemilik tahu apa yang harus diperbaiki
7. Klik tombol **"Simpan Keputusan Verifikasi"**.
8. Pesan notifikasi akan tampil di bagian atas halaman.

**E. Fungsi Komponen**
| Komponen | Fungsi |
|----------|--------|
| Badge Status | Menampilkan status verifikasi saat ini |
| Kartu Profil Usaha | Menampilkan data identitas lengkap UMKM |
| Kartu Data Produk | Menampilkan kategori usaha, deskripsi, dan daftar produk |
| Kartu Kebutuhan & Kendala | Menampilkan hasil kuesioner dengan skor per kategori (bar progress) |
| Radio "Terverifikasi" | Memilih status verifikasi sebagai valid |
| Radio "Perlu Perbaikan" | Memilih status verifikasi yang meminta perbaikan |
| Textarea Catatan | Memasukkan catatan untuk pemilik UMKM |
| Tombol "Simpan Keputusan" | Menyimpan keputusan verifikasi |

**F. Hasil Penggunaan**
- Jika **Terverifikasi**: Status UMKM berubah menjadi "Terverifikasi" dan pemilik mendapatkan notifikasi.
- Jika **Perlu Perbaikan**: Status berubah menjadi "Perlu Perbaikan" dan pemilik melihat catatan perbaikan di halaman profil usahanya.

---

### 4.4.4 Pendataan UMKM (oleh Petugas)

**A. Deskripsi Fitur**  
Halaman Pendataan UMKM memungkinkan Petugas Desa untuk mendaftarkan data UMKM yang pemiliknya belum memiliki akun sendiri. Formulir yang digunakan sama persis dengan formulir profil usaha milik pemilik.

**B. Lokasi Menu**  
Sidebar → **PENGELOLAAN** → **Pendataan UMKM** / URL: `/petugas/umkm/pendataan-umkm`

**C. Langkah Penggunaan**
1. Klik menu **"Pendataan UMKM"** di sidebar.
2. Formulir data usaha akan tampil (sama dengan formulir profil UMKM).
3. Isi seluruh data usaha UMKM yang akan didaftarkan.
4. Klik tombol **"Simpan Data UMKM"**.
5. Jika berhasil, pesan sukses tampil dan formulir dikosongkan secara otomatis (siap untuk input UMKM berikutnya).

**F. Catatan Penting**
> [!NOTE]
> - Berbeda dengan formulir profil oleh pemilik (yang diarahkan ke dashboard setelah simpan), formulir pendataan oleh petugas **dikosongkan otomatis** setelah simpan agar petugas dapat langsung menginput data UMKM berikutnya.
> - UMKM yang didatakan oleh petugas dapat kemudian membuat akun sendiri untuk mengelola datanya.

---

### 4.4.5 Jenis Usaha

**A. Deskripsi Fitur**  
Halaman Jenis Usaha memungkinkan Petugas Desa untuk mengelola daftar kategori/jenis usaha yang tersedia sebagai pilihan saat mendaftarkan profil UMKM.

**B. Lokasi Menu**  
Sidebar → **PENGELOLAAN** → **Jenis Usaha** / URL: `/petugas/jenis-usaha`

**C. Langkah Penggunaan (Menambah Jenis Usaha)**
1. Klik menu **"Jenis Usaha"** di sidebar.
2. Halaman pengelolaan jenis usaha akan tampil.
3. Klik tombol **"Tambah"** atau gunakan formulir yang tersedia.
4. Masukkan **nama jenis usaha**.
5. Klik **"Simpan"**.

**D. Langkah Penggunaan (Mengaktifkan/Menonaktifkan Jenis Usaha)**
1. Temukan jenis usaha yang ingin diubah.
2. Klik tombol toggle/switch untuk mengaktifkan atau menonaktifkan.
3. Jenis usaha yang nonaktif tidak akan muncul sebagai pilihan di formulir profil UMKM.

**E. Langkah Penggunaan (Menghapus Jenis Usaha)**
1. Temukan jenis usaha yang ingin dihapus.
2. Klik tombol hapus (ikon tempat sampah).
3. Konfirmasi penghapusan.

---

### 4.4.6 Kategori Kendala

**A. Deskripsi Fitur**  
Halaman Kategori Kendala digunakan untuk mengelola daftar kategori kendala usaha yang akan menjadi basis penilaian kuesioner dan pengelompokan UMKM.

**B. Lokasi Menu**  
Sidebar → **PENGELOLAAN** → **Kategori Kendala** / URL: `/petugas/kategori-kendala`

**C. Langkah Penggunaan**
1. Klik menu **"Kategori Kendala"** di sidebar.
2. Daftar kategori kendala yang sudah ada akan tampil.
3. **Tambah kategori**: Klik tombol tambah, isi nama dan deskripsi kategori, simpan.
4. **Edit kategori**: Klik tombol edit pada baris kategori, ubah data, simpan.
5. **Toggle aktif/nonaktif**: Klik switch untuk mengaktifkan atau menonaktifkan kategori.
6. **Hapus kategori**: Klik tombol hapus, konfirmasi penghapusan.

**D. Catatan Penting**
> [!WARNING]
> Menghapus atau menonaktifkan kategori kendala yang sudah terkait dengan pertanyaan bank soal atau data asesmen dapat memengaruhi hasil penilaian UMKM. Lakukan perubahan dengan hati-hati.

---

### 4.4.7 Bank Soal

**A. Deskripsi Fitur**  
Halaman Bank Soal digunakan untuk mengelola pertanyaan-pertanyaan yang akan ditampilkan dalam kuesioner kebutuhan dan kendala UMKM.

**B. Lokasi Menu**  
Sidebar → **PENGELOLAAN** → **Bank Soal** / URL: `/petugas/bank-soal`

**C. Struktur Bank Soal**
Setiap pertanyaan (soal) terdiri dari:
- **Kategori Kendala**: Kategori yang terkait dengan pertanyaan
- **Teks Pertanyaan (Prompt)**: Pertanyaan yang ditampilkan ke pengguna
- **Teks Bantuan (Help Text)**: Penjelasan tambahan opsional
- **Tipe Pertanyaan**: Likert (skala angka) atau Pilihan Kalimat
- **Opsi Jawaban**: Pilihan jawaban beserta nilainya (skor/bobot)

**D. Langkah Penggunaan (Menambah Pertanyaan)**
1. Klik menu **"Bank Soal"** di sidebar.
2. Daftar pertanyaan yang sudah ada akan tampil.
3. Klik tombol **"Tambah Pertanyaan"**.
4. Isi data pertanyaan (kategori, teks, tipe, dll.).
5. Tambahkan opsi jawaban beserta skor masing-masing.
6. Klik **"Simpan"**.

**E. Catatan Penting**
> [!IMPORTANT]
> - Pertanyaan yang dinonaktifkan tidak akan muncul dalam kuesioner UMKM.
> - Perubahan pada bank soal akan memengaruhi kuesioner yang diisi oleh UMKM setelah perubahan dilakukan. Kuesioner yang sudah diisi sebelumnya tidak terpengaruh.

---

### 4.4.8 Program Bantuan

**A. Deskripsi Fitur**  
Halaman Program Bantuan digunakan untuk mengelola data program-program bantuan yang dapat direkomendasikan kepada UMKM berdasarkan kendala yang teridentifikasi.

**B. Lokasi Menu**  
Sidebar → **PENGELOLAAN** → **Program Bantuan** / URL: `/petugas/program-bantuan`

**C. Tampilan Halaman**  
- 4 kartu statistik: Total Program, Program Aktif, Draf, Ditutup/Arsip
- Filter: Pencarian nama/penyelenggara, Status, Kategori Kendala
- Tabel daftar program

**D. Kolom Tabel**

| Kolom | Keterangan |
|-------|-----------|
| No. | Nomor urut |
| Program & Penyelenggara | Nama program, penyelenggara, dan tautan (jika ada) |
| Kategori Kendala Terkait | Kategori kendala yang ditangani beserta tingkat minimum |
| Kuota | Jumlah peserta (atau "Tak terbatas") |
| Periode | Tanggal mulai dan akhir program |
| Status | Aktif / Draf / Ditutup / Diarsipkan |
| Aksi | Tombol lihat detail |

**E. Status Program**

| Status | Keterangan |
|--------|-----------|
| **Aktif** | Program berjalan dan siap direkomendasikan ke UMKM |
| **Draf** | Program belum dipublikasikan |
| **Ditutup** | Program sudah selesai |
| **Diarsipkan** | Program diarsipkan |

**F. Langkah Penggunaan (Menambah Program Baru)**
1. Klik tombol **"Tambah Program"**.
2. Halaman formulir tambah program akan tampil.
3. Isi data program:
   - **Nama Program** *(wajib)*
   - **Penyelenggara** *(wajib)*: Nama lembaga yang menyelenggarakan
   - **Deskripsi**: Penjelasan program
   - **Status**: Pilih status (Aktif/Draf/Ditutup/Diarsipkan)
   - **Kategori Kendala Terkait**: Pilih kategori kendala dan tingkat minimum yang disyaratkan
   - **Kuota**: Jumlah peserta (kosongkan jika tidak terbatas)
   - **Periode**: Tanggal mulai dan akhir (opsional)
   - **URL**: Tautan informasi program (opsional)
4. Klik **"Simpan"**.

**G. Langkah Penggunaan (Melihat dan Mengedit Program)**
1. Klik ikon lihat detail (mata) pada baris program.
2. Halaman detail program akan tampil.
3. Klik tombol **"Edit"** untuk mengubah data program.
4. Lakukan perubahan dan klik **"Simpan"**.

---

### 4.4.9 Ajukan Tindak Lanjut

**A. Deskripsi Fitur**  
Halaman Ajukan Tindak Lanjut digunakan oleh Petugas Desa untuk membuat rencana tindak lanjut pembinaan bagi UMKM yang sudah mendaftarkan minat pada program bantuan. Rencana ini kemudian diajukan kepada Kepala Desa untuk mendapatkan persetujuan.

**B. Lokasi Menu**  
Sidebar → **TINDAK LANJUT** → **Ajukan Tindak Lanjut** / URL: `/petugas/tindak-lanjut`

**C. Tampilan Halaman**  
- Daftar UMKM yang mendaftarkan minat pada program (antrian/queue)
- Formulir pembuatan rencana tindak lanjut

**D. Langkah Penggunaan**
1. Klik menu **"Ajukan Tindak Lanjut"** di sidebar.
2. Daftar UMKM dengan minat program akan tampil.
3. Pilih UMKM yang akan dibuat rencana tindak lanjutnya.
4. Isi formulir rencana tindak lanjut:
   - **Rencana Kegiatan**: Deskripsi kegiatan pembinaan yang direncanakan
   - **Tanggal Rencana**: Tanggal kegiatan yang direncanakan
5. Klik **"Ajukan"** untuk mengirimkan rencana kepada Kepala Desa.
6. Status rencana berubah menjadi **"Menunggu Persetujuan"**.

---

### 4.4.10 Riwayat Pembinaan / Catat Pembinaan

**A. Deskripsi Fitur**  
Halaman Catat Pembinaan digunakan untuk mencatat sesi pembinaan yang telah selesai dilaksanakan. Halaman ini menampilkan rencana tindak lanjut yang sudah disetujui Kepala Desa dan formulir untuk mencatat hasilnya.

**B. Lokasi Menu**  
Sidebar → **TINDAK LANJUT** → **Riwayat Pembinaan** / URL: `/petugas/catat-pembinaan`

**C. Tampilan Halaman**  
- Daftar rencana tindak lanjut yang sudah disetujui (antrian pembinaan)
- Riwayat sesi pembinaan yang sudah dicatat

**D. Langkah Penggunaan (Mencatat Sesi Pembinaan)**
1. Klik menu **"Riwayat Pembinaan"** di sidebar (bagian Tindak Lanjut).
2. Daftar rencana yang disetujui akan tampil.
3. Pilih rencana yang ingin dicatat hasil pembinaannya.
4. Isi formulir sesi pembinaan:
   - **Judul Sesi**: Nama/judul kegiatan pembinaan
   - **Tanggal Pelaksanaan**: Tanggal sesi dilaksanakan
   - **Hasil Pembinaan**: Catatan hasil dan pencapaian sesi
5. Klik **"Simpan"** untuk menyimpan catatan sesi.

---

### 4.4.11 Pengelompokan UMKM (Petugas)

**A. Deskripsi Fitur**  
Halaman Pengelompokan menampilkan daftar UMKM yang dikelompokkan berdasarkan kategori kendala utama mereka. Data ini membantu Petugas memahami distribusi permasalahan UMKM.

**B. Lokasi Menu**  
Sidebar → **ANALISIS** → **Pengelompokan** / URL: `/petugas/pengelompokan`

**C. Tampilan Halaman**  
- Judul "Pengelompokan"
- Filter berdasarkan kategori kendala
- Tabel UMKM dengan kolom: Usaha, Pemilik, Kendala Utama, Tingkat

**D. Langkah Penggunaan**
1. Klik menu **"Pengelompokan"** di sidebar.
2. Tabel pengelompokan UMKM akan tampil.
3. Gunakan dropdown **"Filter Kategori"** untuk menyaring berdasarkan kategori kendala tertentu.
4. Klik tombol **"Filter"** untuk menerapkan filter.

---

### 4.4.12 Rekomendasi (Pantau oleh Petugas)

**A. Deskripsi Fitur**  
Halaman Rekomendasi (versi petugas) menampilkan hasil pencocokan otomatis antara kendala UMKM dengan program bantuan yang tersedia.

**B. Lokasi Menu**  
Sidebar → **ANALISIS** → **Rekomendasi** / URL: `/petugas/rekomendasi`

---

### 4.4.13 Laporan

**A. Deskripsi Fitur**  
Halaman Laporan menampilkan ringkasan komprehensif kondisi UMKM desa, termasuk statistik pendataan, distribusi kendala, dan perkembangan pembinaan. Laporan dapat difilter dan diekspor.

**B. Lokasi Menu**  
Sidebar → **ANALISIS** → **Laporan** / URL: `/petugas/laporan`

**C. Tampilan Halaman**  
- Judul "Laporan Ringkasan UMKM"
- Tombol ekspor PDF dan Excel
- Panel filter (tanggal mulai, tanggal akhir, kategori kendala)
- Seksi "Ringkasan Kondisi UMKM" (5 kartu statistik + tabel detail)
- Seksi "Sebaran Kendala Berdasarkan Kategori" (tabel distribusi)
- Seksi "Perkembangan Pembinaan UMKM" (4 kartu + tabel tindak lanjut + riwayat pembinaan)

**D. Langkah Penggunaan (Menerapkan Filter)**
1. Buka halaman **Laporan**.
2. Di panel **Filter Laporan**, isi:
   - **Tanggal Mulai**: Tanggal awal periode laporan
   - **Tanggal Akhir**: Tanggal akhir periode laporan
   - **Kategori Kendala**: Pilih kategori spesifik atau biarkan "Semua kategori"
3. Klik tombol **"Terapkan Filter"**.
4. Laporan diperbarui sesuai filter yang dipilih.
5. Klik **"Reset Filter"** untuk menghapus filter dan menampilkan semua data.

**E. Langkah Penggunaan (Mengekspor Laporan)**
1. Terapkan filter jika diperlukan.
2. Klik tombol **"Ekspor PDF"** untuk mengunduh laporan dalam format PDF.
3. Klik tombol **"Ekspor Excel"** untuk mengunduh laporan dalam format Excel.
4. File akan otomatis terunduh ke perangkat Anda.

**F. Informasi dalam Laporan**

**Ringkasan Kondisi UMKM:**
| Metrik | Keterangan |
|--------|-----------|
| Total UMKM Terdaftar | Jumlah seluruh UMKM (dengan rincian status verifikasi) |
| UMKM Sudah Dinilai | UMKM yang sudah mengisi kuesioner |
| Rata-rata Skor Kendala | Rata-rata skor dari semua penilaian (skala 0-100) |
| Kendala Tinggi | Jumlah UMKM dengan tingkat kendala tinggi |
| Kendala Sedang & Rendah | Jumlah UMKM dengan tingkat kendala sedang dan rendah |

**Sebaran Kendala per Kategori:**
Tabel menampilkan setiap kategori kendala dengan jumlah UMKM yang dinilai, berapa yang menjadi kendala utama, dan distribusi tingkat (tinggi/sedang/rendah).

**Perkembangan Pembinaan:**
Mencakup statistik rekomendasi aktif, tindak lanjut, status persetujuan, dan sesi pembinaan selesai.

---

## 4.5 Panduan Kepala Desa

### 4.5.1 Dashboard Kepala Desa

**A. Deskripsi Fitur**  
Dashboard memberikan gambaran menyeluruh tentang kondisi UMKM desa bagi Kepala Desa.

**B. Lokasi Menu**  
Sidebar → **UTAMA** → **Dashboard** / URL: `/dashboard`

**C. Tampilan dan Informasi**  
Sama dengan Dashboard Petugas — menampilkan statistik dan visualisasi kondisi UMKM, namun dengan menu navigasi sidebar yang berbeda (sesuai hak akses Kepala Desa).

---

### 4.5.2 Pengelompokan UMKM (Kepala Desa)

**A. Deskripsi Fitur**  
Sama dengan halaman Pengelompokan milik Petugas — menampilkan pengelompokan UMKM berdasarkan kendala utama.

**B. Lokasi Menu**  
Sidebar → **PEMANTAUAN** → **Pengelompokan** / URL: `/pimpinan/pengelompokan`

---

### 4.5.3 Rekomendasi (Kepala Desa)

**A. Deskripsi Fitur**  
Memantau distribusi rekomendasi program yang diberikan kepada UMKM.

**B. Lokasi Menu**  
Sidebar → **PEMANTAUAN** → **Rekomendasi** / URL: `/pimpinan/rekomendasi`

---

### 4.5.4 Laporan (Kepala Desa)

**A. Deskripsi Fitur**  
Sama dengan halaman Laporan milik Petugas — menampilkan laporan ringkasan UMKM yang dapat difilter dan diekspor PDF/Excel.

**B. Lokasi Menu**  
Sidebar → **PEMANTAUAN** → **Laporan** / URL: `/pimpinan/laporan`

**C. Langkah Penggunaan**  
Langkah penggunaan identik dengan panduan Laporan untuk Petugas Desa (lihat [4.4.13 Laporan](#4413-laporan)).

---

### 4.5.5 Persetujuan Tindak Lanjut

**A. Deskripsi Fitur**  
Halaman Persetujuan Tindak Lanjut memungkinkan Kepala Desa untuk menyetujui atau menolak rencana tindak lanjut pembinaan yang diajukan oleh Petugas Desa.

**B. Lokasi Menu**  
Sidebar → **KEPUTUSAN** → **Persetujuan Tindak Lanjut** / URL: `/pimpinan/persetujuan-tindak-lanjut`

**C. Tampilan Halaman**  
- Daftar rencana tindak lanjut yang menunggu persetujuan
- Detail setiap rencana: nama usaha, pemilik, program, rencana kegiatan, tanggal
- Tombol aksi (Setujui/Tolak)

**D. Langkah Penggunaan (Menyetujui Tindak Lanjut)**
1. Buka halaman **Persetujuan Tindak Lanjut**.
2. Tinjau rencana tindak lanjut yang diajukan petugas.
3. Klik tombol **"Setujui"** pada rencana yang disetujui.
4. Status rencana berubah menjadi **"Disetujui"** dan Petugas dapat mulai melaksanakan pembinaan.

**E. Langkah Penggunaan (Menolak Tindak Lanjut)**
1. Buka halaman **Persetujuan Tindak Lanjut**.
2. Tinjau rencana yang ingin ditolak.
3. Klik tombol **"Tolak"**.
4. *(Jika tersedia)* Berikan alasan penolakan.
5. Status rencana berubah menjadi **"Ditolak"** dan Petugas mendapat notifikasi.

---

### 4.5.6 Riwayat Pembinaan (Kepala Desa)

**A. Deskripsi Fitur**  
Halaman Riwayat Pembinaan memungkinkan Kepala Desa untuk memantau seluruh sesi pembinaan UMKM yang telah dilaksanakan oleh Petugas Desa.

**B. Lokasi Menu**  
Sidebar → **KEPUTUSAN** → **Riwayat Pembinaan** / URL: `/pimpinan/riwayat-pembinaan`

**C. Tampilan Halaman**  
- Daftar riwayat seluruh sesi pembinaan
- Informasi: nama usaha, pemilik, program, judul sesi, tanggal, dan hasil pembinaan

---

### 4.5.7 Profil Akun (Kepala Desa)

Langkah penggunaan identik dengan panduan Profil Akun (lihat [4.3.7 Profil Akun](#437-profil-akun-umkm)).

---

[← BAB III](./03_Persiapan_Penggunaan.md) | [Lanjut: BAB V →](./05_Alur_Penggunaan.md)
