# Pembagian unit test PHPUnit

Diperbarui 5 November 2026. Pemilik modul mengikuti `docs/skpl.md` dan `docs/M01_Workspace.md`.

Dokumen ini hanya berisi tes PHPUnit yang belum ada. Berikan berkas ini kepada AI, lalu sebut nama anggota yang dikerjakan. AI mengerjakan bagian nama itu saja.

## Aturan untuk AI

1. Gunakan PHPUnit. Kelas tes meng-extend `Tests\TestCase`. Proyek ini tidak memakai Pest.
2. Tes request, halaman, dan validasi diletakkan di `tests/Feature`. Tes `User::initials` dan `User::roleLabel` diletakkan di `tests/Unit`.
3. Buat berkas baru dengan `php artisan make:test --phpunit {Nama} --no-interaction`. Untuk `UserLabelTest`, tambahkan `--unit`. Berkas yang sudah ada di tabel cukup ditambahi metode baru.
4. Feature test memakai `Illuminate\Foundation\Testing\RefreshDatabase`. Data pengguna dibuat dengan `User::factory()`. Kata sandi bawaan factory adalah `password`.
5. Satu metode menguji satu perilaku. Nama metode diawali `test_` dan memakai snake case.
6. Panggil rute lewat `route('nama.rute')` bila rute punya nama. `POST /login` dan `POST /register` tidak punya nama, jadi pakai path `/login` dan `/register`.
7. Untuk penolakan form biasa, mulai dari `$this->from(...)` lalu `assertSessionHasErrors`. Untuk endpoint JSON, pakai `postJson` atau `putJson` dan `assertUnprocessable()` plus `assertJsonValidationErrors`.
8. Usaha pada rute `/umkm/produk` dan `/umkm/kebutuhan` harus sudah terverifikasi: `Business::factory()->verified()`.
9. Kolom `follow_ups.business_id` menyimpan `users.id` pemilik, bukan `businesses.id`.
10. Metode tes yang sudah ada di berkas tujuan dibiarkan. Tambahkan metode baru di bawahnya.
11. Setelah menulis, jalankan tes berkas itu: `php artisan test --compact {path}`. Rapikan PHP dengan `vendor/bin/pint --dirty --format agent`.
12. Ubah kode aplikasi hanya jika tes menemukan perilaku yang berbeda dari spesifikasi di bawah. Perilaku yang ditulis di sini adalah perilaku yang harus tetap ada.

## Ringkasan

| Pembuat | Fitur | Berkas PHPUnit | Kode yang diuji |
|---|---|---|---|
| Daffa Putra Prasetya | Masuk | `tests/Feature/LoginTest.php` | `AuthController::login` |
| Daffa Putra Prasetya | Daftar pemilik | `tests/Feature/RegistrationProfileFlowTest.php` | `AuthController::register` |
| Daffa Putra Prasetya | Sesi akun nonaktif | `tests/Feature/LoginTest.php` | `EnsureUserIsActive` |
| Daffa Putra Prasetya | Label nama dan peran | `tests/Unit/UserLabelTest.php` | `User::initials`, `User::roleLabel` |
| Hernanda Rizka Utami | Kelola produk | `tests/Feature/ProductManagementTest.php` | `ProductController::index`, `store`, `toggle` |
| Hernanda Rizka Utami | Kuesioner belum lengkap | `tests/Feature/AssessmentCompletionTest.php` | `AssessmentController::save` |
| Hernanda Rizka Utami | Validasi profil usaha | `tests/Feature/BusinessProfileValidationTest.php` | `BusinessProfileController::save` |
| Nazwa Azahra Audina | Ubah kategori kendala | `tests/Feature/ObstacleCategoryManagementTest.php` | `ObstacleCategoryController::update` |
| Nazwa Azahra Audina | Akses kategori kendala | `tests/Feature/ObstacleCategoryManagementTest.php` | `ObstacleCategoryController` |
| Radith Ferdian Hibawan | Pengajuan tindak lanjut | `tests/Feature/FollowUpWorkflowTest.php` | `FollowUpController::store` |
| Radith Ferdian Hibawan | Catat pembinaan | `tests/Feature/CoachingSessionHistoryTest.php` | `CoachingSessionController::store` |

