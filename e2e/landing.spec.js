import { expect, test } from '@playwright/test';

test('TC_LAND_001 halaman awal menuju masuk', async ({ page }) => {
    await page.goto('/');

    await page.getByRole('link', { name: 'Masuk', exact: true }).first().click();

    await expect(page).toHaveURL(/\/login$/);
    await expect(page.getByRole('heading', { name: 'Masuk' })).toBeVisible();
});

test('TC_LAND_002 halaman awal menuju daftar', async ({ page }) => {
    await page.goto('/');

    await page.getByRole('link', { name: 'Daftarkan Usaha', exact: true }).first().click();

    await expect(page).toHaveURL(/\/register$/);
    await expect(page.getByRole('heading', { name: 'Daftar' })).toBeVisible();
});
