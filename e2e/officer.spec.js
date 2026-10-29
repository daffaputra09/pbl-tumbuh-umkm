import { expect, test } from '@playwright/test';
import { accounts, login } from './auth.js';

test('TC_LIST_002 petugas mencari UMKM yang terdaftar', async ({ page }) => {
    await login(page, accounts.officer);
    await page.goto('/petugas/umkm');

    await page.getByPlaceholder('Cari nama usaha, nama pemilik, atau kontak…').fill('Keripik Bu Sari');
    await page.getByPlaceholder('Cari nama usaha, nama pemilik, atau kontak…').press('Tab');

    await expect(page.getByText('Keripik Bu Sari')).toBeVisible();
});

test('TC_BIZ_008 halaman pendataan petugas terbuka', async ({ page }) => {
    await login(page, accounts.officer);
    await page.goto('/petugas/umkm/pendataan-umkm');

    await expect(page.getByRole('heading', { name: 'Data Usaha UMKM' })).toBeVisible();
    await expect(page.getByRole('button', { name: 'Simpan Data UMKM' })).toBeVisible();
});

test('TC_TYPE_001 dan TC_TYPE_003 jenis usaha awal tampil dan jenis baru bisa ditambah', async ({ page }) => {
    const name = `Perikanan ${Date.now()}`;

    await login(page, accounts.officer);
    await page.goto('/petugas/jenis-usaha');

    await expect(page.getByRole('heading', { name: 'Kelola Jenis Usaha' })).toBeVisible();
    await expect(page.getByText('Makanan & Minuman')).toBeVisible();

    await page.getByRole('button', { name: 'Tambah Jenis Usaha' }).click();
    await page.getByPlaceholder('Contoh: Perikanan').fill(name);
    await page.getByRole('button', { name: 'Tambah', exact: true }).click();

    await expect(page.getByText(name)).toBeVisible();
});

test('TC_CAT_001 lima kategori kendala awal tampil', async ({ page }) => {
    await login(page, accounts.officer);
    await page.goto('/petugas/kategori-kendala');

    await expect(page.getByRole('heading', { name: 'Kategori Kendala' })).toBeVisible();
    for (const name of ['Modal', 'Pemasaran', 'Legalitas', 'Produksi', 'Digitalisasi']) {
        await expect(page.getByText(name).first()).toBeVisible();
    }
});

test('TC_Q_010 bank soal menampilkan pertanyaan awal', async ({ page }) => {
    await login(page, accounts.officer);
    await page.goto('/petugas/bank-soal');

    await expect(page.getByRole('heading', { name: 'Bank Soal Kuesioner' })).toBeVisible();
    await expect(page.getByText('Saya kesulitan menyediakan modal untuk mengembangkan usaha.')).toBeVisible();
});

test('TC_DASH_003 dashboard petugas menampilkan ringkasan', async ({ page }) => {
    await login(page, accounts.officer);
    await page.goto('/dashboard');

    await expect(page.getByText('Ringkasan UMKM')).toBeVisible();
});

test('TC_GRP_001 pengelompokan terbuka untuk petugas', async ({ page }) => {
    await login(page, accounts.officer);
    await page.goto('/petugas/pengelompokan');

    await expect(page.getByRole('heading', { name: 'Pengelompokan' })).toBeVisible();
    await expect(page.getByRole('columnheader', { name: 'Kendala utama' })).toBeVisible();
});

test('TC_REC_006 petugas membuka daftar rekomendasi', async ({ page }) => {
    await login(page, accounts.officer);
    await page.goto('/petugas/rekomendasi');

    await expect(page.getByRole('heading', { name: 'Rekomendasi' })).toBeVisible();
});

