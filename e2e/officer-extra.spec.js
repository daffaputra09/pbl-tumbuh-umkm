import { expect, test } from '@playwright/test';
import { accounts, login } from './auth.js';
import { api, createOwner, sql, switchTo } from './support/helpers.js';

const stamp = () => `${Date.now()}${Math.floor(Math.random() * 100)}`;

async function createCategory(page) {
    const id = stamp();
    const response = await api(page, 'POST', '/petugas/kategori-kendala', {
        data: {
            name: `Kategori Uji ${id}`,
            slug: `kategori-uji-${id}`,
            moderate_threshold: 40,
            high_threshold: 70,
            sort_order: 90,
            is_active: true,
        },
    });
    expect(response.ok()).toBeTruthy();
    const [row] = sql(`select id from obstacle_categories where slug = 'kategori-uji-${id}'`);

    return { id: row.id, name: `Kategori Uji ${id}`, slug: `kategori-uji-${id}` };
}

async function createLikert(page, categoryId, prompt) {
    const response = await api(page, 'POST', '/petugas/bank-soal', {
        data: { obstacle_category_id: categoryId, type: 'likert', prompt, weight: 1, sort_order: 90, is_active: true },
    });
    expect(response.ok()).toBeTruthy();
    const [row] = sql(`select id from assessment_questions where prompt = '${prompt}'`);

    return row.id;
}

