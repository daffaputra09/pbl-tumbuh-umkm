# Spesifikasi Kebutuhan Perangkat Lunak TUMBUH UMKM

| Item | Keterangan |
| --- | --- |
| Nama sistem | TUMBUH UMKM |
| Jenis | Sistem informasi berbasis web untuk pendataan, pemetaan kebutuhan, dan pembinaan UMKM desa |
| Studi kasus | Kampung Rejoso, Desa Junrejo, Kecamatan Junrejo, Kota Batu |
| Pengguna | Pemilik UMKM, Petugas Desa, Kepala Desa |
| Tim | Daffa Putra Prasetya, Hernanda Rizka Utami, Nazwa Azahra Audina, Radith Ferdian Hibawan |
| Teknologi | Laravel, Blade, Tailwind CSS, PostgreSQL |

# BAB 1 PENDAHULUAN

## 1.1 Latar Belakang

Usaha Mikro, Kecil, dan Menengah (UMKM) merupakan salah satu penggerak utama perekonomian di tingkat desa. Banyak desa masih mengelola data dan menyampaikan informasi kepada masyarakat secara manual, sehingga potensi sumber daya setempat belum terdokumentasi dengan baik [1]. Kondisi serupa terjadi pada layanan administrasi di tingkat kelurahan: proses manual membuat pelayanan kurang cepat dan kurang efisien [2]. Beberapa penelitian kemudian mengembangkan sistem pelayanan berbasis web agar pengelolaan data lebih teratur [3]. Penerapan framework Laravel pada sistem informasi desa juga menunjukkan bahwa sistem informasi dapat menjadi alternatif pemecahan masalah pada pemerintahan desa [6].

Kampung Rejoso, Desa Junrejo, Kota Batu, adalah sentra UMKM yang sudah puluhan tahun dikenal melalui kerajinan batu dan kayu, lalu berkembang ke produk olahan makanan dan minuman. Jumlah pelaku usaha di kawasan ini tercatat menurun. Tantangan yang kerap muncul berkaitan dengan pemasaran, legalitas, regenerasi pelaku usaha, dan akses terhadap program pendampingan [4][5].

Setiap UMKM memiliki jenis usaha, produk, kondisi modal, kemampuan pemasaran, status legalitas, kapasitas produksi, dan kemampuan digital yang berbeda. Pihak desa memerlukan data yang cukup lengkap untuk mengetahui kondisi usaha dan menentukan program pembinaan yang sesuai. Jika data masih tersebar, petugas desa kesulitan merekap informasi, sedangkan kepala desa kesulitan mengambil keputusan karena tidak tersedia ringkasan yang siap pakai. Pencatatan manual juga berisiko menghasilkan data yang tidak mutakhir. Tanpa akses mandiri, pemilik UMKM sulit memperbarui kondisi usahanya dan mengetahui program yang cocok bagi mereka.

Persoalan penentuan sasaran bantuan pernah dibahas dalam penelitian lain. Penelitian tentang bantuan usaha mikro di Kabupaten Padang Lawas Utara menemukan bahwa prioritas penerima bantuan masih ditentukan secara manual, sehingga bantuan tidak tepat sasaran [7]. Efektivitas program bantuan pemerintah bagi usaha mikro juga telah menjadi objek kajian tersendiri [8]. Temuan ini menunjukkan perlunya data usaha yang terstruktur sebagai dasar pencocokan antara kebutuhan pelaku usaha dan program yang tersedia.

Berdasarkan uraian tersebut, dirancang TUMBUH UMKM. Sistem ini mendata UMKM secara terpusat, meminta pemilik usaha mengisi kuesioner kebutuhan dan kendala, lalu menghitung skor tiap kategori dengan pembobotan jawaban. Hasil skor dipakai untuk mengenali tingkat kendala dan mencocokkan UMKM dengan program bantuan yang relevan. Petugas desa mengelola data, memverifikasi isian mandiri, dan mengajukan tindak lanjut. Kepala desa memantau dasbor serta menyetujui atau menolak tindak lanjut. Rekomendasi bersifat informatif. Keputusan pembinaan tetap berada pada pihak desa.

## 1.2 Rumusan Masalah

1. Bagaimana pihak desa dapat mendata dan mengelola informasi UMKM secara terpusat, lengkap, dan mudah diperbarui?  
2. Bagaimana sistem dapat menilai tingkat kendala UMKM pada tiap kategori melalui pembobotan jawaban kuesioner, sehingga pola masalah lebih mudah terlihat?
3. Bagaimana informasi program bantuan yang relevan dapat ditampilkan sebagai rekomendasi sesuai tingkat kendala UMKM bagi petugas desa, kepala desa, dan pemilik UMKM?
4. Bagaimana kepala desa dapat memantau data terpusat dan mengambil keputusan pembinaan berdasarkan dasbor, pengajuan tindak lanjut, serta laporan sistem?
5. Bagaimana pemilik UMKM dapat memiliki akun, mengisi atau memperbarui data usahanya sendiri, mengisi kuesioner, dan melihat rekomendasi program dalam bahasa yang mudah dipahami?

## 1.3 Tujuan

1. Mengembangkan sistem pendataan UMKM desa yang terpusat dan mudah diperbarui sebagai implementasi mata kuliah Pemrograman Web Lanjut.  
2. Menyediakan autentikasi dan pembatasan hak akses bagi tiga pengguna, yaitu pemilik UMKM, petugas desa, dan kepala desa.
3. Mengelompokkan kebutuhan UMKM dengan pembobotan jawaban kuesioner, sehingga tiap usaha memiliki skor dan tingkat kendala per kategori.
4. Menyediakan rekomendasi program bantuan yang relevan berdasarkan tingkat kendala dan kategori yang ditautkan pada program.
5. Membantu kepala desa mengambil keputusan pembinaan melalui dasbor, laporan, dan persetujuan tindak lanjut.  
6. Memastikan fitur utama sistem teruji dan bebas dari bug kritis melalui pengujian yang mengikuti prinsip penjaminan mutu perangkat lunak.

# BAB 2 PROJECT CHARTER

## 2.1 Deskripsi

