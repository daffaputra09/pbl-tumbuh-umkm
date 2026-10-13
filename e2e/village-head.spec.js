import { expect, test } from '@playwright/test';
import { accounts, login } from './auth.js';

test('TC_DASH_004 dashboard kepala desa menampilkan ringkasan', async ({ page }) => {
    await login(page, accounts.villageHead);
    await page.goto('/dashboard');

    await expect(page.getByText('Dashboard Kepala Desa').first()).toBeVisible();
    await expect(page.getByText('Ringkasan UMKM')).toBeVisible();
});

test('TC_FU_006 halaman persetujuan terbuka', async ({ page }) => {
    await login(page, accounts.villageHead);
    await page.goto('/pimpinan/persetujuan-tindak-lanjut');

    await expect(page.getByRole('heading', { name: 'Persetujuan Tindak Lanjut' })).toBeVisible();
});

test('TC_REC_008 kepala desa dapat membuka daftar rekomendasi', async ({ page }) => {
    await login(page, accounts.villageHead);
    await page.goto('/pimpinan/rekomendasi');

    await expect(page.getByRole('heading', { name: 'Rekomendasi' })).toBeVisible();
});
