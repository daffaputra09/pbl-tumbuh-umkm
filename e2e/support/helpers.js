import { execFileSync } from 'node:child_process';
import { readFileSync, readdirSync, existsSync } from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { login } from '../auth.js';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '../..');

/** Menjalankan fixture PHP dan mengembalikan hasil JSON-nya. */
export function fx(command, args = {}) {
    const output = execFileSync('php', ['e2e/support/fixtures.php', command, JSON.stringify(args)], {
        cwd: root,
        encoding: 'utf8',
    });

    const lastLine = output.trim().split('\n').pop();

    try {
        return JSON.parse(lastLine);
    } catch {
        throw new Error(`Keluaran fixture '${command}' bukan JSON:\n${output.slice(-1500)}`);
    }
}

/** Query SELECT langsung ke basis data uji. */
export function sql(query) {
    return fx('sql', { query });
}

export function uniqueEmail(prefix) {
    return `${prefix}.${Date.now()}.${Math.floor(Math.random() * 1000)}@tumbuh.test`;
}

/** Pemilik lengkap (akun + profil usaha) beserta jawaban kuesioner siap kirim. */
export function createOwner(prefix, business = 'Usaha Uji') {
    return fx('owner', { email: uniqueEmail(prefix), name: `Pemilik ${prefix}`, business });
}

/**
 * Panggilan HTTP berotentikasi dengan token CSRF dari sesi yang sedang masuk.
 * Pengalihan (302) diikuti dengan GET, seperti peramban, bukan dengan metode semula.
 */
export async function api(page, method, url, { data, multipart } = {}) {
    await page.goto('/akun');
    const token = await page.locator('meta[name="csrf-token"]').getAttribute('content');

    const response = await page.request.fetch(url, {
        method,
        headers: { 'X-CSRF-TOKEN': token, Accept: 'application/json' },
        data,
        multipart,
        maxRedirects: 0,
    });

    if ([301, 302, 303].includes(response.status())) {
        return page.request.get(response.headers().location);
    }

    return response;
}

/** Ganti akun yang sedang masuk. */
export async function switchTo(page, email) {
    await page.context().clearCookies();
    await login(page, email);
}

/** Tautan atur ulang kata sandi terakhir untuk email tertentu dari log email (MAIL_MAILER=log). */
export function latestResetPath(email) {
    const dir = path.join(root, 'storage/logs');
    if (!existsSync(dir)) {
        return null;
    }
    const files = readdirSync(dir).filter((name) => name.endsWith('.log')).sort();
    let found = null;
    for (const name of files) {
        const text = readFileSync(path.join(dir, name), 'utf8')
            .replace(/=\r?\n/g, '')
            .replace(/=3D/g, '=')
            .replace(/&amp;/g, '&');
        for (const match of text.matchAll(/reset-password\/([A-Za-z0-9]+)\?email=([^"'\s<>)\]&]+)/g)) {
            if (decodeURIComponent(match[2]) === email) {
                found = `/reset-password/${match[1]}?email=${match[2]}`;
            }
        }
    }

    return found;
}