Proyek ini mengembangkan TUMBUH UMKM, yaitu sistem informasi berbasis web untuk pendataan, pemetaan kebutuhan, dan rekomendasi program bantuan bagi UMKM di tingkat desa. Studi kasus yang diusulkan adalah Kampung Rejoso, Desa Junrejo, Kecamatan Junrejo, Kota Batu. Wilayah ini merupakan kampung wisata UMKM yang sudah memiliki media digital, seperti situs profil desa dan akun Instagram. Media tersebut memudahkan sosialisasi sistem, termasuk ajakan kepada pemilik UMKM untuk membuat akun.

Alur utama sistem adalah data, profil usaha, kuesioner, pembobotan, rekomendasi, pembinaan, lalu pemantauan. Data usaha dan produk dicatat oleh pemilik UMKM atau oleh petugas desa. Pemilik mengisi kuesioner pada lima kategori kendala, yaitu modal, pemasaran, legalitas, produksi, dan digitalisasi. Sistem menghitung skor 0–100 pada tiap kategori, menetapkan tingkat rendah, sedang, atau tinggi, lalu menandai kategori dengan skor tertinggi sebagai kendala utama. Program bantuan yang ditautkan ke kategori tersebut ditampilkan sebagai rekomendasi bila tingkat kendala memenuhi batas minimum program. Petugas desa mengajukan tindak lanjut, kepala desa menyetujui atau menolak, dan pembinaan yang disetujui dicatat beserta hasilnya.

Sistem dibangun dengan Laravel dan PostgreSQL, dengan antarmuka Blade dan Tailwind CSS. Pengerjaan direncanakan dalam 16 minggu.

## 2.2 Tujuan

1. Mengembangkan sistem digital untuk pendataan UMKM desa yang terpusat dan mudah diperbarui.  
2. Menyediakan akses mandiri bagi pemilik UMKM untuk mengisi profil, produk, dan kuesioner kebutuhan.
3. Menghitung skor dan tingkat kendala per kategori dengan pembobotan jawaban.
4. Menampilkan rekomendasi program bantuan yang sesuai dengan tingkat kendala.
5. Menyediakan pengajuan dan persetujuan tindak lanjut pembinaan secara berjenjang.
6. Menyediakan dasbor dan laporan yang dapat diunduh dalam format PDF atau Excel sebagai dasar keputusan kepala desa.  
7. Menghasilkan sistem yang teruji sesuai rencana pengujian mata kuliah Pengujian Mutu Perangkat Lunak.

## 2.3 Sasaran

1. Tersedianya aplikasi TUMBUH UMKM berbasis web yang dapat diakses melalui peramban umum oleh seluruh pengguna sesuai perannya.  
2. Seluruh alur dari pendataan, verifikasi, pengisian kuesioner, pembobotan, rekomendasi, pengajuan tindak lanjut, persetujuan, hingga pencatatan pembinaan dapat dilakukan di dalam sistem.
3. Pemilik UMKM dapat mendaftar, mengisi data, melihat status verifikasi, dan melihat rekomendasi program melalui akunnya sendiri.
4. Data UMKM tersimpan terpusat dan dapat ditelusuri kembali oleh pihak yang berwenang.  
5. Laporan ringkasan dapat diekspor dan digunakan sebagai bahan pengambilan keputusan pembinaan.

## 2.4 Stakeholder

| Pihak | Peran | Kepentingan |
| --- | --- | --- |
| Petugas Desa | Pengguna operasional | Mengelola data terpusat, memverifikasi isian mandiri, mengatur soal dan program, mengajukan tindak lanjut, serta mencatat pembinaan |
| Kepala Desa | Pengguna pimpinan | Memantau dasbor, membaca laporan, serta menyetujui atau menolak tindak lanjut pembinaan |
| Pemilik UMKM | Pengguna mandiri dan penerima manfaat | Memiliki akun, mengisi data usaha dan kuesioner, serta melihat rekomendasi sesuai kendalanya |

## 2.5 Kriteria Keberhasilan

1. Sistem dapat digunakan oleh ketiga jenis pengguna sesuai perannya masing-masing.  
2. Modul autentikasi, pendataan, verifikasi, kuesioner, pembobotan, rekomendasi, persetujuan, dasbor, dan ekspor berjalan sesuai spesifikasi.
3. Skor kategori pada data uji sesuai rumus pembobotan, dan tingkat kendala sesuai ambang kategori.
4. Program yang direkomendasikan sesuai kategori serta tingkat minimumnya, dan tampil pada sisi petugas, kepala desa, serta akun pemilik UMKM.
5. Petugas desa dapat merekap data tanpa proses manual, dan kepala desa memperoleh ringkasan untuk keputusan pembinaan.  
6. Pemilik UMKM memahami hasil analisis dalam bahasa biasa, tanpa istilah skor teknis sebagai pesan utama.
7. Pengujian black-box lolos tanpa bug kritis, dan proposal, laporan, serta bahan ekspo tersedia.

## 2.6 Ruang Lingkup

Yang dikerjakan: 

1. Autentikasi tiga peran dan registrasi mandiri pemilik UMKM.
2. Pendataan profil usaha dan produk, baik oleh pemilik maupun oleh petugas.
3. Verifikasi data mandiri oleh petugas desa.
4. Pengelolaan kategori kendala, bank soal, dan pilihan jawaban beserta bobot serta skornya.
5. Pengisian kuesioner kebutuhan dan kendala.
6. Penghitungan skor, tingkat kendala, dan kendala utama dengan pembobotan jawaban.
7. Pengelolaan program bantuan beserta kategori sasaran dan tingkat minimumnya.
8. Rekomendasi program, pengajuan tindak lanjut, persetujuan kepala desa, dan pencatatan pembinaan.
9. Dasbor ketiga peran serta laporan yang dapat diunduh dalam format PDF atau Excel.

Yang tidak dikerjakan: 

1. Pengelompokan dengan fuzzy clustering atau Fuzzy C-Means.
2. Modul terpisah untuk menyimpan nomor dokumen legalitas, seperti NIB, NPWP, atau sertifikat. Kondisi legalitas ditanyakan di dalam kuesioner kategori Legalitas.
3. Integrasi API resmi pemerintah.
4. Pengajuan atau pencairan dana otomatis.
5. Verifikasi legalitas yang menggantikan proses resmi instansi.
6. Aplikasi mobile native.
7. Marketplace atau transaksi jual beli.
8. Fitur pesan real-time.
9. Masuk dengan Google. Tabel penyiapan akun sosial dapat ada di basis data, tetapi login versi ini memakai email dan kata sandi.

