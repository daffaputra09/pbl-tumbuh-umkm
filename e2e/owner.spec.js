import { expect, test } from '@playwright/test';
import { accounts, login } from './auth.js';

test('TC_BIZ_001 profil usaha pemilik menampilkan data yang tersimpan', async ({ page }) => {
    await login(page, accounts.owner);
    await page.goto('/umkm/profil');

    await expect(page.getByRole('heading', { name: 'Profil UMKM' })).toBeVisible();
    await expect(page.locator('input[value="Keripik Bu Sari"]')).toBeVisible();
    await expect(page.locator('select option:checked').first()).toHaveText('Makanan & Minuman');
});

test('TC_DASH_001 dashboard pemilik menampilkan status verifikasi', async ({ page }) => {
    await login(page, accounts.owner);
    await page.goto('/umkm/dashboard');

    await expect(page.getByRole('heading', { name: 'Selamat datang, Pemilik UMKM!' })).toBeVisible();
    await expect(page.getByText('Menunggu Cek').first()).toBeVisible();
    await expect(page.getByText('Keripik Bu Sari')).toBeVisible();
});

test('TC_PROD_001 pemilik menambah produk lalu menonaktifkannya', async ({ page }) => {
    const name = `Keripik Uji ${Date.now()}`;

    await login(page, accounts.owner);
    await page.goto('/umkm/produk');

    await page.getByPlaceholder('Contoh: Keripik Singkong Original').fill(name);
    await page.getByPlaceholder('Contoh: Makanan Ringan').fill('Makanan Ringan');
    await page.getByRole('button', { name: 'Tambah Produk' }).click();

    const nameText = page.getByText(name, { exact: true });
    await expect(nameText).toBeVisible();
    const row = nameText.locator('xpath=ancestor::div[contains(@class,"justify-between")][1]');
    await expect(row.getByText('Aktif', { exact: true })).toBeVisible();

    await row.getByRole('button', { name: 'Nonaktifkan' }).click();

    await expect(row.getByText('Nonaktif')).toBeVisible();
});

test('TC_PROD_002 produk tanpa nama tidak tersimpan', async ({ page }) => {
    await login(page, accounts.owner);
    await page.goto('/umkm/produk');

    await page.getByRole('button', { name: 'Tambah Produk' }).click();

    await expect(page.getByText('Nama dan kategori produk wajib diisi.')).toBeVisible();
});
