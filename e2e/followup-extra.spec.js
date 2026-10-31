import { expect, test } from '@playwright/test';
import { accounts, login } from './auth.js';
import { api, createOwner, fx, sql, switchTo } from './support/helpers.js';

const approval = (id) => `/pimpinan/persetujuan-tindak-lanjut/${id}`;
const submission = (owner, extra = {}) => ({
    business_id: owner.userId,
    planned_activity: 'Pendampingan foto produk',
    planned_on: '2026-12-01',
    submission_note: 'Pemilik sudah mendaftarkan minat.',
    ...extra,
});

test.describe('pengajuan dan keputusan tindak lanjut', () => {
    test('TC_FU_002 pengajuan tanpa kegiatan rencana ditolak', async ({ page }) => {
        const owner = createOwner('fu002');
        await login(page, accounts.officer);

        const response = await api(page, 'POST', '/petugas/tindak-lanjut', { data: submission(owner, { planned_activity: '' }) });

        expect(response.status()).toBe(422);
        expect((await response.json()).errors).toHaveProperty('planned_activity');
        expect(sql(`select id from follow_ups where business_id = ${owner.userId}`)).toHaveLength(0);
    });

    test('TC_FU_008 pengajuan tanpa tanggal rencana atau catatan ditolak', async ({ page }) => {
        const owner = createOwner('fu008');
        await login(page, accounts.officer);

        const noDate = await api(page, 'POST', '/petugas/tindak-lanjut', { data: submission(owner, { planned_on: '' }) });
        expect(noDate.status()).toBe(422);
        expect((await noDate.json()).errors).toHaveProperty('planned_on');

        const noNote = await api(page, 'POST', '/petugas/tindak-lanjut', { data: submission(owner, { submission_note: '' }) });
        expect(noNote.status()).toBe(422);
        expect((await noNote.json()).errors).toHaveProperty('submission_note');

        expect(sql(`select id from follow_ups where business_id = ${owner.userId}`)).toHaveLength(0);
    });

    test('TC_FU_004 kepala desa menolak tindak lanjut dengan alasan', async ({ page }) => {
        const owner = createOwner('fu004');
        const { followUpId } = fx('followup', { userId: owner.userId });
        await login(page, accounts.villageHead);

        const response = await api(page, 'PATCH', approval(followUpId), { data: { status: 'rejected', decision_note: 'Anggaran belum tersedia' } });

        expect(response.ok()).toBeTruthy();
        const [row] = sql(`select status, decision_note, decided_by from follow_ups where id = ${followUpId}`);
        expect(row.status).toBe('rejected');
        expect(row.decision_note).toBe('Anggaran belum tersedia');
        expect(row.decided_by).not.toBeNull();
    });

    test('TC_FU_005 pemilik tidak dapat mengajukan atau memutuskan tindak lanjut', async ({ page }) => {
        const owner = createOwner('fu005');
        const { followUpId } = fx('followup', { userId: owner.userId });
        await login(page, owner.email);

        const submit = await api(page, 'POST', '/petugas/tindak-lanjut', { data: submission(owner) });
        expect(submit.status()).toBe(403);

        const decide = await api(page, 'PATCH', approval(followUpId), { data: { status: 'approved', decision_note: 'Disetujui sendiri' } });
        expect(decide.status()).toBe(403);

        const [row] = sql(`select status from follow_ups where id = ${followUpId}`);
        expect(row.status).toBe('pending');
    });

    test('TC_FU_007 keputusan tanpa catatan ditolak', async ({ page }) => {
        const owner = createOwner('fu007');
        const { followUpId } = fx('followup', { userId: owner.userId });
        await login(page, accounts.villageHead);

        const response = await api(page, 'PATCH', approval(followUpId), { data: { status: 'approved', decision_note: '' } });

        expect(response.status()).toBe(422);
        expect((await response.json()).errors).toHaveProperty('decision_note');
        const [row] = sql(`select status from follow_ups where id = ${followUpId}`);
        expect(row.status).toBe('pending');
    });
});