## 2.7 Asumsi dan Batasan

Asumsi. Pihak desa bersedia memakai sistem dan memperbarui data secara berkala. Petugas desa dan kepala desa mendapat penjelasan singkat sebelum sistem digunakan. Informasi program bantuan diinput manual oleh petugas berdasarkan informasi dari pemerintah. Pemilik UMKM bersedia membuat akun, mengisi profil, dan menyelesaikan kuesioner. Petugas bersedia memverifikasi data mandiri, dan kepala desa bersedia meninjau dasbor serta memutuskan pengajuan. Lima kategori awal (modal, pemasaran, legalitas, produksi, dan digitalisasi) cukup untuk studi kasus Kampung Rejoso, dan petugas dapat menyesuaikan soal serta ambang skor.

Batasan. Sistem merupakan produk yang realistis dikerjakan dalam satu semester dan belum terhubung dengan sistem pemerintah. Keputusan akhir pembinaan atau penyaluran bantuan tetap berada pada pihak desa dan instansi terkait. Pemilik UMKM hanya dapat mengakses dan mengubah data usahanya sendiri. Kepala desa tidak mengubah data operasional. Rekomendasi bersifat informatif dan tidak menetapkan penerima bantuan secara otomatis. Satu UMKM dapat memiliki tingkat sedang atau tinggi pada lebih dari satu kategori. Hasil itu diperoleh dari bobot jawaban, bukan dari keanggotaan fuzzy.

## 2.8 Pembagian Pekerjaan

Tim beranggotakan empat orang. Pembagian berikut cukup untuk mengetahui siapa mengerjakan bagian yang mana. Rincian jadwal mingguan tidak dimuat di dokumen ini.

| Anggota | Bagian yang dikerjakan |
| --- | --- |
| Daffa Putra Prasetya | Akun, navigasi, dan analisis: login, registrasi, profil akun, pembatasan tiga peran, menu tiga peran, penghitungan skor, serta pembuatan rekomendasi |
| Hernanda Rizka Utami | Data usaha pada sisi pemilik: jenis usaha, profil usaha, pendataan oleh petugas, produk, form kuesioner, status verifikasi pada akun pemilik, dasbor pemilik, dan tampilan rekomendasi milik sendiri |
| Nazwa Azahra Audina | Data master dan laporan: daftar seluruh UMKM, verifikasi, kategori kendala, bank soal, program bantuan, tampilan rekomendasi untuk petugas, laporan ringkasan, serta ekspor PDF dan Excel |
| Radith Ferdian Hibawan | Tindak lanjut dan dasbor: dasbor petugas, pengajuan tindak lanjut, persetujuan kepala desa, pencatatan pembinaan, riwayat pembinaan, dasbor kepala desa, dan pemantauan status tindak lanjut |

# BAB 3 SPESIFIKASI KEBUTUHAN PERANGKAT LUNAK

Penyusunan spesifikasi pada bab ini memakai pendekatan rekayasa kebutuhan yang lazim: pemangku kepentingan diidentifikasi, kebutuhan fungsional dipisahkan dari kebutuhan nonfungsional, lalu keduanya dimodelkan dengan diagram. ISO/IEC/IEEE 29148:2018 mengatur proses dan isi informasi hasil rekayasa kebutuhan perangkat lunak [10]. Pemodelan memakai UML dan ERD.

Hak akses mengikuti konsep role-based access control. Pada model ini, hak akses dikaitkan dengan peran, dan pengguna menjadi anggota peran yang sesuai [9]. TUMBUH UMKM hanya memiliki tiga peran, yaitu pemilik UMKM (`business_owner`), petugas desa (`officer`), dan kepala desa (`village_head`). Peran tidak ditambah melalui halaman sistem.

## 3.1 Kebutuhan Fungsional

Tabel 3.1 Kebutuhan Fungsional

| Kode | Peran | Kebutuhan |
| --- | --- | --- |
| F-01 | Pemilik UMKM, Petugas Desa, Kepala Desa | Dapat login dan logout. Sistem mengarahkan pengguna ke dasbor sesuai peran |
| F-02 | Pemilik UMKM | Dapat registrasi mandiri dengan nama, email, kata sandi, nomor telepon, dan nama usaha |
| F-03 | Pemilik UMKM, Petugas Desa, Kepala Desa | Dapat melihat dan mengubah profil akun, termasuk nama, email, nomor telepon, dan kata sandi |
| F-04 | Sistem | Membatasi menu dan data sesuai peran. Pemilik UMKM tidak dapat membuka data usaha lain |
| F-05 | Pemilik UMKM, Petugas Desa | Dapat mengisi dan memperbarui profil usaha. Pemilik hanya mengelola usahanya sendiri. Petugas dapat mendata UMKM yang belum memiliki akun |
| F-06 | Pemilik UMKM, Petugas Desa | Dapat menambah, mengubah, dan menonaktifkan produk pada usaha yang berwenang |
| F-07 | Pemilik UMKM, Petugas Desa | Dapat mengisi kuesioner kebutuhan dan kendala. Petugas dapat mendampingi pengisian untuk UMKM yang didatanya |
| F-08 | Petugas Desa | Dapat meninjau data mandiri lalu menetapkan status terverifikasi, perlu perbaikan, atau ditolak, disertai catatan |
| F-09 | Petugas Desa | Dapat mengelola kategori kendala, pertanyaan, dan pilihan jawaban, termasuk bobot pertanyaan dan skor pilihan |
| F-10 | Sistem | Setelah kuesioner selesai, menghitung skor tiap kategori, menetapkan tingkat kendala, dan menentukan kendala utama |
| F-11 | Petugas Desa | Dapat mengelola program bantuan dan menautkannya ke satu atau lebih kategori beserta tingkat minimum |
| F-12 | Pemilik UMKM, Petugas Desa, Kepala Desa | Dapat melihat rekomendasi program. Pemilik hanya melihat usahanya sendiri, dalam bahasa yang mudah dipahami |
| F-13 | Petugas Desa | Dapat mengajukan tindak lanjut atas rekomendasi, lengkap dengan rencana kegiatan |
| F-14 | Kepala Desa | Dapat menyetujui atau menolak pengajuan tindak lanjut, disertai catatan bila perlu |
| F-15 | Petugas Desa, Kepala Desa, Pemilik UMKM | Petugas mencatat pembinaan setelah pengajuan disetujui. Kepala desa dan pemilik dapat melihat riwayat sesuai hak akses |
| F-16 | Pemilik UMKM, Petugas Desa, Kepala Desa | Dapat melihat dasbor sesuai peran: ringkasan usaha sendiri, ringkasan operasional, atau ringkasan pimpinan |
| F-17 | Petugas Desa, Kepala Desa | Dapat melihat laporan ringkasan serta mengunduhnya dalam format PDF atau Excel |