## Daffa Putra Prasetya

Modul akun, masuk, dan sesi.

| Tes | Fitur | Berkas | Rute |
|---|---|---|---|
| `test_wrong_password_stays_on_the_login_form` | Masuk | `tests/Feature/LoginTest.php` | `POST /login` |
| `test_unknown_account_uses_the_same_message_as_a_wrong_password` | Masuk | `tests/Feature/LoginTest.php` | `POST /login` |
| `test_login_requires_an_identifier_and_a_password` | Masuk | `tests/Feature/LoginTest.php` | `POST /login` |
| `test_officer_can_sign_in_with_their_name` | Masuk | `tests/Feature/LoginTest.php` | `POST /login` |
| `test_inactive_account_cannot_sign_in` | Masuk | `tests/Feature/LoginTest.php` | `POST /login` |
| `test_duplicate_email_is_rejected` | Daftar pemilik | `tests/Feature/RegistrationProfileFlowTest.php` | `POST /register` |
| `test_short_password_is_rejected` | Daftar pemilik | `tests/Feature/RegistrationProfileFlowTest.php` | `POST /register` |
| `test_registration_requires_name_email_password_and_business_name` | Daftar pemilik | `tests/Feature/RegistrationProfileFlowTest.php` | `POST /register` |
| `test_posted_role_is_ignored_and_the_account_stays_a_business_owner` | Daftar pemilik | `tests/Feature/RegistrationProfileFlowTest.php` | `POST /register` |
| `test_a_signed_in_user_is_logged_out_after_the_account_is_deactivated` | Sesi | `tests/Feature/LoginTest.php` | `GET /akun` |
| `test_initials_use_the_first_letter_of_up_to_two_words` | Shell samping | `tests/Unit/UserLabelTest.php` | — |
| `test_each_role_has_an_indonesian_label` | Shell samping | `tests/Unit/UserLabelTest.php` | — |

### Masuk

Fitur: halaman `/login`. Pemilik, petugas, dan kepala desa masuk dengan email atau nama. Akun nonaktif tidak mendapat sesi.

Kode: `app/Http/Controllers/AuthController.php` metode `login`. Pesan akun nonaktif ada di `App\Http\Middleware\EnsureUserIsActive::MESSAGE`.

Buat `tests/Feature/LoginTest.php`. Setiap tes form memakai `$this->from('/login')->post('/login', ...)`.

| Metode | Susunan | Yang dinyatakan |
|---|---|---|
| `test_wrong_password_stays_on_the_login_form` | `User::factory()` dengan email `siti@example.com` dan `is_active = true`. Kirim email itu dan kata sandi `bukan-password`. | `assertSessionHasErrors('login')`. Error berisi `Email/Username atau kata sandi salah.` `assertGuest()`. |
| `test_unknown_account_uses_the_same_message_as_a_wrong_password` | Tidak ada pengguna dengan email `tidak-ada@example.com`. | Pesan error `login` sama dengan tes kata sandi salah. `assertGuest()`. |
| `test_login_requires_an_identifier_and_a_password` | Kirim `login` dan `password` kosong. | `assertSessionHasErrors(['login', 'password'])`. `assertGuest()`. |
| `test_officer_can_sign_in_with_their_name` | Petugas `role = officer`, `name = Budi Santoso`, `is_active = true`. Kirim `login = Budi Santoso` dan `password = password`. | `assertRedirect(route('dashboard'))`. `assertAuthenticatedAs` petugas itu. |
| `test_inactive_account_cannot_sign_in` | Pengguna `is_active = false` dan kata sandi factory `password`. | `assertSessionHasErrors('login')`. Error berisi `Akun ini tidak aktif.` `assertGuest()`. |