test('TC_TYPE_002 jenis usaha baru bisa dinonaktifkan', async ({ page }) => {
    const name = `Perikanan ${Date.now()}`;

    await login(page, accounts.officer);
    await page.goto('/petugas/jenis-usaha');
    await page.getByRole('button', { name: 'Tambah Jenis Usaha' }).click();
    await page.getByPlaceholder('Contoh: Perikanan').fill(name);
    await page.getByRole('button', { name: 'Tambah', exact: true }).click();

    const row = page.locator('div.rounded-xl.border').filter({ hasText: name });
    await row.getByRole('button', { name: 'Nonaktifkan' }).click();

    await expect(row.getByText('Nonaktif', { exact: true })).toBeVisible();
});

test('TC_TYPE_003 pencarian jenis usaha menyaring data berdasarkan nama secara case-insensitive', async ({ page }) => {
    await login(page, accounts.officer);
    await page.goto('/petugas/jenis-usaha');

    const searchInput = page.getByPlaceholder('Cari jenis usaha...');
    await searchInput.fill('makanan');

    await expect(page.getByText('Makanan & Minuman')).toBeVisible();
    await expect(page.getByText('Kerajinan Tangan')).toHaveCount(0);

    // Case-insensitive test
    await searchInput.fill('MAKANAN');
    await expect(page.getByText('Makanan & Minuman')).toBeVisible();
});

test('TC_TYPE_004 pencarian tanpa hasil menampilkan pesan informatif', async ({ page }) => {
    await login(page, accounts.officer);
    await page.goto('/petugas/jenis-usaha');

    const searchInput = page.getByPlaceholder('Cari jenis usaha...');
    await searchInput.fill('jenisusahaxxx999');

    await expect(page.getByText('Tidak ada jenis usaha yang cocok dengan pencarianmu.')).toBeVisible();
    await expect(page.getByText('Makanan & Minuman')).toHaveCount(0);
});

test('TC_TYPE_005 mengosongkan kolom pencarian menampilkan seluruh data kembali', async ({ page }) => {
    await login(page, accounts.officer);
    await page.goto('/petugas/jenis-usaha');

    const searchInput = page.getByPlaceholder('Cari jenis usaha...');
    await searchInput.fill('makanan');
    await expect(page.getByText('Makanan & Minuman')).toBeVisible();
    await expect(page.getByText('Kerajinan Tangan')).toHaveCount(0);

    await searchInput.fill('');
    await expect(page.getByText('Makanan & Minuman')).toBeVisible();
    await expect(page.getByText('Kerajinan Tangan')).toBeVisible();
});

test('TC_TYPE_006 validasi form tambah berjalan dan popup tetap terbuka jika input tidak valid', async ({ page }) => {
    await login(page, accounts.officer);
    await page.goto('/petugas/jenis-usaha');

    await page.getByRole('button', { name: 'Tambah Jenis Usaha' }).click();

    const modal = page.getByRole('dialog');
    await expect(modal).toBeVisible();

    // 1. Submit kosong
    await modal.getByRole('button', { name: 'Tambah', exact: true }).click();
    await expect(modal.getByRole('alert')).toContainText('Nama jenis usaha wajib diisi.');
    await expect(modal).toBeVisible();

    // 2. Submit nama duplikat yang sudah ada
    await modal.getByPlaceholder('Contoh: Perikanan').fill('Makanan & Minuman');
    await modal.getByRole('button', { name: 'Tambah', exact: true }).click();
    await expect(modal.getByRole('alert')).toBeVisible();
    await expect(modal).toBeVisible();

    // 3. Tombol Batal menutup popup
    await modal.getByRole('button', { name: 'Batal' }).click();
    await expect(modal).toHaveCount(0);
});