Tabel 3.2 Matriks Hak Akses

| Fitur | Petugas Desa | Kepala Desa | Pemilik UMKM |
| --- | --- | --- | --- |
| Registrasi mandiri | Tidak | Tidak | Ya |
| Login dan kelola profil akun | Ya | Ya | Ya |
| Kelola data seluruh UMKM | Ya | Hanya lihat | Tidak |
| Isi atau ubah data usaha sendiri | Ya, saat mendata | Tidak | Ya |
| Kelola produk | Ya | Hanya lihat | Ya, milik sendiri |
| Isi kuesioner | Ya, saat mendata atau mendampingi | Tidak | Ya, milik sendiri |
| Verifikasi data UMKM | Ya | Tidak | Tidak |
| Kelola kategori, soal, dan program | Ya | Hanya lihat program | Tidak |
| Lihat rekomendasi program | Ya, seluruh UMKM | Ya, seluruh UMKM | Ya, milik sendiri |
| Ajukan tindak lanjut | Ya | Tidak | Tidak |
| Setujui atau tolak tindak lanjut | Tidak | Ya | Tidak |
| Catat pembinaan | Ya | Tidak | Tidak |
| Lihat riwayat pembinaan | Ya, seluruh UMKM | Ya, seluruh UMKM | Ya, milik sendiri |
| Dasbor | Ya, operasional | Ya, pimpinan | Ya, usaha sendiri |
| Laporan dan ekspor | Ya | Ya | Tidak |

## 3.2 Kebutuhan Nonfungsional

Tabel 3.3 Kebutuhan Nonfungsional

| Kode | Aspek | Kebutuhan | Indikator pemenuhan |
| --- | --- | --- | --- |
| NF-01 | Keamanan | Kata sandi disimpan dalam bentuk hash, bukan teks biasa | Kata sandi tidak tampil utuh di basis data |
| NF-02 | Otorisasi | Setiap peran hanya dapat mengakses menu dan data sesuai matriks hak akses | Akses silang antarperan dan antarakun UMKM ditolak |
| NF-03 | Kegunaan | Pemilik UMKM melihat kendala utama dan rekomendasi dalam kalimat biasa | Pesan utama tidak menampilkan istilah membership, cluster, atau rumus |
| NF-04 | Ketersediaan | Sistem berbasis web dan dapat diakses melalui peramban umum | Diuji pada Chrome dan peramban umum lain |
| NF-05 | Kinerja | Halaman utama, daftar UMKM, dan penghitungan skor merespons dalam waktu wajar pada data puluhan hingga sekitar seratus UMKM | Halaman utama dan hasil kuesioner tampil tanpa jeda yang mengganggu |
| NF-06 | Keterawatan | Struktur modul, rumus skor, dan basis data terdokumentasi | ERD, use case, dan penjelasan pembobotan tersedia |
| NF-07 | Kualitas | Fitur utama lolos pengujian black-box tanpa bug kritis | Sesuai rencana pengujian mata kuliah Pengujian Mutu Perangkat Lunak |

## 3.3 Aturan Bisnis

Tabel 3.4 Aturan Bisnis

| Kode | Aturan |
| --- | --- |
| BR-01 | Registrasi mandiri hanya tersedia bagi pemilik UMKM. Akun petugas desa dan kepala desa tidak dapat didaftarkan melalui halaman registrasi |
| BR-02 | Data usaha yang diisi pemilik berstatus menunggu sampai ditinjau petugas. Petugas menetapkan terverifikasi, perlu perbaikan, atau ditolak, disertai catatan. Jika pemilik memperbaiki data yang perlu perbaikan atau yang sudah terverifikasi, status kembali menjadi menunggu |
| BR-03 | Pemilik UMKM hanya dapat melihat dan mengubah data yang terhubung dengan akunnya. Petugas desa dapat mendata UMKM yang belum memiliki akun |
| BR-04 | Pengelompokan memakai pembobotan jawaban, bukan fuzzy clustering dan bukan sekadar menandai kategori sebagai aktif atau tidak aktif. Tiap kategori mendapat skor 0–100. Tingkat dibanding ambang sedang dan ambang tinggi pada kategori tersebut. Bawaan ambang sedang adalah 40 dan ambang tinggi adalah 70. Skor di bawah ambang sedang berarti rendah. Skor dari ambang sedang sampai di bawah ambang tinggi berarti sedang. Skor pada ambang tinggi atau lebih berarti tinggi |
| BR-05 | Rumus skor kategori adalah jumlah dari (skor pilihan × bobot pertanyaan) dibagi jumlah dari (100 × bobot pertanyaan), lalu dikali 100. Jika pertanyaan bertanda skor terbalik, skor yang masuk rumus adalah 100 dikurangi skor pilihan. Kategori dengan skor tertinggi menjadi kendala utama. Satu UMKM dapat berada pada tingkat sedang atau tinggi di lebih dari satu kategori |
| BR-06 | Hanya ada satu kuesioner berjalan untuk tiap UMKM. Kuesioner baru yang diselesaikan menonaktifkan rekomendasi dari kuesioner sebelumnya |
| BR-07 | Rekomendasi dibuat untuk program berstatus aktif. Program cocok bila sedikitnya satu kategori yang ditautkan padanya memiliki tingkat kendala UMKM yang memenuhi tingkat minimum program. Tingkat minimum sedang mencakup sedang dan tinggi. Tingkat minimum tinggi hanya mencakup tinggi. Tingkat rendah tidak menghasilkan rekomendasi pada kategori itu |
| BR-08 | Rekomendasi bersifat informatif dan tidak menetapkan penerima bantuan secara otomatis |
| BR-09 | Hanya petugas desa yang dapat mengajukan tindak lanjut. Hanya kepala desa yang dapat menyetujui atau menolaknya. Petugas tidak dapat menyetujui pengajuannya sendiri |
| BR-10 | Pembinaan hanya dicatat untuk tindak lanjut yang sudah disetujui. Pemilik UMKM hanya melihat riwayat pembinaan miliknya |
| BR-11 | Kepala desa tidak dapat mengubah data operasional UMKM. Aksesnya terbatas pada pemantauan, laporan, dan persetujuan |
| BR-12 | Jawaban pada kategori Legalitas hanya menggambarkan kondisi usaha. Jawaban itu tidak menggantikan verifikasi instansi resmi dan tidak disimpan sebagai arsip nomor dokumen |

