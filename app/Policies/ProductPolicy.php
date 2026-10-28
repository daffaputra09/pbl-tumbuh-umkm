<?php

namespace App\Policies;

use App\Models\Business;
use App\Models\Product;
use App\Models\User;

/**
 * Pengelolaan produk hanya boleh dilakukan oleh pelaku UMKM pemilik usaha terkait.
 * Petugas dan Kepala Desa tidak memiliki akses untuk mengelola produk.
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
        $business ??= $product->business;

        if ($business === null || (int) $product->business_id !== (int) $business->id) {
            return false;
        }

        return $this->manages($user, $business);
    }

    public function delete(User $user, Product $product, ?Business $business = null): bool
    {
        return $this->update($user, $product, $business);
    }

    private function manages(User $user, Business $business): bool
    {
        return $user->hasRole(User::ROLE_BUSINESS_OWNER)
            && $business->user_id !== null
            && (int) $business->user_id === (int) $user->id;
    }
}