test('TC_TYPE_007 petugas berhasil mengedit jenis usaha dan data berubah', async ({ page }) => {
    const originalName = `Usaha Baru ${Date.now()}`;
    const updatedName = `Usaha Diedit ${Date.now()}`;

    await login(page, accounts.officer);
    await page.goto('/petugas/jenis-usaha');

    // Buat data baru
    await page.getByRole('button', { name: 'Tambah Jenis Usaha' }).click();
    await page.getByPlaceholder('Contoh: Perikanan').fill(originalName);
    await page.getByRole('button', { name: 'Tambah', exact: true }).click();
    await expect(page.getByText(originalName)).toBeVisible();

    // Klik Edit pada data yang dipilih
    const row = page.locator('div.rounded-xl.border').filter({ hasText: originalName });
    await row.getByRole('button', { name: 'Edit' }).click();

    const modal = page.getByRole('dialog');
    await expect(modal).toBeVisible();
    await expect(modal.getByRole('heading', { name: 'Edit Jenis Usaha' })).toBeVisible();
    await expect(modal.getByPlaceholder('Contoh: Perikanan')).toHaveValue(originalName);

    // Edit nama dan simpan
    await modal.getByPlaceholder('Contoh: Perikanan').fill(updatedName);
    await modal.getByRole('button', { name: 'Simpan Perubahan' }).click();

    // Popup tertutup dan data berubah
    await expect(modal).toHaveCount(0);
    await expect(page.getByText('Jenis usaha berhasil diperbarui.')).toBeVisible();
    await expect(page.getByText(updatedName)).toBeVisible();
    await expect(page.getByText(originalName)).toHaveCount(0);
});

test('TC_TYPE_008 pembatalan popup edit tidak menyimpan perubahan data', async ({ page }) => {
    const name = `Usaha Tetap ${Date.now()}`;

    await login(page, accounts.officer);
    await page.goto('/petugas/jenis-usaha');

    // Buat data baru
    await page.getByRole('button', { name: 'Tambah Jenis Usaha' }).click();
    await page.getByPlaceholder('Contoh: Perikanan').fill(name);
    await page.getByRole('button', { name: 'Tambah', exact: true }).click();
    await expect(page.getByText(name)).toBeVisible();

    // Buka Edit lalu batalkan
    const row = page.locator('div.rounded-xl.border').filter({ hasText: name });
    await row.getByRole('button', { name: 'Edit' }).click();

    const modal = page.getByRole('dialog');
    await expect(modal).toBeVisible();
    await modal.getByPlaceholder('Contoh: Perikanan').fill('Tidak Boleh Tersimpan');
    await modal.getByRole('button', { name: 'Batal' }).click();

    await expect(modal).toHaveCount(0);
    await expect(page.getByText(name)).toBeVisible();
    await expect(page.getByText('Tidak Boleh Tersimpan')).toHaveCount(0);
});

test('TC_TYPE_009 dialog konfirmasi hapus berfungsi dan jenis usaha dapat dihapus', async ({ page }) => {
    const name = `Usaha Hapus ${Date.now()}`;

    await login(page, accounts.officer);
    await page.goto('/petugas/jenis-usaha');

    // Buat data baru yang belum terhubung dengan UMKM
    await page.getByRole('button', { name: 'Tambah Jenis Usaha' }).click();
    await page.getByPlaceholder('Contoh: Perikanan').fill(name);
    await page.getByRole('button', { name: 'Tambah', exact: true }).click();
    await expect(page.getByText(name)).toBeVisible();

    // Klik tombol hapus
    const row = page.locator('div.rounded-xl.border').filter({ hasText: name });
    await row.getByRole('button', { name: `Hapus ${name}` }).click();

    // Dialog konfirmasi muncul
    const dialog = page.getByRole('dialog');
    await expect(dialog).toBeVisible();
    await expect(dialog.getByRole('heading', { name: 'Hapus Jenis Usaha' })).toBeVisible();

    // Uji tombol batal tidak menghapus data
    await dialog.getByRole('button', { name: 'Batal' }).click();
    await expect(dialog).toHaveCount(0);
    await expect(page.getByText(name)).toBeVisible();

    // Buka kembali dialog hapus lalu konfirmasi hapus
    await row.getByRole('button', { name: `Hapus ${name}` }).click();
    const confirmDialog = page.getByRole('dialog');
    await expect(confirmDialog).toBeVisible();
    await confirmDialog.getByRole('button', { name: 'Hapus', exact: true }).click();

    await expect(confirmDialog).toHaveCount(0);
    await expect(page.getByText('Jenis usaha berhasil dihapus.')).toBeVisible();
    await expect(page.getByText(name)).toHaveCount(0);
});