test.describe('catatan pembinaan', () => {
    const body = (followUpId, extra = {}) => ({
        follow_up_id: followUpId,
        title: 'Pendampingan foto produk',
        held_on: '2026-11-21',
        description: 'Praktik foto produk bersama pemilik.',
        outcome: null,
        ...extra,
    });

    test('TC_COA_002 pembinaan ditolak jika tindak lanjut belum disetujui', async ({ page }) => {
        const owner = createOwner('coa002');
        const pending = fx('followup', { userId: owner.userId, status: 'pending' }).followUpId;
        const rejected = fx('followup', { userId: owner.userId, status: 'rejected' }).followUpId;
        await login(page, accounts.officer);

        const accepted = [];
        for (const id of [pending, rejected]) {
            const response = await api(page, 'POST', '/petugas/catat-pembinaan', { data: body(id) });
            accepted.push(response.ok());
        }

        expect(accepted, 'penerimaan pembinaan untuk tindak lanjut pending dan rejected').toEqual([false, false]);
        expect(sql(`select id from coaching_sessions where follow_up_id in (${pending}, ${rejected})`)).toHaveLength(0);
    });

    test('TC_COA_003 satu tindak lanjut hanya satu sesi pembinaan', async ({ page }) => {
        const owner = createOwner('coa003');
        const { followUpId } = fx('followup', { userId: owner.userId, status: 'approved' });
        await login(page, accounts.officer);

        const first = await api(page, 'POST', '/petugas/catat-pembinaan', { data: body(followUpId) });
        expect(first.ok()).toBeTruthy();

        const second = await api(page, 'POST', '/petugas/catat-pembinaan', { data: body(followUpId, { title: 'Sesi kedua' }) });

        expect(second.status()).toBe(422);
        expect(sql(`select id from coaching_sessions where follow_up_id = ${followUpId}`)).toHaveLength(1);
    });

    test('TC_COA_007 judul, tanggal, dan uraian wajib diisi', async ({ page }) => {
        const owner = createOwner('coa007');
        const { followUpId } = fx('followup', { userId: owner.userId, status: 'approved' });
        await login(page, accounts.officer);

        const response = await api(page, 'POST', '/petugas/catat-pembinaan', {
            data: body(followUpId, { title: '', held_on: '', description: '' }),
        });

        expect(response.status()).toBe(422);
        const { errors } = await response.json();
        expect(Object.keys(errors).sort()).toEqual(['description', 'held_on', 'title']);
        expect(sql(`select id from coaching_sessions where follow_up_id = ${followUpId}`)).toHaveLength(0);
    });

    test('TC_COA_006 dan TC_COA_008 riwayat pembinaan menurut peran', async ({ page }) => {
        const alpha = createOwner('riwayata', 'Usaha Alpha');
        const beta = createOwner('riwayatb', 'Usaha Beta');
        const titleA = `Sesi Alpha ${Date.now()}`;
        const titleB = `Sesi Beta ${Date.now()}`;
        fx('session', { followUpId: fx('followup', { userId: alpha.userId, status: 'approved' }).followUpId, title: titleA });
        fx('session', { followUpId: fx('followup', { userId: beta.userId, status: 'approved' }).followUpId, title: titleB });

        await login(page, accounts.officer);
        const officerView = await (await page.request.get('/petugas/catat-pembinaan')).text();
        expect(officerView).toContain(titleA);
        expect(officerView).toContain(titleB);

        await switchTo(page, alpha.email);
        const ownerView = await (await page.request.get('/umkm/riwayat-pembinaan')).text();
        expect(ownerView).toContain(titleA);
        expect(ownerView).not.toContain(titleB);

        await switchTo(page, accounts.villageHead);
        await page.goto('/pimpinan/riwayat-pembinaan');
        const headView = await page.content();
        expect(headView).toContain(titleA);
        expect(headView).toContain(titleB);
        await expect(page.getByRole('button', { name: 'Catat Pembinaan' })).toHaveCount(0);
    });
});
