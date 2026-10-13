import { expect, test } from '@playwright/test';
import { accounts, password } from './auth.js';

test('TC_REG_001 daftar pemilik menuju profil usaha', async ({ page }) => {
    const email = `pemilik.${Date.now()}@tumbuh.test`;

    await page.goto('/register');
    await page.getByLabel('Nama lengkap').fill('Siti Aminah');
    await page.getByLabel('Email').fill(email);
    await page.getByLabel('Nama usaha').fill('Warung Siti');
    await page.getByRole('textbox', { name: 'Kata sandi' }).fill(password);
    await page.getByRole('button', { name: 'Buat akun' }).click();

    await expect(page).toHaveURL(/\/umkm\/profil$/);
    await expect(page.getByText('Lengkapi data usaha ini dulu')).toBeVisible();
    await expect(page.locator('input[value="Warung Siti"]')).toBeVisible();
});

test('TC_REG_002 email yang sudah terdaftar ditolak', async ({ page }) => {
    await page.goto('/register');
    await page.getByLabel('Nama lengkap').fill('Pemilik Lain');
    await page.getByLabel('Email').fill(accounts.owner);
    await page.getByLabel('Nama usaha').fill('Usaha Kembar');
    await page.getByRole('textbox', { name: 'Kata sandi' }).fill(password);
    await page.getByRole('button', { name: 'Buat akun' }).click();

    await expect(page).toHaveURL(/\/register$/);
    await expect(page.getByRole('alert')).toContainText(/email/i);
    await expect(page.getByText('Lengkapi data usaha ini dulu')).toHaveCount(0);
});

test('TC_REG_003 field wajib yang kosong tidak terkirim', async ({ page }) => {
    await page.goto('/register');
    await page.getByRole('button', { name: 'Buat akun' }).click();

    await expect(page).toHaveURL(/\/register$/);
    await expect(page.getByLabel('Nama lengkap')).toHaveJSProperty('validity.valueMissing', true);
});

test('TC_REG_005 peran tidak bisa dipilih sendiri', async ({ page }) => {
    await page.goto('/register');

    await expect(page.getByRole('combobox')).toHaveCount(0);
    await expect(page.getByLabel('Peran')).toHaveCount(0);
    await expect(page.getByRole('link', { name: 'Daftar dengan Google' })).toBeVisible();
});