test.describe('master data oleh petugas', () => {
    test.beforeEach(async ({ page }) => {
        await login(page, accounts.officer);
    });

    test('TC_CAT_003 mengubah ambang sedang dan tinggi', async ({ page }) => {
        const category = await createCategory(page);

        const response = await api(page, 'PUT', `/petugas/kategori-kendala/${category.id}`, {
            data: { name: category.name, slug: category.slug, moderate_threshold: 50, high_threshold: 80, sort_order: 90, is_active: true },
        });
        expect(response.ok()).toBeTruthy();

        const [row] = sql(`select moderate_threshold, high_threshold from obstacle_categories where id = ${category.id}`);
        expect(Number(row.moderate_threshold)).toBe(50);
        expect(Number(row.high_threshold)).toBe(80);
    });

    test('TC_CAT_004 ambang tinggi lebih kecil atau di luar 0 sampai 100 ditolak', async ({ page }) => {
        const category = await createCategory(page);
        const base = { name: category.name, slug: category.slug, sort_order: 90, is_active: true };

        const lower = await api(page, 'PUT', `/petugas/kategori-kendala/${category.id}`, {
            data: { ...base, moderate_threshold: 70, high_threshold: 40 },
        });
        expect(lower.status()).toBe(422);

        const outOfRange = await api(page, 'PUT', `/petugas/kategori-kendala/${category.id}`, {
            data: { ...base, moderate_threshold: -1, high_threshold: 120 },
        });
        expect(outOfRange.status()).toBe(422);

        const [row] = sql(`select moderate_threshold, high_threshold from obstacle_categories where id = ${category.id}`);
        expect(Number(row.moderate_threshold)).toBe(40);
        expect(Number(row.high_threshold)).toBe(70);
    });

    test('TC_CAT_009 ambang tinggi yang sama dengan ambang sedang ditolak', async ({ page }) => {
        const category = await createCategory(page);

        const response = await api(page, 'PUT', `/petugas/kategori-kendala/${category.id}`, {
            data: { name: category.name, slug: category.slug, moderate_threshold: 60, high_threshold: 60, sort_order: 90, is_active: true },
        });

        expect(response.status()).toBe(422);
    });

    test('TC_CAT_007 kategori yang sudah terhubung tidak dapat dihapus', async ({ page }) => {
        const [modal] = sql("select id from obstacle_categories where slug = 'modal'");

        const response = await api(page, 'DELETE', `/petugas/kategori-kendala/${modal.id}`);

        expect(await response.text()).toContain('Kategori tidak dapat dihapus karena sudah terhubung');
        expect(sql(`select id from obstacle_categories where id = ${modal.id}`)).toHaveLength(1);
    });

    test('TC_CAT_008 kategori tanpa soal dan program dapat dihapus', async ({ page }) => {
        const category = await createCategory(page);

        const response = await api(page, 'DELETE', `/petugas/kategori-kendala/${category.id}`);

        expect(await response.text()).toContain('Kategori kendala berhasil dihapus.');
        expect(sql(`select id from obstacle_categories where id = ${category.id}`)).toHaveLength(0);
    });

    test('TC_Q_006 skor opsi di luar 0 sampai 100 ditolak', async ({ page }) => {
        const [option] = sql('select id, label, sort_order, score from question_options order by id limit 1');

        for (const score of [101, -5]) {
            const response = await api(page, 'PUT', `/petugas/bank-soal/options/${option.id}`, {
                data: { label: option.label, score, sort_order: option.sort_order },
            });
            expect(response.status()).toBe(422);
        }

        const [after] = sql(`select score from question_options where id = ${option.id}`);
        expect(Number(after.score)).toBe(Number(option.score));
    });

    test('TC_Q_008 soal baru tanpa jawaban dapat dihapus beserta opsinya', async ({ page }) => {
        const category = await createCategory(page);
        const questionId = await createLikert(page, category.id, `Soal hapus ${stamp()}`);
        expect(sql(`select id from question_options where assessment_question_id = ${questionId}`)).toHaveLength(5);

        const response = await api(page, 'DELETE', `/petugas/bank-soal/${questionId}`);

        expect(await response.text()).toContain('Pertanyaan dan seluruh opsi jawabannya berhasil dihapus.');
        expect(sql(`select id from assessment_questions where id = ${questionId}`)).toHaveLength(0);
        expect(sql(`select id from question_options where assessment_question_id = ${questionId}`)).toHaveLength(0);
    });

    test('TC_Q_007 soal yang sudah dijawab tidak dapat dihapus', async ({ page, browser }) => {
        const owner = createOwner('soaldijawab');
        const ownerContext = await browser.newContext();
        const ownerPage = await ownerContext.newPage();
        await login(ownerPage, owner.email);
        const submit = await api(ownerPage, 'POST', '/umkm/kebutuhan', { data: { answers: owner.answersHigh } });
        expect(submit.status()).toBe(201);
        await ownerContext.close();

        const [answered] = sql('select assessment_question_id as id from assessment_answers limit 1');
        const response = await api(page, 'DELETE', `/petugas/bank-soal/${answered.id}`);

        expect(await response.text()).toContain('Pertanyaan tidak dapat dihapus karena sudah memiliki riwayat pengisian');
        expect(sql(`select id from assessment_questions where id = ${answered.id}`)).toHaveLength(1);
    });

    test('TC_Q_009 soal Likert tidak boleh kurang dari dua opsi', async ({ page }) => {
        const category = await createCategory(page);
        const questionId = await createLikert(page, category.id, `Soal opsi ${stamp()}`);
        const options = sql(`select id from question_options where assessment_question_id = ${questionId} order by id`);

        for (const option of options.slice(0, 3)) {
            await api(page, 'DELETE', `/petugas/bank-soal/options/${option.id}`);
        }
        expect(sql(`select id from question_options where assessment_question_id = ${questionId}`)).toHaveLength(2);

        await api(page, 'DELETE', `/petugas/bank-soal/options/${options[3].id}`);
        expect(sql(`select id from question_options where assessment_question_id = ${questionId}`)).toHaveLength(2);
    });

    test('TC_PROG_005 tanggal selesai sebelum tanggal mulai ditolak', async ({ page }) => {
        const [modal] = sql("select id from obstacle_categories where slug = 'modal'");

        const response = await api(page, 'POST', '/petugas/program-bantuan', {
            data: {
                name: `Program Tanggal ${stamp()}`,
                provider: 'Desa Tumbuh',
                description: 'Uji urutan tanggal.',
                status: 'draft',
                starts_on: '2026-12-10',
                ends_on: '2026-12-01',
                obstacle_category_ids: [modal.id],
            },
        });

        expect(response.status()).toBe(422);
        expect(await response.text()).toContain('Tanggal periode selesai harus sama dengan atau setelah tanggal mulai.');
    });

    test('TC_PROG_007 menghapus program bantuan', async ({ page }) => {
        const [modal] = sql("select id from obstacle_categories where slug = 'modal'");
        const name = `Program Hapus ${stamp()}`;
        const created = await api(page, 'POST', '/petugas/program-bantuan', {
            data: { name, provider: 'Desa Tumbuh', description: 'Akan dihapus.', status: 'draft', obstacle_category_ids: [modal.id] },
        });
        expect(created.ok()).toBeTruthy();
        const [program] = sql(`select id from assistance_programs where name = '${name}'`);

        const response = await api(page, 'DELETE', `/petugas/program-bantuan/${program.id}`);

        expect(await response.text()).toContain('Program bantuan berhasil dihapus.');
        const detail = await page.request.get(`/petugas/program-bantuan/${program.id}`);
        expect(detail.status()).toBe(404);
    });
});

