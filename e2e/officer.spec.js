import { expect, test } from '@playwright/test';
import { accounts, login } from './auth.js';

test('TC_LIST_001 petugas mencari UMKM yang terdaftar', async ({ page }) => {
    await login(page, accounts.officer);
    await page.goto('/petugas/umkm');

    await page.getByPlaceholder('Cari nama usaha, nama pemilik, atau kontak…').fill('Keripik Bu Sari');
    await page.getByPlaceholder('Cari nama usaha, nama pemilik, atau kontak…').press('Tab');

    await expect(page.getByText('Keripik Bu Sari')).toBeVisible();
});

test('TC_BIZ_002 halaman pendataan petugas terbuka', async ({ page }) => {
    await login(page, accounts.officer);
    await page.goto('/petugas/umkm/pendataan-umkm');

    await expect(page.getByRole('heading', { name: 'Data Usaha UMKM' })).toBeVisible();
    await expect(page.getByRole('button', { name: 'Simpan Data UMKM' })).toBeVisible();
});

test('TC_TYPE_001 jenis usaha awal tampil dan jenis baru bisa ditambah', async ({ page }) => {
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

test('TC_Q_001 bank soal menampilkan pertanyaan awal', async ({ page }) => {
    await login(page, accounts.officer);
    await page.goto('/petugas/bank-soal');

    await expect(page.getByRole('heading', { name: 'Bank Soal Kuesioner' })).toBeVisible();
    await expect(page.getByText('Saya kesulitan menyediakan modal untuk mengembangkan usaha.')).toBeVisible();
});

test('TC_DASH_002 dashboard petugas menampilkan ringkasan', async ({ page }) => {
    await login(page, accounts.officer);
    await page.goto('/dashboard');

    await expect(page.getByText('Ringkasan UMKM')).toBeVisible();
});

test('TC_DASH_003 pengelompokan terbuka untuk petugas', async ({ page }) => {
    await login(page, accounts.officer);
    await page.goto('/petugas/pengelompokan');

    await expect(page.getByRole('heading', { name: 'Pengelompokan' })).toBeVisible();
    await expect(page.getByRole('columnheader', { name: 'Kendala utama' })).toBeVisible();
});
