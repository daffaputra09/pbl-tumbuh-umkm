<?php

namespace App\Navigation;

use App\Models\Business;
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
     * @return list<array{title: string, items: list<array{label: string, href: ?string, icon: string, reason?: ?string}>}>
     */
    private static function businessOwner(): array
    {
        $access = self::ownerAccess();

        return [
            ['title' => 'Utama', 'items' => [
                self::ownerItem('Dashboard', '/umkm/dashboard', 'DashboardSquare01Icon', self::lockReason($access, false)),
            ]],
            ['title' => 'Usaha', 'items' => [
                self::ownerItem('Profil usaha', '/umkm/profil', 'Store04Icon', null),
                self::ownerItem('Produk', '/umkm/produk', 'Factory01Icon', self::lockReason($access, true)),
                self::ownerItem('Kebutuhan dan kendala', '/umkm/kebutuhan', 'CheckListIcon', self::lockReason($access, true)),
            ]],
            ['title' => 'Hasil', 'items' => [
                self::ownerItem('Rekomendasi program', '/umkm/rekomendasi', 'GiftIcon', self::lockReason($access, true)),
                self::ownerItem('Riwayat pembinaan', '/umkm/riwayat-pembinaan', 'TeachingIcon', self::lockReason($access, true)),
            ]],
            ['title' => 'Akun', 'items' => [
                self::ownerItem('Profil akun', '/akun', 'User02Icon', null),
            ]],
        ];
    }

    /**
     * @return array{profile: bool, verified: bool}
     */
    private static function ownerAccess(): array
    {
        $user = auth()->user();
        $business = $user instanceof User ? $user->business : null;
        $profileReady = $business instanceof Business && $business->hasCompleteProfile();

        return [
            'profile' => $profileReady,
            'verified' => $profileReady && $business->isVerified(),
        ];
    }

    /**
     * @param  array{profile: bool, verified: bool}  $access
     */
    private static function lockReason(array $access, bool $needsVerification): ?string
    {
        if (! $access['profile']) {
            return 'Lengkapi profil usaha terlebih dahulu.';
        }

        if ($needsVerification && ! $access['verified']) {
            return 'Usaha belum diverifikasi petugas desa.';
        }

        return null;
    }

    /**
     * @return array{label: string, href: ?string, icon: string, reason: ?string}
     */
    private static function ownerItem(string $label, string $href, string $icon, ?string $lockedReason): array
    {
        return [
            'label' => $label,
            'href' => $lockedReason === null ? $href : null,
            'icon' => $icon,
            'reason' => $lockedReason,
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
                ['label' => 'Kategori kendala', 'href' => '/petugas/kategori-kendala', 'icon' => 'Tag01Icon'],
                ['label' => 'Bank soal', 'href' => '/petugas/bank-soal', 'icon' => 'CheckListIcon'],
                ['label' => 'Program bantuan', 'href' => '/petugas/program-bantuan', 'icon' => 'GiftIcon'],
            ]],
            ['title' => 'Tindak lanjut', 'items' => [
                ['label' => 'Ajukan tindak lanjut', 'href' => '/petugas/tindak-lanjut', 'icon' => 'TeachingIcon'],
                ['label' => 'Riwayat pembinaan', 'href' => '/petugas/catat-pembinaan', 'icon' => 'TeachingIcon'],
            ]],
            ['title' => 'Analisis', 'items' => [
                ['label' => 'Pengelompokan', 'href' => '/petugas/pengelompokan', 'icon' => 'Analytics01Icon'],
                ['label' => 'Rekomendasi', 'href' => '/petugas/rekomendasi', 'icon' => 'GiftIcon'],
                ['label' => 'Laporan', 'href' => '/petugas/laporan', 'icon' => 'FileExportIcon'],
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
                ['label' => 'Data UMKM', 'href' => '/petugas/umkm', 'icon' => 'Store01Icon'],
                ['label' => 'Pengelompokan', 'href' => '/pimpinan/pengelompokan', 'icon' => 'Analytics01Icon'],
                ['label' => 'Rekomendasi', 'href' => '/pimpinan/rekomendasi', 'icon' => 'GiftIcon'],
                ['label' => 'Laporan', 'href' => '/pimpinan/laporan', 'icon' => 'FileExportIcon'],
            ]],
            ['title' => 'Keputusan', 'items' => [
                ['label' => 'Persetujuan tindak lanjut', 'href' => '/pimpinan/persetujuan-tindak-lanjut', 'icon' => 'CheckListIcon'],
                ['label' => 'Riwayat pembinaan', 'href' => '/pimpinan/riwayat-pembinaan', 'icon' => 'TeachingIcon'],
            ]],
            ['title' => 'Akun', 'items' => [
                ['label' => 'Kelola petugas', 'href' => '/pimpinan/petugas', 'icon' => 'User02Icon'],
                ['label' => 'Profil akun', 'href' => '/akun', 'icon' => 'User02Icon'],
            ]],
        ];
    }
}
