<?php

namespace App\Navigation;

use App\Models\User;

class RoleNavigation
{
    /**
     * Menu tunggal untuk ketiga peran. Item tanpa href belum punya halaman.
     *
     * @return list<array{title: string, items: list<array{label: string, href: ?string, icon: string}>}>
     */
    public static function groups(string $role): array
    {
        return match ($role) {
            User::ROLE_OFFICER => self::officer(),
            User::ROLE_VILLAGE_HEAD => self::villageHead(),
            default => self::businessOwner(),
        };
    }

    public static function headerTitle(string $role): string
    {
        return match ($role) {
            User::ROLE_OFFICER => 'Dashboard Petugas',
            User::ROLE_VILLAGE_HEAD => 'Dashboard Kepala Desa',
            default => 'Dashboard Usaha',
        };
    }

    public static function isCurrent(?string $href): bool
    {
        if ($href === null || $href === '') {
            return false;
        }

        $path = trim($href, '/');
        $currentPath = request()->path();

        if ($currentPath === $path) {
            return true;
        }

        if ($path === 'petugas/umkm') {
            return request()->is('petugas/umkm/*') && ! request()->is('petugas/umkm/pendataan-umkm*');
        }

        if (request()->is($path.'/*')) {
            return true;
        }

        return false;
    }

    /**
     * @return list<array{title: string, items: list<array{label: string, href: ?string, icon: string}>}>
     */
    private static function businessOwner(): array
    {
        return [
            ['title' => 'Utama', 'items' => [
                ['label' => 'Dashboard', 'href' => '/umkm/dashboard', 'icon' => 'DashboardSquare01Icon'],
            ]],
            ['title' => 'Usaha', 'items' => [
                ['label' => 'Profil usaha', 'href' => '/umkm/profil', 'icon' => 'Store04Icon'],
                ['label' => 'Produk', 'href' => '/umkm/produk', 'icon' => 'Factory01Icon'],
                ['label' => 'Kebutuhan dan kendala', 'href' => '/umkm/kebutuhan', 'icon' => 'CheckListIcon'],
            ]],
            ['title' => 'Hasil', 'items' => [
                ['label' => 'Rekomendasi program', 'href' => null, 'icon' => 'GiftIcon'],
                ['label' => 'Riwayat pembinaan', 'href' => null, 'icon' => 'TeachingIcon'],
            ]],
            ['title' => 'Akun', 'items' => [
                ['label' => 'Profil akun', 'href' => '/akun', 'icon' => 'User02Icon'],
            ]],
        ];
    }

    /**
     * @return list<array{title: string, items: list<array{label: string, href: ?string, icon: string}>}>
     */
    private static function officer(): array
    {
        return [
            ['title' => 'Utama', 'items' => [
                ['label' => 'Dashboard', 'href' => '/dashboard', 'icon' => 'DashboardSquare01Icon'],
            ]],
            ['title' => 'Pengelolaan', 'items' => [
                ['label' => 'Data UMKM', 'href' => '/petugas/umkm', 'icon' => 'Store01Icon'],
                ['label' => 'Pendataan UMKM', 'href' => '/petugas/umkm/pendataan-umkm', 'icon' => 'Store04Icon'],
                ['label' => 'Jenis usaha', 'href' => '/petugas/jenis-usaha', 'icon' => 'CheckListIcon'],
                ['label' => 'Kategori kendala', 'href' => null, 'icon' => 'Tag01Icon'],
                ['label' => 'Program bantuan', 'href' => null, 'icon' => 'GiftIcon'],
            ]],
            ['title' => 'Tindak lanjut', 'items' => [
                ['label' => 'Ajukan tindak lanjut', 'href' => null, 'icon' => 'TeachingIcon'],
                ['label' => 'Riwayat pembinaan', 'href' => null, 'icon' => 'TeachingIcon'],
            ]],
            ['title' => 'Analisis', 'items' => [
                ['label' => 'Pengelompokan', 'href' => null, 'icon' => 'Analytics01Icon'],
                ['label' => 'Rekomendasi', 'href' => null, 'icon' => 'GiftIcon'],
                ['label' => 'Laporan', 'href' => null, 'icon' => 'FileExportIcon'],
            ]],
            ['title' => 'Akun', 'items' => [
                ['label' => 'Profil akun', 'href' => '/akun', 'icon' => 'User02Icon'],
            ]],
        ];
    }

    /**
     * @return list<array{title: string, items: list<array{label: string, href: ?string, icon: string}>}>
     */
    private static function villageHead(): array
    {
        return [
            ['title' => 'Utama', 'items' => [
                ['label' => 'Dashboard', 'href' => '/dashboard', 'icon' => 'DashboardSquare01Icon'],
            ]],
            ['title' => 'Pemantauan', 'items' => [
                ['label' => 'Data UMKM', 'href' => null, 'icon' => 'Store01Icon'],
                ['label' => 'Pengelompokan', 'href' => null, 'icon' => 'Analytics01Icon'],
                ['label' => 'Rekomendasi', 'href' => null, 'icon' => 'GiftIcon'],
                ['label' => 'Laporan', 'href' => null, 'icon' => 'FileExportIcon'],
            ]],
            ['title' => 'Keputusan', 'items' => [
                ['label' => 'Persetujuan tindak lanjut', 'href' => null, 'icon' => 'CheckListIcon'],
                ['label' => 'Riwayat pembinaan', 'href' => null, 'icon' => 'TeachingIcon'],
            ]],
            ['title' => 'Akun', 'items' => [
                ['label' => 'Profil akun', 'href' => '/akun', 'icon' => 'User02Icon'],
            ]],
        ];
    }
}
