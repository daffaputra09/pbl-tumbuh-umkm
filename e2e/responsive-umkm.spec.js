import { expect, test } from '@playwright/test';
import { accounts, login } from './auth.js';
import { createOwner } from './support/helpers.js';

const viewports = [
    { name: 'Mobile kecil', width: 320, height: 720 },
    { name: 'Mobile', width: 360, height: 800 },
    { name: 'Mobile standard', width: 390, height: 844 },
    { name: 'Mobile besar', width: 430, height: 932 },
    { name: 'Tablet', width: 768, height: 1024 },
    { name: 'Desktop', width: 1280, height: 800 },
    { name: 'Desktop besar', width: 1440, height: 900 },
];

async function assertNoHorizontalOverflow(page, contextMsg) {
    const overflow = await page.evaluate(() => {
        const docWidth = document.documentElement.clientWidth;
        const scrollWidth = document.documentElement.scrollWidth;
        const bodyScrollWidth = document.body ? document.body.scrollWidth : 0;
        return {
            docWidth,
            scrollWidth,
            bodyScrollWidth,
            hasOverflow: scrollWidth > docWidth + 1 || bodyScrollWidth > docWidth + 1,
        };
    });

    expect(
        overflow.hasOverflow,
        `Horizontal overflow terdeteksi pada ${contextMsg}: docWidth=${overflow.docWidth}, scrollWidth=${overflow.scrollWidth}, bodyScrollWidth=${overflow.bodyScrollWidth}`
    ).toBe(false);
}

test.describe('Responsive Testing Pelaku UMKM', () => {
    for (const vp of viewports) {
        test(`Viewport ${vp.name} (${vp.width}x${vp.height}) - Header, Dashboard, Profil, Produk, Kebutuhan`, async ({ page }) => {
            await page.setViewportSize({ width: vp.width, height: vp.height });

            // Gunakan owner verified dengan data lengkap
            const owner = createOwner(`resp_${vp.width}`, 'Usaha Uji Responsive', { verified: true });
            await login(page, owner.email);

            // 1. Test Dashboard Pelaku UMKM
            await page.goto('/umkm/dashboard');
            await expect(page.getByRole('heading', { name: /Selamat datang/i })).toBeVisible();
            await assertNoHorizontalOverflow(page, `Dashboard pada ${vp.name}`);

            // Cek elemen header
            const logoutButton = page.locator('header').getByRole('button', { name: 'Keluar' });
            await expect(logoutButton).toBeVisible();

            if (vp.width < 1024) {
                const hamburger = page.locator('#open-nav');
                await expect(hamburger).toBeVisible();

                // Test interaksi buka tutup navigasi
                await hamburger.click();
                const mobileNav = page.locator('#mobile-nav');
                await expect(mobileNav).toBeVisible();
                await page.locator('#mobile-nav aside button[data-close-nav]').click();
                await expect(mobileNav).toBeHidden();
            }

            // 2. Test Halaman Kelola Produk
            await page.goto('/umkm/produk');
            await expect(page.getByRole('heading', { name: 'Produk Usahamu' })).toBeVisible();
            await assertNoHorizontalOverflow(page, `Produk pada ${vp.name}`);

            const tambahBtn = page.getByRole('button', { name: 'Tambah Produk' });
            await expect(tambahBtn).toBeVisible();

            // Tambah satu produk untuk mengecek card produk
            const productName = `Produk VP ${vp.width}`;
            await tambahBtn.click();
            const modal = page.getByRole('dialog');
            await expect(modal).toBeVisible();
            await modal.getByPlaceholder('Contoh: Keripik Singkong Original').fill(productName);
            await modal.getByPlaceholder('Contoh: Makanan Ringan').fill('Camilan');
            await modal.getByRole('button', { name: 'Tambah Produk' }).click();
            await expect(modal).toBeHidden();

            const itemText = page.getByText(productName, { exact: true });
            await expect(itemText).toBeVisible();
            const card = itemText.locator('xpath=ancestor::div[contains(@class,"justify-between")][1]');
            await expect(card.getByText('Aktif', { exact: true })).toBeVisible();
            await expect(card.getByRole('button', { name: 'Nonaktifkan' })).toBeVisible();
            await expect(card.getByRole('button', { name: `Edit produk ${productName}` })).toBeVisible();
            await assertNoHorizontalOverflow(page, `Produk dengan Item pada ${vp.name}`);

            // 3. Test Halaman Profil Usaha
            await page.goto('/umkm/profil');
            await expect(page.getByRole('heading', { name: 'Profil UMKM' })).toBeVisible();
            await assertNoHorizontalOverflow(page, `Profil Usaha pada ${vp.name}`);
            const simpanProfilBtn = page.getByRole('button', { name: 'Simpan Profil Usaha' });
            await expect(simpanProfilBtn).toBeVisible();

            // 4. Test Halaman Kebutuhan dan Kendala
            await page.goto('/umkm/kebutuhan');
            await expect(page.getByRole('heading', { name: /Ceritakan Kesulitan Usahamu|Ringkasan Jawabanmu/i })).toBeVisible();
            await assertNoHorizontalOverflow(page, `Kebutuhan pada ${vp.name}`);
        });
    }
});
