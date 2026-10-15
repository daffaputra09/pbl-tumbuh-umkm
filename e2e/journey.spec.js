import { expect, test } from '@playwright/test';
import { acceptDialogs, accounts, login } from './auth.js';

const programName = `Bantuan Modal E2E ${Date.now()}`;

test.describe.serial('jalur kuesioner sampai pembinaan', () => {
    test('TC_PROG_002 petugas membuat program bantuan aktif', async ({ page }) => {
        await login(page, accounts.officer);
        await page.goto('/petugas/program-bantuan/create');

        await page.locator('#name').fill(programName);
        await page.locator('#provider').fill('Desa Tumbuh');
        await page.locator('#description').fill('Bantuan modal untuk usaha dengan kendala modal tinggi.');
        await page.locator('#status').selectOption('active');
        await page.getByRole('checkbox', { name: 'Modal' }).check();
        await page.getByRole('button', { name: 'Simpan Program Bantuan' }).click();

        await expect(page.getByText('Program bantuan berhasil ditambahkan.')).toBeVisible();
        await expect(page.getByText(programName)).toBeVisible();
    });

    test('TC_ASM_003 pemilik menyelesaikan kuesioner', async ({ page }) => {
        await login(page, accounts.owner);
        await page.goto('/umkm/kebutuhan');

        await expect(page.getByRole('heading', { name: 'Ceritakan Kesulitan Usahamu' })).toBeVisible();
        await page.getByRole('button', { name: 'Ya, ada' }).click();

        const highScores = page.getByRole('button', { name: '5', exact: true });
        await expect(highScores).toHaveCount(4);
        for (let index = 0; index < 4; index += 1) {
            await highScores.nth(index).click();
        }

        await page.getByRole('button', { name: 'Lanjut' }).click();

        for (let index = 0; index < 4; index += 1) {
            await page.getByRole('button', { name: 'Tidak', exact: true }).click();
        }

        await page.getByRole('button', { name: 'Kirim Jawaban' }).click();

        await expect(page).toHaveURL(/\/umkm\/dashboard$/);
    });

    test('TC_SCORE_001 dan TC_REC_005 pemilik melihat program yang cocok', async ({ page }) => {
        await login(page, accounts.owner);
        await page.goto('/umkm/rekomendasi');

        await expect(page.getByRole('heading', { name: 'Rekomendasi program' })).toBeVisible();
        await expect(page.getByText(programName)).toBeVisible();
        await expect(page.getByText('Modal').first()).toBeVisible();
    });

    test('TC_REC_002 pemilik mendaftarkan minat', async ({ page }) => {
        await login(page, accounts.owner);
        await page.goto('/umkm/rekomendasi');

        const row = page.getByRole('row').filter({ hasText: programName });
        await row.getByRole('button', { name: 'Daftarkan minat' }).click();

        await expect(page.getByText('Minat sudah didaftarkan.').first()).toBeVisible();
    });

    test('TC_FU_001 petugas mengajukan tindak lanjut', async ({ page }) => {
        acceptDialogs(page);
        await login(page, accounts.officer);
        await page.goto('/petugas/tindak-lanjut');

        const row = page.getByRole('row').filter({ hasText: programName });
        await row.getByRole('button', { name: 'Buat Pengajuan' }).click();
        await page.locator('input[name="planned_on"]').fill('2026-11-20');
        await page.getByPlaceholder('Tuliskan catatan atau justifikasi untuk kepala desa...').fill('Pemilik sudah mendaftar minat.');
        await page.getByRole('button', { name: 'Kirim Pengajuan' }).click();

        await expect(row.getByText('Menunggu Persetujuan')).toBeVisible();
    });

    test('TC_FU_003 kepala desa menyetujui pengajuan', async ({ page }) => {
        acceptDialogs(page);
        await login(page, accounts.villageHead);
        await page.goto('/pimpinan/persetujuan-tindak-lanjut');

        const row = page.getByRole('row').filter({ hasText: programName });
        await row.getByRole('button', { name: 'Tinjau Pengajuan' }).click();
        await page.getByRole('button', { name: 'Setujui', exact: true }).click();
        await page.getByPlaceholder('Tambahkan catatan persetujuan...').fill('Disetujui untuk dilaksanakan.');
        await page.getByRole('button', { name: 'Konfirmasi Persetujuan' }).click();

        await expect(row.getByText('Disetujui')).toBeVisible();
    });

    test('TC_COA_001 petugas mencatat pembinaan yang sudah disetujui', async ({ page }) => {
        acceptDialogs(page);
        await login(page, accounts.officer);
        await page.goto('/petugas/catat-pembinaan');

        const row = page.getByRole('row').filter({ hasText: programName });
        await row.getByRole('button', { name: 'Catat Pembinaan' }).click();
        await page.getByPlaceholder('Contoh: Mengikuti pelatihan digital marketing...').fill('Pendampingan modal usaha');
        await page.locator('input[name="held_on"]').fill('2026-11-21');
        await page.getByPlaceholder('Deskripsikan proses pembinaan yang telah dilakukan...').fill('Pemilik menyusun rencana penggunaan modal.');
        await page.getByRole('button', { name: 'Simpan Data Pembinaan' }).click();

        await expect(page.getByRole('row').filter({ hasText: programName })).toHaveCount(0);
    });
});
