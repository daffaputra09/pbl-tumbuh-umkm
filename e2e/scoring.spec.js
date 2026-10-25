import { expect, test } from '@playwright/test';
import { accounts, login } from './auth.js';
import { api, createOwner, sql } from './support/helpers.js';

const stamp = () => `${Date.now()}${Math.floor(Math.random() * 1000)}`;

/** Petugas membuat kategori baru berisi soal Likert dengan skor opsi terpilih, lalu mengembalikan id soal dan opsinya. */
async function createScoredCategory(page, questions) {
    const id = stamp();
    const created = await api(page, 'POST', '/petugas/kategori-kendala', {
        data: { name: `Skor ${id}`, slug: `skor-${id}`, moderate_threshold: 40, high_threshold: 70, sort_order: 80, is_active: true },
    });
    expect(created.ok()).toBeTruthy();
    const [category] = sql(`select id from obstacle_categories where slug = 'skor-${id}'`);

    const made = [];
    for (const [index, spec] of questions.entries()) {
        const prompt = `Soal skor ${id}-${index}`;
        const response = await api(page, 'POST', '/petugas/bank-soal', {
            data: {
                obstacle_category_id: category.id,
                type: 'likert',
                prompt,
                weight: spec.weight ?? 1,
                is_reverse_scored: spec.reverse ?? false,
                sort_order: index + 1,
                is_active: true,
            },
        });
        expect(response.ok()).toBeTruthy();
        const [question] = sql(`select id from assessment_questions where prompt = '${prompt}'`);
        const [option] = sql(`select id, label, sort_order from question_options where assessment_question_id = ${question.id} order by id limit 1`);
        const update = await api(page, 'PUT', `/petugas/bank-soal/options/${option.id}`, {
            data: { label: option.label, score: spec.score, sort_order: option.sort_order },
        });
        expect(update.ok()).toBeTruthy();
        made.push({ questionId: question.id, optionId: option.id });
    }

    return { categoryId: category.id, answers: made };
}

async function ownerSubmits(browser, answers) {
    const owner = createOwner('skor');
    const context = await browser.newContext();
    const page = await context.newPage();
    await login(page, owner.email);
    const response = await api(page, 'POST', '/umkm/kebutuhan', { data: { answers } });
    expect(response.status()).toBe(201);
    const assessmentId = (await response.json()).id;
    await context.close();

    return { owner, assessmentId };
}

function scoreOf(assessmentId, categoryId) {
    const [row] = sql(`select score, level from assessment_category_scores where assessment_id = ${assessmentId} and obstacle_category_id = ${categoryId}`);

    return { score: Number(row.score), level: row.level };
}

test.describe('skor kategori dan level kendala', () => {
    test.beforeEach(async ({ page }) => {
        await login(page, accounts.officer);
    });

    test('TC_SCORE_001 skor kategori mengikuti rumus bobot', async ({ page, browser }) => {
        const { categoryId, answers } = await createScoredCategory(page, [{ score: 80 }, { score: 40 }]);

        const { assessmentId } = await ownerSubmits(browser, answers);

        expect(scoreOf(assessmentId, categoryId)).toEqual({ score: 60, level: 'moderate' });
        expect(sql(`select id from assessment_category_scores where assessment_id = ${assessmentId} and obstacle_category_id = ${categoryId}`)).toHaveLength(1);
    });

    test('TC_SCORE_002 skor di bawah ambang sedang menjadi rendah', async ({ page, browser }) => {
        const { categoryId, answers } = await createScoredCategory(page, [{ score: 20 }, { score: 20 }]);

        const { assessmentId } = await ownerSubmits(browser, answers);

        expect(scoreOf(assessmentId, categoryId)).toEqual({ score: 20, level: 'low' });
    });

    test('TC_SCORE_003 skor tepat di ambang memakai batas yang ditentukan', async ({ page, browser }) => {
        const atModerate = await createScoredCategory(page, [{ score: 40 }]);
        const atHigh = await createScoredCategory(page, [{ score: 70 }]);

        const { assessmentId } = await ownerSubmits(browser, [...atModerate.answers, ...atHigh.answers]);

        expect(scoreOf(assessmentId, atModerate.categoryId)).toEqual({ score: 40, level: 'moderate' });
        expect(scoreOf(assessmentId, atHigh.categoryId)).toEqual({ score: 70, level: 'high' });
    });

    test('TC_SCORE_004 kategori utama adalah skor tertinggi', async ({ page, browser }) => {
        const top = await createScoredCategory(page, [{ score: 90 }]);
        const lower = await createScoredCategory(page, [{ score: 60 }]);

        const { assessmentId } = await ownerSubmits(browser, [...lower.answers, ...top.answers]);

        const [assessment] = sql(`select primary_obstacle_category_id as id from assessments where id = ${assessmentId}`);
        expect(assessment.id).toBe(top.categoryId);
    });

    test('TC_SCORE_005 soal skor terbalik membalik kontribusi sebelum rumus', async ({ page, browser }) => {
        const { categoryId, answers } = await createScoredCategory(page, [{ score: 80, reverse: true }]);

        const { assessmentId } = await ownerSubmits(browser, answers);

        expect(scoreOf(assessmentId, categoryId)).toEqual({ score: 20, level: 'low' });
    });

    test('TC_SCORE_006 bobot soal mengubah hasil skor', async ({ page, browser }) => {
        const { categoryId, answers } = await createScoredCategory(page, [{ score: 0, weight: 1 }, { score: 100, weight: 3 }]);

        const { assessmentId } = await ownerSubmits(browser, answers);

        expect(scoreOf(assessmentId, categoryId)).toEqual({ score: 75, level: 'high' });
    });
});