`test_officer_can_sign_in_with_their_name` memakai nama, bukan email. `AuthController` memilih kolom `name` ketika nilai `login` bukan email.

### Daftar pemilik

Fitur: halaman `/register`. Akun baru selalu `business_owner` dan, pada jalur sukses yang sudah ada, diarahkan ke profil usaha.

Kode: `app/Http/Controllers/AuthController.php` metode `register`.

Tambahkan metode pada `tests/Feature/RegistrationProfileFlowTest.php`. Kelas ini sudah memakai `RefreshDatabase`.

| Metode | Susunan | Yang dinyatakan |
|---|---|---|
| `test_duplicate_email_is_rejected` | `User::factory()` dengan email `siti@example.com`. Kirim `POST /register` dengan email yang sama, nama, kata sandi `password`, dan `business_name`. | `assertSessionHasErrors('email')`. Jumlah `users` tetap 1. `assertGuest()`. |
| `test_short_password_is_rejected` | Kirim kata sandi `pendek`. | `assertSessionHasErrors('password')`. Tidak ada baris `users` dengan email yang dikirim. |
| `test_registration_requires_name_email_password_and_business_name` | `$this->from('/register')->post('/register', [])`. | `assertSessionHasErrors(['name', 'email', 'password', 'business_name'])`. |
| `test_posted_role_is_ignored_and_the_account_stays_a_business_owner` | Kirim data daftar yang valid plus `role = officer`. | Pengguna yang tersimpan memiliki `role = business_owner`. `assertAuthenticated()`. `assertRedirect(route('umkm.profil'))`. |

### Sesi akun yang dinonaktifkan

Fitur: middleware `active` pada halaman yang sudah masuk, termasuk profil akun. Setelah kepala desa menonaktifkan petugas, permintaan berikutnya mengeluarkan petugas itu.

Kode: `app/Http/Middleware/EnsureUserIsActive.php`.

Tambahkan di `tests/Feature/LoginTest.php`.

| Metode | Susunan | Yang dinyatakan |
|---|---|---|
| `test_a_signed_in_user_is_logged_out_after_the_account_is_deactivated` | Petugas aktif dimasukkan dengan `actingAs`. Setelah itu `$officer->update(['is_active' => false])`. Minta `GET route('account.edit')`. | `assertRedirect(route('login'))`. Error `login` berisi `Akun ini tidak aktif.` `assertGuest()`. |

### Label nama dan peran

Fitur: panel samping. Inisial avatar diambil dari nama. Teks peran menampilkan bahasa Indonesia.

Kode: `app/Models/User.php` metode `initials` dan `roleLabel`.

Buat `tests/Unit/UserLabelTest.php` dengan `php artisan make:test --phpunit --unit UserLabelTest --no-interaction`. Ubah induk kelas menjadi `Tests\TestCase`. Jangan memakai `RefreshDatabase`. Susun data dengan `User::factory()->make([...])`.

| Metode | Masukan | Hasil |
|---|---|---|
| `test_initials_use_the_first_letter_of_up_to_two_words` | `Daffa Putra` | `DP` |
| metode yang sama, atau tiga panggilan terpisah jika lebih jelas | `Siti` | `S` |
| metode yang sama | nama `''` | `TU` |
| `test_each_role_has_an_indonesian_label` | `User::ROLE_OFFICER` | `Petugas Desa` |
| metode yang sama | `User::ROLE_VILLAGE_HEAD` | `Kepala Desa` |
| metode yang sama | `User::ROLE_BUSINESS_OWNER` | `Pemilik UMKM` |

Tiga bentuk inisial boleh menjadi tiga metode: `test_two_words_use_both_initials`, `test_one_word_uses_one_initial`, dan `test_a_blank_name_uses_the_fallback_initials`.

## Hernanda Rizka Utami

Modul profil usaha, produk, dan kuesioner pemilik.

