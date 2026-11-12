import { defineConfig } from 'vitest/config';

export default defineConfig({
    test: {
        include: ['resources/js/**/*.test.js'],
        exclude: ['e2e/**', 'node_modules/**'],
    },
});
