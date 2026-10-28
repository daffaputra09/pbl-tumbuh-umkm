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

    await page.getByPlaceholder('Contoh: Perikanan').fill(name);
    await page.getByRole('button', { name: 'Tambah' }).click();

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
    await page.getByPlaceholder('Contoh: Perikanan').fill(name);
    await page.getByRole('button', { name: 'Tambah' }).click();

    const row = page.locator('div.rounded-xl.border').filter({ hasText: name });
    await row.getByRole('button', { name: 'Nonaktifkan' }).click();

    await expect(row.getByText('Nonaktif', { exact: true })).toBeVisible();
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
