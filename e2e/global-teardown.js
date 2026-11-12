import { existsSync, readFileSync, unlinkSync, writeFileSync } from 'node:fs';
import path from 'node:path';

const root = process.cwd();
const hotFile = path.join(root, 'public/hot');
const hotBackup = path.join(root, 'test-results/.vite-hot-backup');

export default function globalTeardown() {
    if (! existsSync(hotBackup)) {
        return;
    }

    writeFileSync(hotFile, readFileSync(hotBackup));
    unlinkSync(hotBackup);
}