| Tes | Fitur | Berkas | Rute |
|---|---|---|---|
| `test_owner_stores_a_product` | Produk | `tests/Feature/ProductManagementTest.php` | `umkm.produk.store` |
| `test_owner_lists_products_ordered_by_name` | Produk | `tests/Feature/ProductManagementTest.php` | `umkm.produk.index` |
| `test_owner_toggles_a_product_off_and_on` | Produk | `tests/Feature/ProductManagementTest.php` | `umkm.produk.toggle` |
| `test_product_name_and_category_are_required` | Produk | `tests/Feature/ProductManagementTest.php` | `umkm.produk.store` |
| `test_a_non_image_photo_is_rejected` | Produk | `tests/Feature/ProductManagementTest.php` | `umkm.produk.store` |
| `test_officer_cannot_store_a_product` | Produk | `tests/Feature/ProductManagementTest.php` | `umkm.produk.store` |
| `test_a_partially_answered_category_is_rejected` | Kebutuhan dan kendala | `tests/Feature/AssessmentCompletionTest.php` | `umkm.kebutuhan.save` |
| `test_profile_save_requires_the_business_fields` | Profil usaha | `tests/Feature/BusinessProfileValidationTest.php` | `umkm.profil.save` |
| `test_established_year_and_employee_count_must_be_valid` | Profil usaha | `tests/Feature/BusinessProfileValidationTest.php` | `umkm.profil.save` |

### Kelola produk

Fitur: halaman `/umkm/produk`. Pemilik menambah, melihat, dan menonaktifkan produk usahanya. Petugas tidak mengelola produk. Rute ini berada di dalam middleware `business.verified`.

Kode: `app/Http/Controllers/ProductController.php` metode `index`, `store`, dan `toggle`.

Buat `tests/Feature/ProductManagementTest.php`. Untuk setiap tes pemilik, buat pengguna `business_owner` dan `Business::factory()->verified()` dengan `user_id` serta `created_by` sama dengan id pemilik. Tidak ada `ProductFactory`. Produk yang sudah ada dibuat dengan `Product::query()->create`, mengikuti `tests/Feature/ProductPolicyTest.php`.

| Metode | Susunan | Yang dinyatakan |
|---|---|---|
| `test_owner_stores_a_product` | `actingAs` pemilik. `postJson(route('umkm.produk.store'), ['name' => 'Keripik Asin', 'category' => 'Makanan'])`. | Status 201. `products.name = Keripik Asin`, `is_active = true`, dan `business_id` usaha pemilik. |
| `test_owner_lists_products_ordered_by_name` | Dua produk pada usaha yang sama: `Zebra` dan `Apel`. `getJson(route('umkm.produk.index'))`. | `assertOk()`. Nama pertama `Apel`, nama kedua `Zebra`. |
| `test_owner_toggles_a_product_off_and_on` | Produk `is_active = true`. `patchJson(route('umkm.produk.toggle', $product))` dua kali. | Setelah panggilan pertama `is_active` false. Setelah panggilan kedua `is_active` true. Keduanya tercatat di `products`. |
| `test_product_name_and_category_are_required` | `postJson` dengan `name` dan `category` kosong. | `assertUnprocessable()`. `assertJsonValidationErrors(['name', 'category'])`. Tidak ada baris produk baru. |
| `test_a_non_image_photo_is_rejected` | `Illuminate\Http\UploadedFile::fake()->create('bukti.pdf', 100, 'application/pdf')`. Kirim bersama `name` dan `category` yang valid lewat `post`, bukan `postJson`, agar berkas terkirim. | `assertUnprocessable()` atau `assertSessionHasErrors('photo')` sesuai bentuk respons. Tidak ada produk `name` yang dikirim. |
| `test_officer_cannot_store_a_product` | `actingAs` pengguna `role = officer`. `postJson` ke `umkm.produk.store` dengan nama dan kategori valid. | `assertForbidden()`. Tidak ada produk baru. |

### Kuesioner yang belum lengkap

Fitur: halaman `/umkm/kebutuhan`. Bidang yang mulai dijawab harus tuntas. Soal yang terlewat tidak boleh membuat asesmen, skor, atau rekomendasi.

