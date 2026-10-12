import { execSync } from 'node:child_process';

export default function globalSetup() {
    execSync('php artisan db:seed --class=PlaywrightAuthSeeder --no-interaction', {
        stdio: 'inherit',
    });
}
