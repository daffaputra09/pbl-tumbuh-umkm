<?php

namespace App\Policies;

use App\Models\ProgramRecommendation;
use App\Models\User;

class ProgramRecommendationPolicy
{
    public function view(User $user, ProgramRecommendation $programRecommendation): bool
    {
        return $this->ownsActiveRecommendation($user, $programRecommendation);
    }

    public function apply(User $user, ProgramRecommendation $programRecommendation): bool
    {
        return $this->ownsActiveRecommendation($user, $programRecommendation);
    }

    private function ownsActiveRecommendation(User $user, ProgramRecommendation $programRecommendation): bool
    {
        if (! $programRecommendation->is_active) {
            return false;
        }

        $business = $user->business;

        return $business !== null
            && (int) $business->id === (int) $programRecommendation->business_id;
    }
}
