import { expect, test } from '@playwright/test';
import { accounts, desktopMenu, login } from './auth.js';

test('TC_AUTHZ_001 pemilik ditolak dari halaman petugas', async ({ page }) => {
    await login(page, accounts.owner);

    const response = await page.goto('/petugas/umkm');

    expect(response.status()).toBe(403);
});

test('TC_AUTHZ_003 petugas ditolak dari persetujuan kepala desa', async ({ page }) => {
    await login(page, accounts.officer);

    const response = await page.goto('/pimpinan/persetujuan-tindak-lanjut');

    expect(response.status()).toBe(403);
});

test('TC_AUTHZ_007 kepala desa ditolak dari pendataan petugas', async ({ page }) => {
    await login(page, accounts.villageHead);

    const response = await page.goto('/petugas/umkm/pendataan-umkm');

    expect(response.status()).toBe(403);
});

test('TC_NAV_002 menu petugas memuat pengelolaan yang sudah ada', async ({ page }) => {
    await login(page, accounts.officer);

    const menu = desktopMenu(page);
    for (const label of ['Data UMKM', 'Pendataan UMKM', 'Jenis usaha', 'Kategori kendala', 'Bank soal', 'Program bantuan', 'Ajukan tindak lanjut', 'Pengelompokan', 'Rekomendasi', 'Profil akun']) {
        await expect(menu.getByRole('link', { name: label })).toBeVisible();
    }

    await expect(menu.getByRole('link', { name: 'Laporan' })).toHaveCount(0);
});

test('TC_NAV_003 menu kepala desa memuat pemantauan yang sudah ada', async ({ page }) => {
    await login(page, accounts.villageHead);

    const menu = desktopMenu(page);
    for (const label of ['Pengelompokan', 'Rekomendasi', 'Persetujuan tindak lanjut', 'Riwayat pembinaan', 'Profil akun']) {
        await expect(menu.getByRole('link', { name: label })).toBeVisible();
    }

    await expect(menu.getByRole('link', { name: 'Riwayat pembinaan' })).toHaveAttribute('href', '/pimpinan/riwayat-pembinaan');
    await expect(menu.getByRole('link', { name: 'Data UMKM' })).toHaveCount(0);
    await expect(menu.getByRole('link', { name: 'Laporan' })).toHaveCount(0);
});

test('TC_NAV_001 menu pemilik memuat riwayat pembinaan', async ({ page }) => {
    await login(page, accounts.owner);

    const menu = desktopMenu(page);
    await expect(menu.getByRole('link', { name: 'Riwayat pembinaan' })).toHaveAttribute('href', '/umkm/riwayat-pembinaan');
    await expect(menu.getByRole('link', { name: 'Rekomendasi program' })).toBeVisible();
});