Contoh pembobotan. Sebuah kategori memiliki dua pertanyaan. Pertanyaan pertama berbobot 1 dengan skor pilihan 80. Pertanyaan kedua berbobot 2 dengan skor pilihan 50. Skor kategori = ((80 × 1) + (50 × 2)) / ((100 × 1) + (100 × 2)) × 100 = 180 / 300 × 100 = 60. Jika ambang sedang 40 dan ambang tinggi 70, tingkat kategori ini adalah sedang.

Bagi pemilik UMKM, hasil itu ditampilkan sebagai kalimat, misalnya "Kendala utama usaha Anda ada pada pemasaran" atau "Usaha Anda juga perlu perhatian pada modal". Skor dan tingkat dapat dilihat oleh petugas desa dan kepala desa.

## 3.4 Daftar Diagram yang Wajib Dilampirkan

Gambar pada bagian ini belum disisipkan. Setiap caption diikuti tempat untuk menempelkan gambar. Diagram yang wajib dibuat hanya sembilan gambar berikut.

| Gambar | Diagram | Isi minimum |
| --- | --- | --- |
| 3.1 | Swimlane proses bisnis | Empat jalur: Pemilik UMKM, Petugas Desa, Kepala Desa, dan Sistem, dari pendaftaran sampai pembinaan |
| 3.2 | Use case | Tiga aktor dan use case UC-01 sampai UC-16 |
| 3.3 | Activity registrasi, login, dan profil usaha | Cabang berhasil dan gagal pada login, registrasi, serta simpan profil |
| 3.4 | Activity verifikasi data | Status menunggu, terverifikasi, perlu perbaikan, dan ditolak |
| 3.5 | Activity kuesioner dan pembobotan | Simpan jawaban, hitung skor, tentukan tingkat, tetapkan kendala utama |
| 3.6 | Activity rekomendasi dan tindak lanjut | Cocokkan program, ajukan, setujui, atau tolak |
| 3.7 | Activity pembinaan dan laporan | Catat pembinaan yang disetujui, filter laporan, unduh PDF atau Excel |
| 3.8 | ERD | Seluruh entitas dan relasi pada bagian 3.8 |
| 3.9 | Class diagram | Kelas domain pada bagian 3.9 beserta hubungannya |

Diagram urutan, diagram komponen, dan diagram deployment tidak wajib pada dokumen ini.

## 3.5 Proses Bisnis

### 3.5.1 Swimlane

Alur kerja TUMBUH UMKM digambarkan dengan swimlane yang membagi proses berdasarkan peran. Terdapat empat jalur: Pemilik UMKM, Petugas Desa, Kepala Desa, dan Sistem.

Gambar 3.1 Swimlane Diagram Proses Bisnis TUMBUH UMKM

(Tempel gambar di sini)

### 3.5.2 Deskripsi Alur

1. Pemilik UMKM mendaftar dengan nama, email, kata sandi, nomor telepon, dan nama usaha. Setelah masuk, pemilik melengkapi profil usaha dan produk. Untuk UMKM yang belum memiliki akun, petugas desa mengisi data yang sama. Data dari pemilik berstatus menunggu.
2. Pemilik atau petugas yang mendampingi mengisi kuesioner. Pertanyaan tersusun per kategori. Tiap pertanyaan memiliki bobot. Tiap pilihan memiliki skor. Skor yang lebih tinggi berarti kendala yang lebih berat. Pemilik juga dapat menuliskan kendala lain pada isian teks, dan isian itu tidak menjadi kategori keenam.
3. Petugas desa meninjau data yang menunggu. Petugas menetapkan terverifikasi, perlu perbaikan, atau ditolak, lalu menulis catatan bila diperlukan. Pemilik melihat hasilnya pada akunnya.
4. Saat kuesioner diselesaikan, sistem menghitung skor tiap kategori, membandingkannya dengan ambang, dan menyimpan tingkat rendah, sedang, atau tinggi. Kategori berskor tertinggi menjadi kendala utama. Sistem lalu mencocokkan tingkat itu dengan program aktif.
5. Petugas menginput program bantuan, termasuk penyelenggara, syarat, periode, kategori sasaran, dan tingkat minimum. Sistem menampilkan program yang cocok sebagai rekomendasi. Petugas meninjau rekomendasi dan mengajukan tindak lanjut untuk UMKM yang perlu dibina.
6. Kepala desa memantau dasbor dan meninjau pengajuan. Kepala desa menyetujui atau menolak, dengan catatan bila diperlukan. Keputusan akhir pembinaan tetap berada pada pihak desa.
7. Untuk pengajuan yang disetujui, petugas mencatat kegiatan pembinaan, tanggal, uraian, dan hasilnya. Pemilik dapat melihat riwayat miliknya. Petugas dan kepala desa dapat mengekspor laporan ringkasan dalam format PDF atau Excel.

## 3.6 Use Case

Aktor sistem ini adalah Petugas Desa, Kepala Desa, dan Pemilik UMKM. Sistem menjadi pelaku pada penghitungan skor dan pembuatan rekomendasi.

Gambar 3.2 Use Case Diagram TUMBUH UMKM

(Tempel gambar di sini)

Tabel 3.5 Daftar dan Deskripsi Use Case