test.describe('pencocokan program dengan level kendala', () => {
    async function createProgram(page, categoryId, minimum, status = 'active') {
        const name = `Program ${minimum} ${stamp()}`;
        const response = await api(page, 'POST', '/petugas/program-bantuan', {
            data: {
                name,
                provider: 'Desa Tumbuh',
                description: 'Program uji pencocokan.',
                status,
                obstacle_category_ids: [categoryId],
                minimum_levels: { [categoryId]: minimum },
            },
        });
        expect(response.ok()).toBeTruthy();
        const [program] = sql(`select id from assistance_programs where name = '${name}'`);

        return program.id;
    }

    const recommended = (assessmentId, programId) =>
        sql(`select id from program_recommendations where assessment_id = ${assessmentId} and assistance_program_id = ${programId} and is_active = 1`).length;

    test('TC_REC_001, TC_REC_002, TC_REC_003 program cocok bila level memenuhi tingkat minimum', async ({ page, browser }) => {
        await login(page, accounts.officer);
        const high = await createScoredCategory(page, [{ score: 100 }]);
        const moderate = await createScoredCategory(page, [{ score: 60 }]);
        const low = await createScoredCategory(page, [{ score: 20 }]);

        const minModerateOnHigh = await createProgram(page, high.categoryId, 'moderate');
        const minHighOnHigh = await createProgram(page, high.categoryId, 'high');
        const minModerateOnModerate = await createProgram(page, moderate.categoryId, 'moderate');
        const minHighOnModerate = await createProgram(page, moderate.categoryId, 'high');
        const minModerateOnLow = await createProgram(page, low.categoryId, 'moderate');

        const { assessmentId } = await ownerSubmits(browser, [...high.answers, ...moderate.answers, ...low.answers]);

        expect(recommended(assessmentId, minModerateOnHigh)).toBe(1);
        expect(recommended(assessmentId, minHighOnHigh)).toBe(1);
        expect(recommended(assessmentId, minModerateOnModerate)).toBe(1);
        expect(recommended(assessmentId, minHighOnModerate)).toBe(0);
        expect(recommended(assessmentId, minModerateOnLow)).toBe(0);
    });

    test('TC_PROG_006 program draf tidak masuk rekomendasi', async ({ page, browser }) => {
        await login(page, accounts.officer);
        const high = await createScoredCategory(page, [{ score: 100 }]);
        const active = await createProgram(page, high.categoryId, 'moderate', 'active');
        const draft = await createProgram(page, high.categoryId, 'moderate', 'draft');
        const closed = await createProgram(page, high.categoryId, 'moderate', 'closed');
        const archived = await createProgram(page, high.categoryId, 'moderate', 'archived');

        const { assessmentId } = await ownerSubmits(browser, high.answers);

        expect(recommended(assessmentId, active)).toBe(1);
        for (const id of [draft, closed, archived]) {
            expect(recommended(assessmentId, id)).toBe(0);
        }
    });

    test('TC_REC_004 rekomendasi pemilik diurutkan menurut skor tertinggi', async ({ page, browser }) => {
        await login(page, accounts.officer);
        const high = await createScoredCategory(page, [{ score: 100 }]);
        const moderate = await createScoredCategory(page, [{ score: 60 }]);
        const lowerProgram = await createProgram(page, moderate.categoryId, 'moderate');
        const upperProgram = await createProgram(page, high.categoryId, 'moderate');

        const { owner } = await ownerSubmits(browser, [...moderate.answers, ...high.answers]);

        const context = await browser.newContext();
        const ownerPage = await context.newPage();
        await login(ownerPage, owner.email);
        await ownerPage.goto('/umkm/rekomendasi');
        const [upperName] = sql(`select name from assistance_programs where id = ${upperProgram}`).map((row) => row.name);
        const [lowerName] = sql(`select name from assistance_programs where id = ${lowerProgram}`).map((row) => row.name);
        const html = await ownerPage.content();
        await context.close();

        expect(html.indexOf(upperName)).toBeGreaterThan(-1);
        expect(html.indexOf(lowerName)).toBeGreaterThan(-1);
        expect(html.indexOf(upperName)).toBeLessThan(html.indexOf(lowerName));
    });
});
