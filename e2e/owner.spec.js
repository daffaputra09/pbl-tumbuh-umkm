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

    await page.getByRole('button', { name: 'Tambah Produk' }).click();

    const modal = page.getByRole('dialog');
    await expect(modal).toBeVisible();
    await modal.getByPlaceholder('Contoh: Keripik Singkong Original').fill(name);
    await modal.getByPlaceholder('Contoh: Makanan Ringan').fill('Makanan Ringan');
    await modal.getByRole('button', { name: 'Tambah Produk' }).click();
    await expect(modal).toBeHidden();

    const nameText = page.getByText(name, { exact: true });
    await expect(nameText).toBeVisible();
    const row = nameText.locator('xpath=ancestor::div[contains(@class,"justify-between")][1]');
    await expect(row.getByText('Aktif', { exact: true })).toBeVisible();

    await row.getByRole('button', { name: 'Nonaktifkan' }).click();

    await expect(row.getByText('Nonaktif')).toBeVisible();
});

test('TC_BIZ_003 pemilik menyimpan perubahan profil usaha', async ({ page }) => {
    await login(page, accounts.owner);
    await page.goto('/umkm/profil');

    await page.getByPlaceholder('Ceritakan sedikit tentang usaha ini').fill('Usaha keripik rumahan di Krajan.');
    await page.getByRole('button', { name: 'Simpan Profil Usaha' }).click();

    await expect(page).toHaveURL(/\/umkm\/dashboard$/);
    await expect(page.getByText('Keripik Bu Sari')).toBeVisible();
});

test('TC_PROD_003 pemilik mengubah nama produk', async ({ page }) => {
    const name = `Keripik Edit ${Date.now()}`;
    const renamed = `${name} Asin`;

    await login(page, accounts.owner);
    await page.goto('/umkm/produk');

    await page.getByRole('button', { name: 'Tambah Produk' }).click();

    const createModal = page.getByRole('dialog');
    await expect(createModal).toBeVisible();
    await createModal.getByPlaceholder('Contoh: Keripik Singkong Original').fill(name);
    await createModal.getByPlaceholder('Contoh: Makanan Ringan').fill('Makanan Ringan');
    await createModal.getByRole('button', { name: 'Tambah Produk' }).click();
    await expect(createModal).toBeHidden();

    const nameText = page.getByText(name, { exact: true });
    await expect(nameText).toBeVisible();
    const row = nameText.locator('xpath=ancestor::div[contains(@class,"justify-between")][1]');
    await row.getByRole('button', { name: `Edit produk ${name}` }).click();

    const editModal = page.getByRole('dialog');
    await expect(editModal).toBeVisible();
    await editModal.getByPlaceholder('Contoh: Keripik Singkong Original').fill(renamed);
    await editModal.getByRole('button', { name: 'Simpan Perubahan' }).click();
    await expect(editModal).toBeHidden();

    await expect(page.getByText(renamed, { exact: true })).toBeVisible();
});

test('TC_PROD_002 produk tanpa nama tidak tersimpan', async ({ page }) => {
    await login(page, accounts.owner);
    await page.goto('/umkm/produk');

    await page.getByRole('button', { name: 'Tambah Produk' }).click();

    const modal = page.getByRole('dialog');
    await expect(modal).toBeVisible();

    await modal.getByRole('button', { name: 'Tambah Produk' }).click();

    await expect(modal.getByText('Nama dan kategori produk wajib diisi.')).toBeVisible();
});
