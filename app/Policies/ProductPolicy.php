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
        if ($business !== null && ! $this->belongsToBusiness($product, $business)) {
            return false;
        }

        if ($user->hasRole(User::ROLE_OFFICER)) {
            return true;
        }

        return $user->hasRole(User::ROLE_BUSINESS_OWNER)
            && $product->business_id !== null
            && (int) $product->business_id === $user->id;
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

    private function belongsToBusiness(Product $product, Business $business): bool
    {
        if ($product->business_id === null || $business->user_id === null) {
            return false;
        }

        return (int) $product->business_id === (int) $business->user_id;
    }
}
