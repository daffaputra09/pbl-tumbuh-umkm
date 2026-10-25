import { expect, test } from '@playwright/test';
import { accounts, login, password } from './auth.js';
import { api, createOwner, fx, latestResetPath, sql, switchTo, uniqueEmail } from './support/helpers.js';

async function registerOwner(page, prefix) {
    const email = uniqueEmail(prefix);

    await page.goto('/register');
    await page.getByLabel('Nama lengkap').fill(`Pemilik ${prefix}`);
    await page.getByLabel('Email').fill(email);
    await page.getByLabel('Nama usaha').fill(`Usaha ${prefix}`);
    await page.getByRole('textbox', { name: 'Kata sandi' }).fill(password);
    await page.getByRole('button', { name: 'Buat akun' }).click();
    await expect(page).toHaveURL(/\/umkm\/profil$/);

    return email;
}

async function requestResetLink(page, email) {
    await page.goto('/forgot-password');
    await page.getByLabel('Email').fill(email);
    await page.getByRole('button', { name: 'Kirim tautan' }).click();
    await expect(page.getByRole('status')).toContainText('Jika email itu terdaftar, tautan atur ulang kata sandi sudah dikirim.');
}

test('TC_AUTH_011 akun yang dinonaktifkan saat sesi berjalan dikeluarkan', async ({ page }) => {
    const email = await registerOwner(page, 'nonaktif');

    fx('deactivate', { email });
    await page.goto('/umkm/profil');

    await expect(page).toHaveURL(/\/login$/);
    await expect(page.getByRole('alert')).toContainText('Akun ini tidak aktif.');
});

test('TC_REG_006 pemilik tanpa profil diarahkan ke profil usaha', async ({ page }) => {
    await registerOwner(page, 'tanpaprofil');

    for (const path of ['/umkm/dashboard', '/umkm/kebutuhan', '/umkm/produk']) {
        await page.goto(path);
        await expect(page).toHaveURL(/\/umkm\/profil$/);
        await expect(page.getByText('Lengkapi data usaha ini dulu')).toBeVisible();
    }
});

test('TC_ACC_003 ganti kata sandi ditolak jika kata sandi lama salah', async ({ page }) => {
    await login(page, accounts.owner);
    await page.goto('/akun');

    await page.getByRole('textbox', { name: 'Kata sandi saat ini' }).fill('Bukan123');
    await page.getByRole('textbox', { name: 'Kata sandi baru', exact: true }).fill('Rahasia456');
    await page.getByRole('textbox', { name: 'Ulangi kata sandi baru' }).fill('Rahasia456');
    await page.getByRole('button', { name: 'Simpan perubahan' }).click();

    await expect(page.getByText('Kata sandi saat ini tidak sesuai.')).toBeVisible();
});

test('TC_ACC_004 email tidak dapat diubah menjadi email akun lain', async ({ page }) => {
    await login(page, accounts.owner);
    await page.goto('/akun');

    await page.getByLabel('Email', { exact: true }).fill(accounts.officer);
    await page.getByRole('button', { name: 'Simpan perubahan' }).click();

    await expect(page.getByText('Email ini sudah dipakai akun lain.')).toBeVisible();
});