test.describe('verifikasi data UMKM', () => {
    test('TC_VER_001 usaha baru berstatus menunggu', async () => {
        const owner = createOwner('verbaru');

        const [row] = sql(`select verification_status, verified_by, verified_at from businesses where id = ${owner.businessId}`);

        expect(row.verification_status).toBe('pending');
        expect(row.verified_by).toBeNull();
        expect(row.verified_at).toBeNull();
    });

    test('TC_VER_003 petugas meminta revisi dengan catatan', async ({ page }) => {
        const owner = createOwner('verrevisi');
        await login(page, accounts.officer);

        const noNote = await api(page, 'POST', `/petugas/umkm/${owner.businessId}/verifikasi`, {
            data: { verification_status: 'needs_revision' },
        });
        expect(noNote.status()).toBe(422);

        const response = await api(page, 'POST', `/petugas/umkm/${owner.businessId}/verifikasi`, {
            data: { verification_status: 'needs_revision', verification_note: 'Alamat dusun belum sesuai' },
        });
        expect(response.ok()).toBeTruthy();

        const [row] = sql(`select verification_status, verification_note, verified_by from businesses where id = ${owner.businessId}`);
        expect(row.verification_status).toBe('needs_revision');
        expect(row.verification_note).toBe('Alamat dusun belum sesuai');
        expect(row.verified_by).not.toBeNull();
    });

    test('TC_VER_004 status ditolak tidak tersedia pada verifikasi UMKM', async ({ page }) => {
        const owner = createOwner('verolak');
        await login(page, accounts.officer);

        const response = await api(page, 'POST', `/petugas/umkm/${owner.businessId}/verifikasi`, {
            data: { verification_status: 'rejected', verification_note: 'Data usaha tidak dapat dikonfirmasi' },
        });

        expect(response.status()).toBe(422);
        expect((await response.json()).errors).toHaveProperty('verification_status');
        const [row] = sql(`select verification_status from businesses where id = ${owner.businessId}`);
        expect(row.verification_status).toBe('pending');
    });

    test('TC_VER_006 kepala desa dan pemilik tidak dapat mengubah status verifikasi', async ({ page }) => {
        const owner = createOwner('verblok');

        for (const email of [accounts.villageHead, owner.email]) {
            await switchTo(page, email);
            const response = await api(page, 'POST', `/petugas/umkm/${owner.businessId}/verifikasi`, {
                data: { verification_status: 'verified' },
            });
            expect(response.status()).toBe(403);
        }

        const [row] = sql(`select verification_status, verified_by from businesses where id = ${owner.businessId}`);
        expect(row.verification_status).toBe('pending');
        expect(row.verified_by).toBeNull();
    });

    test('TC_LIST_006 pencarian tanpa hasil tidak menampilkan usaha', async ({ page }) => {
        await login(page, accounts.officer);
        await page.goto('/petugas/umkm');

        const search = page.getByPlaceholder('Cari nama usaha, nama pemilik, atau kontak…');
        await search.fill('Usaha Tidak Ada ZZZ');
        await search.press('Tab');

        await expect(page.getByText('Keripik Bu Sari')).toHaveCount(0);
        await expect(page.getByRole('link', { name: 'Detail' })).toHaveCount(0);
    });
});
