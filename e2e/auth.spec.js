import { expect, test } from '@playwright/test';
import { accounts, desktopMenu, login } from './auth.js';

test('TC_AUTH_001 masuk sebagai pemilik UMKM', async ({ page }) => {
    await login(page, accounts.owner);

    await expect(page).toHaveURL(/\/umkm\/dashboard$/);
    await expect(page.getByText('Dashboard Usaha').first()).toBeVisible();

    const menu = desktopMenu(page);
    await expect(menu.getByRole('link', { name: 'Profil usaha' })).toBeVisible();
    await expect(menu.getByRole('link', { name: 'Pendataan UMKM' })).toHaveCount(0);
    await expect(menu.getByRole('link', { name: 'Persetujuan tindak lanjut' })).toHaveCount(0);
});

test('TC_AUTH_002 masuk sebagai petugas desa', async ({ page }) => {
    await login(page, accounts.officer);

    await expect(page).toHaveURL(/\/dashboard$/);
    await expect(page.getByText('Dashboard Petugas').first()).toBeVisible();
    await expect(desktopMenu(page).getByRole('link', { name: 'Data UMKM' })).toBeVisible();
    await expect(desktopMenu(page).getByRole('link', { name: 'Pendataan UMKM' })).toBeVisible();
});

test('TC_AUTH_003 masuk sebagai kepala desa', async ({ page }) => {
    await login(page, accounts.villageHead);

    await expect(page).toHaveURL(/\/dashboard$/);
    await expect(page.getByText('Dashboard Kepala Desa').first()).toBeVisible();
    await expect(desktopMenu(page).getByRole('link', { name: 'Persetujuan tindak lanjut' })).toBeVisible();
    await expect(desktopMenu(page).getByRole('link', { name: 'Pendataan UMKM' })).toHaveCount(0);
});

test('TC_AUTH_010 masuk dengan nama pengguna', async ({ page }) => {
    await login(page, 'Petugas Desa');

    await expect(page).toHaveURL(/\/dashboard$/);
    await expect(page.getByText('Dashboard Petugas').first()).toBeVisible();
});

test('TC_AUTH_004 email benar dan kata sandi salah', async ({ page }) => {
    await login(page, accounts.owner, 'Salah123');

    await expect(page).toHaveURL(/\/login$/);
    await expect(page.getByRole('alert')).toContainText('Email/Username atau kata sandi salah.');
    await expect(page.getByText('Dashboard Usaha')).toHaveCount(0);
});

test('TC_AUTH_005 email belum terdaftar', async ({ page }) => {
    await login(page, accounts.unknown);

    await expect(page).toHaveURL(/\/login$/);
    await expect(page.getByRole('alert')).toContainText('Email/Username atau kata sandi salah.');
    await expect(page.getByText(accounts.unknown)).toHaveCount(0);
});

test('TC_AUTH_006 email atau kata sandi dikosongkan', async ({ page }) => {
    await page.goto('/login');
    await page.getByRole('button', { name: 'Masuk' }).click();

    await expect(page).toHaveURL(/\/login$/);
    await expect(page.getByLabel('Email atau nama pengguna')).toHaveJSProperty('validity.valueMissing', true);

    await page.getByLabel('Email atau nama pengguna').fill(accounts.owner);
    await page.getByRole('button', { name: 'Masuk' }).click();

    await expect(page).toHaveURL(/\/login$/);
    await expect(page.getByRole('textbox', { name: 'Kata sandi' })).toHaveJSProperty('validity.valueMissing', true);
});

test('TC_AUTH_007 akun nonaktif tidak dapat masuk', async ({ page }) => {
    await login(page, accounts.inactive);

    await expect(page).toHaveURL(/\/login$/);
    await expect(page.getByRole('alert')).toContainText('Akun ini tidak aktif.');
    await expect(page.getByText('Dashboard Usaha')).toHaveCount(0);
});

test('TC_AUTH_008 keluar dari akun', async ({ page }) => {
    await login(page, accounts.owner);
    await expect(page).toHaveURL(/\/umkm\/dashboard$/);

    await page.getByRole('banner').getByRole('button', { name: 'Keluar' }).click();

    await expect(page).toHaveURL(/\/$/);
    await page.goto('/umkm/dashboard');
    await expect(page).toHaveURL(/\/login$/);
    await expect(page.getByRole('heading', { name: 'Masuk' })).toBeVisible();
    await expect(page.getByText('Pemilik UMKM')).toHaveCount(0);
});

test('TC_AUTH_009 halaman dalam tidak dapat dibuka tanpa masuk', async ({ page }) => {
    for (const path of ['/umkm/dashboard', '/petugas/dashboard', '/pimpinan/dashboard']) {
        await page.goto(path);
        await expect(page).toHaveURL(/\/login$/);
        await expect(page.getByRole('heading', { name: 'Masuk' })).toBeVisible();
    }

    await expect(page.getByText('Keripik Bu Sari')).toHaveCount(0);
});
