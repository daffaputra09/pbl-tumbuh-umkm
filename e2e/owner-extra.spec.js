import { expect, test } from '@playwright/test';
import { login } from './auth.js';
import { api, createOwner, fx, sql, switchTo } from './support/helpers.js';

const tinyPng = Buffer.from(
    'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
    'base64',
);

const profile = (extra = {}) => ({
    business_type_id: 1,
    business_name: 'Usaha Uji',
    owner_name: 'Pemilik Uji',
    phone: '081200000000',
    address: 'Jl. Uji No. 1',
    hamlet: 'Krajan',
    established_year: 2019,
    employee_count: 2,
    ...extra,
});

test.describe('profil usaha', () => {
    test('TC_BIZ_002 profil ditolak jika field wajib kosong', async ({ page }) => {
        const owner = createOwner('biz002');
        await login(page, owner.email);

        const response = await api(page, 'POST', '/umkm/profil', { data: {} });

        expect(response.status()).toBe(422);
        const { errors } = await response.json();
        for (const field of ['business_type_id', 'business_name', 'owner_name', 'phone', 'address', 'hamlet', 'established_year', 'employee_count']) {
            expect(errors).toHaveProperty(field);
        }
        for (const field of ['email', 'rt', 'rw', 'description']) {
            expect(errors).not.toHaveProperty(field);
        }
    });

    test('TC_BIZ_003 tahun mulai dan jumlah tenaga kerja tidak valid ditolak', async ({ page }) => {
        const owner = createOwner('biz003');
        await login(page, owner.email);

        const response = await api(page, 'POST', '/umkm/profil', { data: profile({ established_year: 2099, employee_count: 'banyak' }) });

        expect(response.status()).toBe(422);
        const { errors } = await response.json();
        expect(errors).toHaveProperty('established_year');
        expect(errors).toHaveProperty('employee_count');
        const [row] = sql(`select established_year from businesses where id = ${owner.businessId}`);
        expect(Number(row.established_year)).toBe(2019);
    });
});

test.describe('produk', () => {
    test('TC_PROD_005 foto produk bukan gambar atau lebih dari 2 MB ditolak', async ({ page }) => {
        const owner = createOwner('prod005');
        await login(page, owner.email);

        const notImage = await api(page, 'POST', '/umkm/produk', {
            multipart: { name: 'Foto Salah', category: 'Makanan', photo: { name: 'catatan.pdf', mimeType: 'application/pdf', buffer: Buffer.from('%PDF-1.4 uji') } },
        });
        expect(notImage.status()).toBe(422);
        expect((await notImage.json()).errors).toHaveProperty('photo');

        const tooLarge = await api(page, 'POST', '/umkm/produk', {
            multipart: {
                name: 'Foto Besar',
                category: 'Makanan',
                photo: { name: 'besar.png', mimeType: 'image/png', buffer: Buffer.concat([tinyPng, Buffer.alloc(3 * 1024 * 1024)]) },
            },
        });
        expect(tooLarge.status()).toBe(422);
        expect((await tooLarge.json()).errors).toHaveProperty('photo');

        const valid = await api(page, 'POST', '/umkm/produk', {
            multipart: { name: 'Foto Sah', category: 'Makanan', photo: { name: 'sah.png', mimeType: 'image/png', buffer: tinyPng } },
        });
        expect(valid.status()).toBe(201);
        expect((await valid.json()).photo_path).toBeTruthy();
    });

    test('TC_PROD_006 pemilik tidak dapat mengubah produk UMKM lain', async ({ page }) => {
        const first = createOwner('prod006a');
        const second = createOwner('prod006b');

        await login(page, first.email);
        const created = await api(page, 'POST', '/umkm/produk', { data: { name: 'Produk Milik A', category: 'Makanan' } });
        expect(created.status()).toBe(201);
        const { id } = await created.json();

        await switchTo(page, second.email);
        const response = await api(page, 'PUT', `/umkm/produk/${id}`, { data: { name: 'Produk Diserobot', category: 'Makanan' } });

        expect(response.status()).toBe(403);
        const [row] = sql(`select name from products where id = ${id}`);
        expect(row.name).toBe('Produk Milik A');
    });
});

test.describe('kuesioner dan rekomendasi', () => {
    test('TC_ASM_004 kuesioner tidak dapat diselesaikan jika masih ada soal kosong', async ({ page }) => {
        const owner = createOwner('asm004');
        await login(page, owner.email);

        const response = await api(page, 'POST', '/umkm/kebutuhan', { data: { answers: owner.answersHigh.slice(0, 1) } });

        expect(response.status()).toBe(422);
        expect(sql(`select id from assessments where business_id = ${owner.businessId}`)).toHaveLength(0);
    });

    test('TC_ASM_005 dan TC_REC_012 hanya satu asesmen berjalan, rekomendasi lama nonaktif', async ({ page }) => {
        fx('program', { name: `Program Asesmen ${Date.now()}` });
        const owner = createOwner('asm005');
        await login(page, owner.email);

        const first = await api(page, 'POST', '/umkm/kebutuhan', { data: { answers: owner.answersHigh } });
        expect(first.status()).toBe(201);
        const firstId = (await first.json()).id;
        expect(sql(`select id from program_recommendations where assessment_id = ${firstId} and is_active is true`).length).toBeGreaterThan(0);

        const second = await api(page, 'POST', '/umkm/kebutuhan', { data: { answers: owner.answersLow } });
        expect(second.status()).toBe(201);
        const secondId = (await second.json()).id;

        const current = sql(`select id from assessments where business_id = ${owner.businessId} and is_current is true`);
        expect(current).toHaveLength(1);
        expect(current[0].id).toBe(secondId);
        expect(sql(`select id from program_recommendations where assessment_id = ${firstId} and is_active is true`)).toHaveLength(0);
    });

    test('TC_REC_010 dan TC_REC_011 daftar minat tidak ganda dan tidak bisa pada rekomendasi usaha lain', async ({ page }) => {
        fx('program', { name: `Program Minat ${Date.now()}` });
        const first = createOwner('rec010a');
        const second = createOwner('rec010b');

        await login(page, first.email);
        const submitted = await api(page, 'POST', '/umkm/kebutuhan', { data: { answers: first.answersHigh } });
        expect(submitted.status()).toBe(201);
        const [recommendation] = sql(`select id from program_recommendations where business_id = ${first.businessId} and is_active is true order by id limit 1`);

        await api(page, 'POST', `/umkm/rekomendasi/${recommendation.id}/daftar`);
        const [once] = sql(`select applied_at from program_recommendations where id = ${recommendation.id}`);
        expect(once.applied_at).not.toBeNull();

        await api(page, 'POST', `/umkm/rekomendasi/${recommendation.id}/daftar`);
        const [twice] = sql(`select applied_at from program_recommendations where id = ${recommendation.id}`);
        expect(twice.applied_at).toBe(once.applied_at);

        await switchTo(page, second.email);
        const view = await page.request.get(`/umkm/rekomendasi/${recommendation.id}`);
        expect(view.status()).toBe(403);
        const apply = await api(page, 'POST', `/umkm/rekomendasi/${recommendation.id}/daftar`);
        expect(apply.status()).toBe(403);
    });
});