test('TC_TYPE_010 jenis usaha yang berelasi dengan UMKM tidak dapat dihapus dan ditangani dengan benar', async ({ page }) => {
    await login(page, accounts.officer);
    await page.goto('/petugas/jenis-usaha');

    // "Makanan & Minuman" terhubung dengan data UMKM "Keripik Bu Sari"
    const row = page.locator('div.rounded-xl.border').filter({ hasText: 'Makanan & Minuman' });
    await expect(row).toBeVisible();

    // Buka dialog konfirmasi hapus
    await row.getByRole('button', { name: 'Hapus' }).click();

    const dialog = page.getByRole('dialog');
    await expect(dialog).toBeVisible();
    await expect(dialog.getByText('Perhatian')).toBeVisible();

    // Klik tombol Hapus dalam dialog konfirmasi
    await dialog.getByRole('button', { name: 'Hapus', exact: true }).click();

    // Menampilkan pesan error penolakan dan data tidak terhapus
    await expect(dialog.getByRole('alert')).toContainText('masih digunakan oleh data UMKM');

    // Tutup dialog
    await dialog.getByRole('button', { name: 'Batal' }).click();
    await expect(dialog).toHaveCount(0);

    // Data Makanan & Minuman tetap ada
    await expect(page.getByText('Makanan & Minuman')).toBeVisible();
});

test('TC_TYPE_011 status jenis usaha dapat diaktifkan kembali setelah dinonaktifkan', async ({ page }) => {
    const name = `Usaha Reaktif ${Date.now()}`;

    await login(page, accounts.officer);
    await page.goto('/petugas/jenis-usaha');

    await page.getByRole('button', { name: 'Tambah Jenis Usaha' }).click();
    await page.getByPlaceholder('Contoh: Perikanan').fill(name);
    await page.getByRole('button', { name: 'Tambah', exact: true }).click();

    const row = page.locator('div.rounded-xl.border').filter({ hasText: name });
    await row.getByRole('button', { name: 'Nonaktifkan' }).click();
    await expect(row.getByText('Nonaktif', { exact: true })).toBeVisible();

    await row.getByRole('button', { name: 'Aktifkan' }).click();
    await expect(row.getByText('Aktif', { exact: true })).toBeVisible();
});

test('TC_TYPE_012 pemilik UMKM ditolak dari halaman kelola jenis usaha', async ({ page }) => {
    await login(page, accounts.owner);
    const ownerRes = await page.goto('/petugas/jenis-usaha');
    expect(ownerRes.status()).toBe(403);
});

test('TC_TYPE_013 kepala desa ditolak dari halaman kelola jenis usaha', async ({ page }) => {
    await login(page, accounts.villageHead);
    const headRes = await page.goto('/petugas/jenis-usaha');
    expect(headRes.status()).toBe(403);
});

test('TC_CAT_002 petugas menambah kategori kendala', async ({ page }) => {
    const name = `Kendala ${Date.now()}`;

    await login(page, accounts.officer);
    await page.goto('/petugas/kategori-kendala');
    await page.getByRole('button', { name: 'Tambah Kategori' }).click();
    await page.locator('#create_name').fill(name);
    await page.locator('#create_slug').fill(`kendala-${Date.now()}`);
    await page.getByRole('button', { name: 'Simpan Kategori' }).click();

    await expect(page.getByText('Kategori kendala berhasil ditambahkan.')).toBeVisible();
    await expect(page.getByText(name)).toBeVisible();
});