Kode: `app/Http/Controllers/AssessmentController.php` metode `save`, khususnya `ensureAnsweredCategoriesAreComplete`.

Tambahkan pada `tests/Feature/AssessmentCompletionTest.php`. Kelas ini sudah memakai `RefreshDatabase`.

| Metode | Susunan | Yang dinyatakan |
|---|---|---|
| `test_a_partially_answered_category_is_rejected` | Pemilik dan `Business::factory()->verified()`. Satu `ObstacleCategory` bernama `Pemasaran` dengan dua `AssessmentQuestion` aktif. Masing-masing soal punya satu `QuestionOption`. `postJson(route('umkm.kebutuhan.save'), ['answers' => [satu jawaban saja]])`. | `assertUnprocessable()`. `assertJsonValidationErrors('answers')`. Pesan menyebut `Pemasaran`. `assertDatabaseCount('assessments', 0)`. |

Contoh pembuatan soal dan opsi ada di metode pertama `AssessmentCompletionTest`.

### Validasi profil usaha

Fitur: halaman `/umkm/profil`. Field wajib menentukan profil dianggap lengkap. Tahun dan jumlah tenaga kerja yang tidak valid tidak boleh tersimpan.

Kode: `app/Http/Controllers/BusinessProfileController.php` metode `save`. Aturan ada di metode `rules`.

Buat `tests/Feature/BusinessProfileValidationTest.php`. Pemilik pada tes ini belum punya baris `businesses`.

| Metode | Susunan | Yang dinyatakan |
|---|---|---|
| `test_profile_save_requires_the_business_fields` | `actingAs` pemilik. `postJson(route('umkm.profil.save'), [])`. | `assertUnprocessable()`. `assertJsonValidationErrors` untuk `business_type_id`, `business_name`, `owner_name`, `phone`, `email`, `address`, `hamlet`, `established_year`, dan `employee_count`. `rt`, `rw`, dan `description` tidak masuk daftar error. `assertDatabaseCount('businesses', 0)`. |
| `test_established_year_and_employee_count_must_be_valid` | Buat `BusinessType`. Kirim payload lengkap yang valid, lalu ganti `established_year` menjadi `2099` dan `employee_count` menjadi `banyak`. | `assertJsonValidationErrors(['established_year', 'employee_count'])`. Tidak ada usaha dengan nama yang dikirim. |

Payload lengkap yang valid: `business_type_id`, `business_name`, `owner_name`, `phone`, `email`, `address`, `hamlet`, `established_year = 2018`, `employee_count = 2`.

## Nazwa Azahra Audina

Modul kategori kendala. Ambang kategori ini dipakai skor kuesioner untuk menentukan level rendah, sedang, dan tinggi.

| Tes | Fitur | Berkas | Rute |
|---|---|---|---|
| `test_officer_updates_an_obstacle_category` | Kategori kendala | `tests/Feature/ObstacleCategoryManagementTest.php` | `petugas.kategori-kendala.update` |
| `test_update_rejects_a_high_threshold_that_is_not_above_the_moderate_threshold` | Kategori kendala | `tests/Feature/ObstacleCategoryManagementTest.php` | `petugas.kategori-kendala.update` |
| `test_update_rejects_a_slug_owned_by_another_category` | Kategori kendala | `tests/Feature/ObstacleCategoryManagementTest.php` | `petugas.kategori-kendala.update` |
| `test_update_keeps_the_category_own_slug` | Kategori kendala | `tests/Feature/ObstacleCategoryManagementTest.php` | `petugas.kategori-kendala.update` |
| `test_guest_is_redirected_from_obstacle_category_actions` | Akses kategori | `tests/Feature/ObstacleCategoryManagementTest.php` | index, update, toggle, destroy |
| `test_owner_and_village_head_cannot_change_obstacle_categories` | Akses kategori | `tests/Feature/ObstacleCategoryManagementTest.php` | update, toggle, destroy |

### Ubah kategori kendala

Fitur: halaman `/petugas/kategori-kendala`.