| Kode | Use case | Aktor | Deskripsi singkat |
| --- | --- | --- | --- |
| UC-01 | Login dan logout | Pemilik UMKM, Petugas Desa, Kepala Desa | Pengguna memasukkan email dan kata sandi. Sistem memvalidasi kredensial lalu membuka dasbor sesuai peran |
| UC-02 | Registrasi akun pemilik UMKM | Pemilik UMKM | Pengguna membuat akun dan kerangka usaha. Sistem menyimpan kata sandi dalam bentuk hash |
| UC-03 | Mengelola profil akun | Pemilik UMKM, Petugas Desa, Kepala Desa | Pengguna melihat dan memperbarui nama, email, nomor telepon, serta kata sandi |
| UC-04 | Mendata profil usaha | Pemilik UMKM, Petugas Desa | Pengguna mengisi identitas usaha, alamat, dusun, tahun mulai, jumlah pekerja, dan jenis usaha. Pemilik hanya mengelola usahanya |
| UC-05 | Mengelola produk | Pemilik UMKM, Petugas Desa | Pengguna menambah, mengubah, atau menonaktifkan produk pada usaha yang berwenang |
| UC-06 | Mengisi kuesioner kebutuhan dan kendala | Pemilik UMKM, Petugas Desa | Pengguna menjawab soal per kategori. Petugas dapat mengisi untuk UMKM yang didatanya |
| UC-07 | Memverifikasi data UMKM | Petugas Desa | Petugas meninjau data mandiri lalu menetapkan terverifikasi, perlu perbaikan, atau ditolak |
| UC-08 | Mengelola kategori kendala dan bank soal | Petugas Desa | Petugas mengatur kategori, ambang skor, pertanyaan, bobot, dan skor pilihan jawaban |
| UC-09 | Menghitung skor dan tingkat kendala | Sistem | Sistem menghitung skor 0–100 per kategori, menetapkan tingkat, dan menentukan kendala utama |
| UC-10 | Mengelola program bantuan | Petugas Desa | Petugas menambah, mengubah, dan menyelesaikan program, lalu menautkan kategori beserta tingkat minimum |
| UC-11 | Melihat rekomendasi program | Pemilik UMKM, Petugas Desa, Kepala Desa | Pengguna melihat program yang cocok. Pemilik hanya melihat usahanya, dalam bahasa biasa |
| UC-12 | Mengajukan tindak lanjut | Petugas Desa | Petugas mengajukan rencana pembinaan atas rekomendasi kepada kepala desa |
| UC-13 | Menyetujui atau menolak tindak lanjut | Kepala Desa | Kepala desa meninjau pengajuan lalu menyetujui atau menolak |
| UC-14 | Mencatat dan melihat pembinaan | Petugas Desa, Kepala Desa, Pemilik UMKM | Petugas mencatat kegiatan setelah pengajuan disetujui. Pihak lain melihat riwayat sesuai hak akses |
| UC-15 | Melihat dasbor | Pemilik UMKM, Petugas Desa, Kepala Desa | Setiap peran melihat ringkasan yang sesuai: usaha sendiri, operasional desa, atau antrean keputusan |
| UC-16 | Mengekspor laporan | Petugas Desa, Kepala Desa | Pengguna menyaring data menurut periode atau kategori, lalu mengunduh PDF atau Excel |

## 3.7 Activity Diagram

Gambar 3.3 sampai Gambar 3.7 disusun dari alur di bawah ini. Tiap gambar mencakup keputusan utama dan pesan gagal yang relevan, misalnya data tidak lengkap, email sudah terpakai, atau tindak lanjut belum disetujui.

Gambar 3.3 Activity Diagram Registrasi, Login, dan Profil Usaha

(Tempel gambar di sini)

1. Login (UC-01). Pengguna membuka halaman login, memasukkan email dan kata sandi, lalu sistem memvalidasi. Jika valid dan akun aktif, pengguna masuk ke dasbor sesuai peran. Jika tidak valid, sistem menampilkan pesan kesalahan.
2. Registrasi (UC-02). Pemilik mengisi formulir. Sistem memeriksa kelengkapan dan keunikan email. Jika lolos, sistem menyimpan akun pemilik UMKM beserta kerangka usaha, lalu mengarahkan pengguna untuk masuk.
3. Profil usaha dan produk (UC-04 dan UC-05). Pengguna membuka formulir, mengisi data, lalu menyimpan. Sistem memvalidasi masukan. Data yang disimpan pemilik mendapat status verifikasi menunggu.

Gambar 3.4 Activity Diagram Verifikasi Data

(Tempel gambar di sini)

4. Verifikasi (UC-07). Petugas membuka daftar data menunggu, memeriksa detail, memilih terverifikasi, perlu perbaikan, atau ditolak, mengisi catatan bila perlu, lalu menyimpan. Sistem memperbarui status yang tampil pada akun pemilik.

Gambar 3.5 Activity Diagram Kuesioner dan Pembobotan

(Tempel gambar di sini)

5. Kuesioner (UC-06). Pengguna menjawab seluruh pertanyaan yang aktif. Sistem menolak penyimpanan akhir jika masih ada pertanyaan wajib yang kosong. Jawaban dapat disimpan sebagai draf sebelum diselesaikan.
6. Pembobotan (UC-09). Saat status kuesioner menjadi selesai, sistem membaca skor pilihan dan bobot, menerapkan skor terbalik bila pertanyaan ditandai demikian, lalu menghitung skor tiap kategori. Sistem membandingkan skor dengan ambang, menyimpan tingkat, dan mengisi kendala utama dengan kategori berskor tertinggi.

Gambar 3.6 Activity Diagram Rekomendasi dan Tindak Lanjut

(Tempel gambar di sini)

7. Program dan rekomendasi (UC-10 dan UC-11). Petugas menyimpan program beserta kategori dan tingkat minimum. Sistem mencocokkan tingkat kendala UMKM dengan tingkat minimum itu, lalu menampilkan program yang cocok. Rekomendasi lama dinonaktifkan bila ada kuesioner selesai yang lebih baru.
8. Pengajuan dan persetujuan (UC-12 dan UC-13). Petugas memilih rekomendasi, mengisi rencana kegiatan, dan mengajukan. Kepala desa membuka antrean, meninjau detail, lalu menyetujui atau menolak. Sistem menyimpan keputusan, catatan, dan waktu keputusan.

Gambar 3.7 Activity Diagram Pembinaan dan Laporan

(Tempel gambar di sini)

9. Pembinaan (UC-14). Petugas membuka tindak lanjut yang disetujui, mengisi judul, tanggal, uraian, dan hasil, lalu menyimpan. Sistem menolak pencatatan jika pengajuan belum disetujui.
10. Laporan (UC-16). Petugas atau kepala desa memilih filter periode atau kategori. Sistem menampilkan ringkasan, lalu membuat berkas PDF atau Excel saat pengguna meminta unduhan.

## 3.8 ERD

