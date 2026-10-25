<?php

namespace App\Policies;

use App\Models\Business;
use App\Models\Product;
use App\Models\User;

/**
 * Petugas mendampingi produk usaha mana pun. Pemilik hanya produk usahanya.
 * Kepala desa tidak mengubah produk.
 */
class ProductPolicy
{
    public function viewAny(User $user, Business $business): bool
    {
        return $this->manages($user, $business);
    }

    public function create(User $user, Business $business): bool
    {
        return $this->manages($user, $business);
    }

    public function update(User $user, Product $product, ?Business $business = null): bool
    {
        // products.business_id menunjuk ke businesses.id (bukan user_id), jadi
        // usaha pemilik produk dicari lewat relasi itu. UMKM yang didata petugas
        // tidak punya user_id, tapi tetap punya id, jadi tetap bisa diproses.
        $business ??= $product->business;

        if ($business === null || (int) $product->business_id !== (int) $business->id) {
            return false;
        }

        return $this->manages($user, $business);
    }

    private function manages(User $user, Business $business): bool
    {
        if ($user->hasRole(User::ROLE_OFFICER)) {
            return true;
        }

        return $user->hasRole(User::ROLE_BUSINESS_OWNER)
            && $business->user_id !== null
            && (int) $business->user_id === $user->id;
    }
}
