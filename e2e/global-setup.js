import { execSync } from 'node:child_process';
import { existsSync, mkdirSync, readFileSync, unlinkSync, writeFileSync } from 'node:fs';
import path from 'node:path';

const root = process.cwd();
const hotFile = path.join(root, 'public/hot');
const hotBackup = path.join(root, 'test-results/.vite-hot-backup');
const manifest = path.join(root, 'public/build/manifest.json');

export default function globalSetup() {
    mkdirSync(path.dirname(hotBackup), { recursive: true });

    if (existsSync(hotBackup)) {
        unlinkSync(hotBackup);
    }

    if (existsSync(hotFile)) {
        writeFileSync(hotBackup, readFileSync(hotFile));
        unlinkSync(hotFile);
    }

    if (! existsSync(manifest)) {
        execSync('npm run build', { stdio: 'inherit', cwd: root });
    }

    execSync('php artisan db:seed --class=PlaywrightAuthSeeder --no-interaction', {
        stdio: 'inherit',
        cwd: root,
    });
}