Kode: `app/Http/Controllers/ObstacleCategoryController.php` metode `update`. Controller mengubah slug dengan `Str::slug` sebelum menyimpan.

Tambahkan pada `tests/Feature/ObstacleCategoryManagementTest.php`. Kelas ini sudah punya `payload()` dan `RefreshDatabase`. Petugas dibuat dengan `User::factory()->create(['role' => User::ROLE_OFFICER])`.

| Metode | Susunan | Yang dinyatakan |
|---|---|---|
| `test_officer_updates_an_obstacle_category` | `ObstacleCategory::factory()` dengan slug `pemasaran`. `actingAs` petugas. `put(route('petugas.kategori-kendala.update', $category), payload nama Baru, slug pemasaran-baru, ambang 40 dan 70)`. | `assertRedirect(route('petugas.kategori-kendala.index'))`. `assertSessionHas('success')`. Database menyimpan nama baru dan slug `pemasaran-baru`. |
| `test_update_rejects_a_high_threshold_that_is_not_above_the_moderate_threshold` | `$this->from(route('petugas.kategori-kendala.index'))`. Payload dengan `moderate_threshold = 70` dan `high_threshold = 70`. | `assertSessionHasErrors('high_threshold')`. Pesan: `Ambang tinggi harus lebih besar daripada ambang sedang.` Ambang di database tidak berubah. |
| `test_update_rejects_a_slug_owned_by_another_category` | Dua kategori, slug `pemasaran` dan `produksi`. Ubah kategori pertama supaya slug-nya `produksi`. | `assertSessionHasErrors('slug')`. Slug kategori pertama tetap `pemasaran`. |
| `test_update_keeps_the_category_own_slug` | Kategori slug `pemasaran`. Put memakai slug `Pemasaran` dan nama baru. | Redirect sukses. Slug tetap `pemasaran`. Nama baru tersimpan. |

### Akses halaman kategori kendala

Fitur: halaman yang sama. Hanya petugas yang boleh mengubah master kategori.

Kode: `ObstacleCategoryController` bersama middleware `role:officer` pada grup rute petugas di `routes/web.php`.

Tambahkan di berkas yang sama.

| Metode | Susunan | Yang dinyatakan |
|---|---|---|
| `test_guest_is_redirected_from_obstacle_category_actions` | Satu kategori. Tanpa `actingAs`, panggil `GET` index, `PUT` update, `PATCH` toggle, dan `DELETE` destroy. | Setiap respons `assertRedirect(route('login'))`. Nama kategori tidak berubah. |
| `test_owner_and_village_head_cannot_change_obstacle_categories` | Ulangi update, toggle, dan destroy sebagai `business_owner`, lalu sebagai `village_head`. | `assertForbidden()` pada keenam permintaan. `is_active` dan nama kategori tetap. |

## Radith Ferdian Hibawan

Modul tindak lanjut dan pembinaan.

| Tes | Fitur | Berkas | Rute |
|---|---|---|---|
| `test_follow_up_requires_a_planned_activity` | Tindak lanjut | `tests/Feature/FollowUpWorkflowTest.php` | `petugas.tindak-lanjut.store` |
| `test_follow_up_requires_a_planned_date` | Tindak lanjut | `tests/Feature/FollowUpWorkflowTest.php` | `petugas.tindak-lanjut.store` |
| `test_follow_up_requires_a_submission_note` | Tindak lanjut | `tests/Feature/FollowUpWorkflowTest.php` | `petugas.tindak-lanjut.store` |
| `test_pending_follow_up_cannot_be_recorded_as_coaching` | Catat pembinaan | `tests/Feature/CoachingSessionHistoryTest.php` | `petugas.catat-pembinaan.store` |
| `test_rejected_follow_up_cannot_be_recorded_as_coaching` | Catat pembinaan | `tests/Feature/CoachingSessionHistoryTest.php` | `petugas.catat-pembinaan.store` |
| `test_a_follow_up_accepts_only_one_coaching_session` | Catat pembinaan | `tests/Feature/CoachingSessionHistoryTest.php` | `petugas.catat-pembinaan.store` |