Gambar 3.8 ERD TUMBUH UMKM

(Tempel gambar di sini)

Tidak ada tabel peran terpisah dan tidak ada tabel dokumen legalitas. Kondisi legalitas tersimpan sebagai jawaban pada kategori Legalitas.

Tabel 3.6 Entitas dan Atribut Utama

| No. | Entitas | Atribut utama |
| --- | --- | --- |
| 1 | users | id, name, email, password, role, phone, is_active |
| 2 | business_types | id, name, slug, is_active |
| 3 | businesses | id, user_id, business_type_id, created_by, business_name, owner_name, phone, email, address, hamlet, rt, rw, established_year, employee_count, description, operational_status, verification_status, verification_note, verified_by, verified_at |
| 4 | products | id, business_id, name, category, description, price, unit, photo_path, is_active |
| 5 | obstacle_categories | id, name, slug, description, moderate_threshold, high_threshold, sort_order, is_active |
| 6 | assessment_questions | id, obstacle_category_id, type, prompt, help_text, weight, is_reverse_scored, sort_order, is_active |
| 7 | question_options | id, assessment_question_id, label, value, score, sort_order |
| 8 | assessments | id, business_id, filled_by, status, is_current, primary_obstacle_category_id, other_obstacle, completed_at |
| 9 | assessment_answers | id, assessment_id, assessment_question_id, question_option_id, score, weight |
| 10 | assessment_category_scores | id, assessment_id, obstacle_category_id, score, level |
| 11 | assistance_programs | id, name, description, provider, requirements, url, quota, starts_on, ends_on, status, created_by |
| 12 | assistance_program_obstacle_category | id, assistance_program_id, obstacle_category_id, minimum_level |
| 13 | program_recommendations | id, business_id, assistance_program_id, assessment_id, obstacle_category_id, score, is_active |
| 14 | follow_ups | id, business_id, program_recommendation_id, assistance_program_id, submitted_by, planned_activity, planned_on, submission_note, status, decided_by, decision_note, decided_at |
| 15 | coaching_sessions | id, follow_up_id, business_id, assistance_program_id, recorded_by, title, held_on, description, outcome, status |

Tabel 3.7 Relasi Antarentitas

| No. | Relasi | Kardinalitas | Penjelasan |
| --- | --- | --- | --- |
| 1 | users ke businesses sebagai pemilik | 1 : 0..1 | Satu akun pemilik terhubung ke satu usaha. Kolom user_id kosong jika usaha didata petugas dan belum punya akun |
| 2 | business_types ke businesses | 1 : N | Satu jenis usaha dipakai banyak UMKM |
| 3 | businesses ke products | 1 : N | Satu UMKM dapat memiliki banyak produk |
| 4 | businesses ke assessments | 1 : N | Satu UMKM dapat memiliki banyak kuesioner, tetapi hanya satu yang berjalan |
| 5 | obstacle_categories ke assessment_questions | 1 : N | Satu kategori memiliki banyak pertanyaan |
| 6 | assessment_questions ke question_options | 1 : N | Satu pertanyaan memiliki banyak pilihan. Likert memakai lima pilihan |
| 7 | assessments ke assessment_answers | 1 : N | Satu kuesioner menyimpan satu jawaban untuk tiap pertanyaan |
| 8 | assessments ke assessment_category_scores | 1 : N | Satu kuesioner menghasilkan satu skor untuk tiap kategori |
| 9 | assessments ke obstacle_categories sebagai kendala utama | N : 0..1 | Kendala utama diisi setelah skor dihitung |
| 10 | assistance_programs ke obstacle_categories | M : N | Satu program dapat ditujukan ke beberapa kategori, dan satu kategori dapat dituju beberapa program. Relasi menyimpan tingkat minimum |
| 11 | businesses ke program_recommendations | 1 : N | Satu UMKM dapat memperoleh banyak rekomendasi |
| 12 | assistance_programs ke program_recommendations | 1 : N | Satu program dapat direkomendasikan kepada banyak UMKM |
| 13 | assessments ke program_recommendations | 1 : N | Rekomendasi selalu merujuk pada kuesioner yang menghasilkannya |
| 14 | businesses ke follow_ups | 1 : N | Satu UMKM dapat memiliki banyak pengajuan tindak lanjut |
| 15 | follow_ups ke coaching_sessions | 1 : 0..1 | Satu pengajuan yang disetujui dicatat menjadi satu sesi pembinaan |

## 3.9 Class Diagram

Gambar 3.9 Class Diagram TUMBUH UMKM

(Tempel gambar di sini)

Tabel 3.8 Deskripsi Class

| Class | Deskripsi | Method utama |
| --- | --- | --- |
| User | Data autentikasi dan peran pengguna | login(), logout(), register(), updateProfil(), hasRole() |
| BusinessType | Master jenis usaha | daftarAktif() |
| Business | Profil usaha, status operasional, dan status verifikasi | simpan(), perbarui(), verifikasi(), mintaPerbaikan(), tolak() |
| Product | Produk yang dihasilkan UMKM | tambah(), ubah(), nonaktifkan() |
| ObstacleCategory | Kategori kendala beserta ambang sedang dan tinggi | tambah(), ubah(), tentukanTingkat() |
| AssessmentQuestion | Pertanyaan kuesioner, bobot, dan tanda skor terbalik | tambah(), ubah(), nonaktifkan() |
| QuestionOption | Pilihan jawaban dan skor kendalanya | tambah(), ubah() |
| Assessment | Satu pengisian kuesioner pada satu UMKM | simpanDraf(), selesaikan(), tandaiBerjalan() |
| AssessmentAnswer | Jawaban yang dipilih, beserta salinan skor dan bobot | simpan() |
| CategoryScore | Skor dan tingkat satu kategori pada satu kuesioner | hitung(), simpan() |
| AssistanceProgram | Program bantuan dan kategori sasaran | tambah(), ubah(), nonaktifkan() |
| Recommendation | Hasil pencocokan UMKM dengan program | generate(), nonaktifkan() |
| FollowUp | Pengajuan tindak lanjut dan keputusan kepala desa | ajukan(), setujui(), tolak() |
| CoachingSession | Kegiatan pembinaan dari pengajuan yang disetujui | catat(), ubahStatus() |
| Report | Ringkasan dasbor dan berkas unduhan | ringkasan(), eksporPdf(), eksporExcel() |