test.describe('lupa dan atur ulang kata sandi', () => {
    test.skip(process.env.MAIL_MAILER !== 'log', 'Butuh MAIL_MAILER=log agar tautan bisa dibaca dari log email.');

    test('TC_PWD_001 meminta tautan atur ulang untuk email terdaftar', async ({ page }) => {
        const owner = createOwner('lupa');

        await requestResetLink(page, owner.email);

        await expect.poll(() => latestResetPath(owner.email)).not.toBeNull();
    });

    test('TC_PWD_003 email kosong atau berformat salah tidak terkirim', async ({ page }) => {
        await page.goto('/forgot-password');
        await page.getByRole('button', { name: 'Kirim tautan' }).click();
        await expect(page.getByLabel('Email')).toHaveJSProperty('validity.valueMissing', true);

        await page.getByLabel('Email').fill('bukan-email');
        await page.getByRole('button', { name: 'Kirim tautan' }).click();
        await expect(page).toHaveURL(/\/forgot-password$/);
        await expect(page.getByLabel('Email')).toHaveJSProperty('validity.typeMismatch', true);
    });

    test('TC_PWD_004 dan TC_PWD_006 atur ulang dengan tautan sah, tautan bekas tidak berlaku', async ({ page }) => {
        const owner = createOwner('reset');
        await requestResetLink(page, owner.email);
        const link = latestResetPath(owner.email);
        expect(link).not.toBeNull();

        await page.goto(link);
        await page.getByRole('textbox', { name: 'Kata sandi baru', exact: true }).fill('BaruAman123');
        await page.getByRole('textbox', { name: 'Ulangi kata sandi baru' }).fill('BaruAman123');
        await page.getByRole('button', { name: 'Simpan kata sandi' }).click();

        await expect(page).toHaveURL(/\/login$/);
        await expect(page.getByRole('status')).toContainText('Kata sandi sudah diperbarui. Silakan masuk.');

        await login(page, owner.email, 'BaruAman123');
        await expect(page).toHaveURL(/\/umkm\/dashboard$/);

        await page.context().clearCookies();
        await login(page, owner.email, password);
        await expect(page.getByRole('alert')).toContainText('Email/Username atau kata sandi salah.');

        await page.goto(link);
        await page.getByRole('textbox', { name: 'Kata sandi baru', exact: true }).fill('LainLagi123');
        await page.getByRole('textbox', { name: 'Ulangi kata sandi baru' }).fill('LainLagi123');
        await page.getByRole('button', { name: 'Simpan kata sandi' }).click();
        await expect(page.getByText('Tautan tidak berlaku atau sudah kedaluwarsa.')).toBeVisible();
    });

    test('TC_PWD_005 kata sandi baru terlalu pendek atau konfirmasi berbeda ditolak', async ({ page }) => {
        const owner = createOwner('resetpendek');
        await requestResetLink(page, owner.email);
        const link = latestResetPath(owner.email);

        await page.goto(link);
        await page.getByRole('textbox', { name: 'Kata sandi baru', exact: true }).fill('Abc12');
        await page.getByRole('textbox', { name: 'Ulangi kata sandi baru' }).fill('Abc12');
        await page.getByRole('button', { name: 'Simpan kata sandi' }).click();
        await expect(page.getByText('Kata sandi baru minimal 8 karakter.')).toBeVisible();

        await page.getByRole('textbox', { name: 'Kata sandi baru', exact: true }).fill('BaruAman123');
        await page.getByRole('textbox', { name: 'Ulangi kata sandi baru' }).fill('BaruAman124');
        await page.getByRole('button', { name: 'Simpan kata sandi' }).click();
        await expect(page.getByText('Ulangi kata sandi baru yang sama.')).toBeVisible();

        await login(page, owner.email, password);
        await expect(page).toHaveURL(/\/umkm\/dashboard$/);
    });
});

test('TC_AUTHZ_002 pemilik ditolak dari halaman kepala desa', async ({ page }) => {
    await login(page, accounts.owner);

    for (const path of ['/pimpinan/persetujuan-tindak-lanjut', '/pimpinan/pengelompokan', '/pimpinan/riwayat-pembinaan']) {
        const response = await page.goto(path);
        expect(response.status()).toBe(403);
    }
});

test('TC_AUTHZ_004 kepala desa tidak dapat mengubah data operasional UMKM', async ({ page }) => {
    const owner = createOwner('kadesblok');
    await login(page, accounts.villageHead);

    for (const path of ['/petugas/kategori-kendala', '/petugas/bank-soal', '/petugas/program-bantuan', '/petugas/umkm']) {
        const response = await page.goto(path);
        expect(response.status()).toBe(403);
    }

    const verify = await api(page, 'POST', `/petugas/umkm/${owner.businessId}/verifikasi`, {
        data: { verification_status: 'verified' },
    });
    expect(verify.status()).toBe(403);

    const [{ verification_status: status }] = sql(`select verification_status from businesses where id = ${owner.businessId}`);
    expect(status).toBe('pending');
});

test('TC_AUTHZ_005 tidak ada halaman untuk menambah peran atau mengubah hak akses', async ({ page }) => {
    for (const email of [accounts.owner, accounts.officer, accounts.villageHead]) {
        await switchTo(page, email);

        for (const path of ['/roles', '/permissions', '/admin/roles', '/petugas/roles', '/pimpinan/roles']) {
            const response = await page.goto(path);
            expect(response.status()).toBe(404);
        }
    }
});

test('TC_AUTHZ_008 petugas dan kepala desa ditolak dari halaman pemilik', async ({ page }) => {
    for (const email of [accounts.officer, accounts.villageHead]) {
        await switchTo(page, email);

        for (const path of ['/umkm/profil', '/umkm/kebutuhan', '/umkm/rekomendasi', '/umkm/produk']) {
            const response = await page.goto(path);
            expect(response.status()).toBe(403);
        }
    }
});