### Pengajuan tindak lanjut

Fitur: halaman `/petugas/tindak-lanjut`. Pengajuan yang lolos validasi masuk antrean persetujuan kepala desa dengan status `pending`.

Kode: `app/Http/Controllers/FollowUpController.php` metode `store`.

Tambahkan pada `tests/Feature/FollowUpWorkflowTest.php`. Petugas `actingAs` dengan `role = officer`. `business_id` pada payload adalah `users.id` pemilik, bukan `businesses.id`.

Payload dasar:

```php
[
    'business_id' => $owner->id,
    'planned_activity' => 'Pendampingan foto produk',
    'planned_on' => '2026-12-01',
    'submission_note' => 'Pemilik sudah mendaftarkan minat.',
]
```

| Metode | Perubahan payload | Yang dinyatakan |
|---|---|---|
| `test_follow_up_requires_a_planned_activity` | `planned_activity` dikosongkan | `postJson` `assertUnprocessable()`. `assertJsonValidationErrors('planned_activity')`. `assertDatabaseCount('follow_ups', 0)`. |
| `test_follow_up_requires_a_planned_date` | `planned_on` dikosongkan | Error pada `planned_on`. Tidak ada baris `follow_ups`. |
| `test_follow_up_requires_a_submission_note` | `submission_note` dikosongkan | Error pada `submission_note`. Tidak ada baris `follow_ups`. |

Pemilik cukup `User::factory()->create()`. Validasi `business_id` memakai `exists:businesses,user_id`, jadi buat juga `Business::factory()->create(['user_id' => $owner->id])` agar field yang dikosongkan menjadi satu-satunya error.

### Catat pembinaan

Fitur: halaman `/petugas/catat-pembinaan`. Sesi hanya lahir dari tindak lanjut yang sudah disetujui. Satu pengajuan hanya satu sesi.

Kode: `app/Http/Controllers/CoachingSessionController.php` metode `store`.

Tambahkan pada `tests/Feature/CoachingSessionHistoryTest.php`. Susunan tindak lanjut yang disetujui sudah ada di `test_petugas_records_coaching_session_and_it_moves_to_history`: `FollowUp::create` dengan `business_id` sama dengan id pemilik, `status = approved`, plus `actingAs` petugas.

Payload pencatatan:

```php
[
    'follow_up_id' => $followUp->id,
    'title' => 'Pendampingan foto produk',
    'held_on' => '2026-11-21',
    'description' => 'Praktik foto produk bersama pemilik.',
]
```

| Metode | Susunan | Yang dinyatakan |
|---|---|---|
| `test_pending_follow_up_cannot_be_recorded_as_coaching` | `FollowUp` dengan `status = pending`. `postJson` ke `petugas.catat-pembinaan.store`. | `assertUnprocessable()`. `assertJsonValidationErrors('follow_up_id')`. Pesan: `Pembinaan hanya dapat dicatat dari tindak lanjut yang sudah disetujui kepala desa.` `assertDatabaseCount('coaching_sessions', 0)`. |
| `test_rejected_follow_up_cannot_be_recorded_as_coaching` | Sama, dengan `status = rejected`. | Pesan yang sama. Tidak ada baris `coaching_sessions`. |
| `test_a_follow_up_accepts_only_one_coaching_session` | Tindak lanjut `approved` yang sudah punya satu `CoachingSession`. Kirim sesi kedua. | `assertJsonValidationErrors('follow_up_id')`. Pesan: `Tindak lanjut ini sudah memiliki catatan pembinaan.` Jumlah `coaching_sessions` tetap 1. |

`CoachingSession::create` pada tes ketiga membutuhkan `follow_up_id`, `business_id`, `assistance_program_id`, `recorded_by`, `title`, `held_on`, `description`, dan `status`. Salin bentuk array dari tes antrean yang sudah ada di berkas yang sama.
