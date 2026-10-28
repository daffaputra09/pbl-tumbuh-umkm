import { expect, test } from '@playwright/test';
import { accounts, login, password } from './auth.js';

test('TC_ACC_001 ubah nomor HP akun', async ({ page }) => {
    await login(page, accounts.owner);
    await page.goto('/akun');

    await page.getByLabel('Nomor HP').fill('081200000001');
    await page.getByRole('button', { name: 'Simpan perubahan' }).click();

    await expect(page.getByRole('status')).toContainText('Profil akun sudah disimpan.');
    await expect(page.getByLabel('Nomor HP')).toHaveValue('081200000001');
});

test('TC_ACC_005 nama akun yang dikosongkan ditolak', async ({ page }) => {
    await login(page, accounts.officer);
    await page.goto('/akun');

    await page.getByLabel('Nama').fill('');
    await page.getByRole('button', { name: 'Simpan perubahan' }).click();

    await expect(page).toHaveURL(/\/akun$/);
    await expect(page.getByLabel('Nama')).toHaveJSProperty('validity.valueMissing', true);
});

test('TC_ACC_002 ganti kata sandi lalu masuk dengan kata sandi baru', async ({ page }) => {
    const email = `sandi.${Date.now()}@tumbuh.test`;
    const nextPassword = 'Rahasia456';

    await page.goto('/register');
    await page.getByLabel('Nama lengkap').fill('Penguji Sandi');
    await page.getByLabel('Email').fill(email);
    await page.getByLabel('Nama usaha').fill('Warung Sandi');
    await page.getByRole('textbox', { name: 'Kata sandi' }).fill(password);
    await page.getByRole('button', { name: 'Buat akun' }).click();
    await expect(page).toHaveURL(/\/umkm\/profil$/);

    await page.goto('/akun');
    await page.getByRole('textbox', { name: 'Kata sandi saat ini' }).fill(password);
    await page.getByRole('textbox', { name: 'Kata sandi baru', exact: true }).fill(nextPassword);
    await page.getByRole('textbox', { name: 'Ulangi kata sandi baru' }).fill(nextPassword);
    await page.getByRole('button', { name: 'Simpan perubahan' }).click();

    await expect(page.getByRole('status')).toContainText('Profil akun sudah disimpan.');

    await page.getByRole('banner').getByRole('button', { name: 'Keluar' }).click();
    await login(page, email, nextPassword);
    await expect(page).toHaveURL(/\/umkm\/profil$/);
});

test('TC_PWD_002 lupa kata sandi untuk email yang tidak terdaftar tetap menjawab sama', async ({ page }) => {
    await page.goto('/forgot-password');
    await page.getByLabel('Email').fill(accounts.unknown);
    await page.getByRole('button', { name: 'Kirim tautan' }).click();

    await expect(page.getByRole('status')).toContainText('Jika email itu terdaftar, tautan atur ulang kata sandi sudah dikirim.');
});

test('TC_SSO_004 tombol Google ada di halaman masuk', async ({ page }) => {
    await page.goto('/login');

    await expect(page.getByRole('link', { name: 'Lanjutkan dengan Google' })).toHaveAttribute('href', /\/auth\/google$/);
});