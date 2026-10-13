export const password = 'Rahasia123';

export const accounts = {
    owner: 'pemilik.umkm@tumbuh.test',
    officer: 'petugas@tumbuh.test',
    villageHead: 'kepala.desa@tumbuh.test',
    inactive: 'nonaktif@tumbuh.test',
    unknown: 'tidak.ada@tumbuh.test',
};

export async function login(page, email, secret = password) {
    await page.goto('/login');
    await page.getByLabel('Email atau nama pengguna').fill(email);
    await page.getByRole('textbox', { name: 'Kata sandi' }).fill(secret);
    await page.getByRole('button', { name: 'Masuk' }).click();
}

export function desktopMenu(page) {
    return page.locator('aside').filter({ has: page.getByRole('navigation', { name: 'Menu dashboard' }) }).first();
}

export function acceptDialogs(page) {
    page.on('dialog', (dialog) => dialog.accept());
}