test('TC_Q_001 petugas menambah pertanyaan likert', async ({ page }) => {
    const prompt = `Pertanyaan uji ${Date.now()}`;

    await login(page, accounts.officer);
    await page.goto('/petugas/bank-soal');
    await page.getByRole('button', { name: 'Tambah Pertanyaan' }).click();
    await page.locator('#create_category_id').selectOption({ label: 'Modal' });
    await page.locator('#create_prompt').fill(prompt);
    await page.getByRole('button', { name: 'Simpan Pertanyaan' }).click();

    await expect(page.getByText('Pertanyaan berhasil ditambahkan ke Bank Soal.')).toBeVisible();
    await expect(page.getByText(prompt)).toBeVisible();
});

test('TC_PROG_001 petugas membuat program draf lalu membuka detail', async ({ page }) => {
    const programName = `Program Detail ${Date.now()}`;

    await login(page, accounts.officer);
    await page.goto('/petugas/program-bantuan/create');
    await page.locator('#name').fill(programName);
    await page.locator('#provider').fill('Desa Tumbuh');
    await page.locator('#description').fill('Program untuk melihat halaman detail.');
    await page.locator('#status').selectOption('draft');
    await page.getByRole('checkbox', { name: 'Modal' }).check();
    await page.getByRole('button', { name: 'Simpan Program Bantuan' }).click();
    await expect(page.getByText(programName)).toBeVisible();

    await page.getByRole('row').filter({ hasText: programName }).getByRole('link', { name: 'Lihat Detail Program' }).click();

    await expect(page.getByRole('heading', { name: programName })).toBeVisible();
});

test('TC_BIZ_005 dan TC_VER_002 petugas mendata usaha lalu memverifikasi', async ({ page }) => {
    const businessName = `Warung Uji ${Date.now()}`;

    await login(page, accounts.officer);
    await page.goto('/petugas/umkm/pendataan-umkm');
    await page.getByRole('combobox').selectOption({ label: 'Jasa' });
    await page.getByPlaceholder('Contoh: Warung Nasi Padang').fill(businessName);
    await page.getByPlaceholder('Nama lengkap pemilik usaha').fill('Bu Warung');
    await page.getByPlaceholder('08xxxxxxxxxx').fill('081234009988');
    await page.getByPlaceholder('Alamat lengkap tempat usaha beroperasi').fill('Jl. Melati No. 2');
    await page.getByPlaceholder('Nama dusun').fill('Krajan');
    await page.getByRole('button', { name: /Pilih tahun/ }).click();
    await page.getByRole('button', { name: '«' }).click();
    await page.getByRole('button', { name: '2018', exact: true }).click();
    await page.getByPlaceholder('Contoh: 3').fill('2');
    await page.getByRole('button', { name: 'Simpan Data UMKM' }).click();

    await expect(page.getByText('Data usaha berhasil disimpan.')).toBeVisible();

    await page.goto('/petugas/umkm');
    await page.getByPlaceholder('Cari nama usaha, nama pemilik, atau kontak…').fill(businessName);
    await page.getByPlaceholder('Cari nama usaha, nama pemilik, atau kontak…').press('Tab');
    await page.getByRole('link', { name: 'Detail' }).click();
    await page.getByRole('button', { name: 'Simpan Keputusan Verifikasi' }).click();

    await expect(page.getByText(`Status verifikasi UMKM "${businessName}" berhasil diubah menjadi diverifikasi.`)).toBeVisible();
});

test('TC_PROD_004 petugas tidak dapat mengakses endpoint pengelolaan produk', async ({ page }) => {
    await login(page, accounts.officer);

    // Petugas ditolak dari halaman pengelolaan produk UMKM (403 Forbidden)
    const ownerProductResponse = await page.goto('/umkm/produk');
    expect(ownerProductResponse.status()).toBe(403);

    // Endpoint produk khusus petugas tidak tersedia (404 Not Found)
    const officerProductResponse = await page.goto('/petugas/umkm/1/produk');
    expect(officerProductResponse.status()).toBe(404);
});