Hubungan antarkelas mengikuti kardinalitas pada Tabel 3.7. User berasosiasi dengan Business sebagai pemilik, pembuat data, atau petugas yang memverifikasi. Business berasosiasi dengan Product, Assessment, Recommendation, FollowUp, dan CoachingSession. Assessment berasosiasi dengan AssessmentAnswer dan CategoryScore. ObstacleCategory berasosiasi dengan AssessmentQuestion dan, secara many-to-many, dengan AssistanceProgram. Report membaca data dari kelas-kelas tersebut tanpa menyimpan tabel sendiri.

# BAB 4 PENUTUP

## 4.1 Kesimpulan

1. Pendataan yang tersebar menyulitkan petugas desa merekap kondisi UMKM dan menyulitkan kepala desa menentukan pembinaan. TUMBUH UMKM menyediakan data usaha, produk, dan kuesioner kebutuhan dalam satu sistem, dengan akses mandiri bagi pemilik UMKM.
2. Sistem melayani tiga peran, yaitu pemilik UMKM, petugas desa, dan kepala desa. Kewenangan tiap peran dibatasi pada menu dan data yang sesuai.
3. Pengelompokan dilakukan dengan pembobotan jawaban. Tiap kategori mendapat skor 0–100 dan tingkat rendah, sedang, atau tinggi. Satu UMKM dapat menonjol pada lebih dari satu kategori. Kategori berskor tertinggi menjadi kendala utama.
4. Rekomendasi program bersifat informatif. Program ditampilkan bila tingkat kendala memenuhi tingkat minimum pada kategori yang ditautkan. Keputusan tindak lanjut tetap berada pada petugas desa dan kepala desa.
5. Sembilan diagram pada Bagian 3.4 menjadi lampiran wajib: swimlane, use case, lima activity diagram, ERD, dan class diagram.

## 4.2 Saran

1. Redaksi soal, bobot, dan ambang kategori perlu dicocokkan dengan hasil observasi dan wawancara di Kampung Rejoso agar skor mencerminkan kondisi lapangan.
2. Pada pengembangan lanjut, program bantuan dapat dihubungkan dengan sumber resmi dinas terkait, dan masuk dengan Google dapat diaktifkan tanpa mengubah peran pengguna.
3. Rencana pengujian dapat diperinci dengan format IEEE 829, meliputi alur tiga peran, penghitungan skor, rekomendasi, serta pengajuan sampai pencatatan pembinaan.

# DAFTAR PUSTAKA

[1] E. Mardinata, T. D. Cahyono, and R. Muhammad Rizqi, "Transformasi Digital Desa Melalui Sistem Informasi Desa (SID): Meningkatkan Kualitas Pelayanan Publik dan Kesejahteraan Masyarakat," Parta: J. Pengabdi. Kpd. Masy., vol. 4, no. 1, pp. 73–81, 2023, doi: 10.38043/parta.v4i1.4402. https://doi.org/10.38043/parta.v4i1.4402

[2] A. P. Sari, D. D. Kurnia, and B. Rudianto, "Aplikasi Pelayanan Publik pada Unit Pelaksana Pelayanan Terpadu Satu Pintu (PTSP) Berbasis Web," Hexagon: J. Tek. dan Sains, vol. 2, no. 2, pp. 66–70, 2021, doi: 10.36761/hexagon.v2i2.1089. https://doi.org/10.36761/hexagon.v2i2.1089

[3] L. Mutiara, Yuliadi, Y. Mulyanto, Rodianto, F. D. Ikram, W. Ismiyarti, and H. Rosika, "Sistem Informasi Pelayanan Publik Desa Satu Pintu Berbasis Web," J. Teknol. dan Ilmu Komput. Prima, vol. 7, no. 1, pp. 98–105, 2024, doi: 10.34012/jutikomp.v7i1.5126. https://doi.org/10.34012/jutikomp.v7i1.5126

[4] N. N. Muna and N. H. P. Meiji, "Dinamika Perubahan Sosial Masyarakat dalam Keberlanjutan Industri Kerajinan di Kampung Wisata Rejoso Kota Batu," J. Perspektif, vol. 8, no. 2, 2025, doi: 10.24036/perspektif.v8i2.1133. https://doi.org/10.24036/perspektif.v8i2.1133

[5] Jatim Times, "Sukirno Tohu Craft Beberkan Perkembangan Kampung UMKM Rejoso Kota Batu," 24 Feb. 2023. [Online]. Available: https://jatimtimes.com/baca/284343/20230224/201400/sukirno-tohu-craft-beberkan-perkembangan-kampung-umkm-rejoso-kota-batu

[6] S. Aji, D. Pratmanto, A. Ardiansyah, and S. Saifudin, "Implementasi Framework Laravel dalam Perancangan Sistem Informasi Desa," Indonesian J. Softw. Eng. (IJSE), vol. 7, no. 2, pp. 237–246, 2021, doi: 10.31294/ijse.v7i2.12050. https://doi.org/10.31294/ijse.v7i2.12050

[7] N. M. Harahap, R. Kurniawan R, and I. A. Sinaga, "Sistem Pendukung Keputusan Penerima Bantuan Usaha Mikro Menggunakan Metode AHP dan SAW Berbasis Web," JISTech (J. Islamic Sci. Technol.), vol. 10, no. 1, pp. 85–95, 2025, doi: 10.30829/jistech.v10i1.24159. https://doi.org/10.30829/jistech.v10i1.24159

[8] N. F. Mustofa and R. Yunita, "Efektivitas Program Bantuan Pemerintah bagi Usaha Mikro di Kabupaten Ponorogo," Niqosiya: J. Econ. Bus. Res., vol. 1, no. 2, pp. 233–246, 2021, doi: 10.21154/niqosiya.v1i2.288. https://doi.org/10.21154/niqosiya.v1i2.288

[9] R. S. Sandhu, E. J. Coyne, H. L. Feinstein, and C. E. Youman, "Role-Based Access Control Models," IEEE Computer, vol. 29, no. 2, pp. 38–47, Feb. 1996, doi: 10.1109/2.485845. https://doi.org/10.1109/2.485845

[10] ISO/IEC/IEEE, ISO/IEC/IEEE 29148:2018 Systems and Software Engineering: Life Cycle Processes: Requirements Engineering, 2018. [Online]. Available: https://webstore.iec.ch/en/publication/64315
