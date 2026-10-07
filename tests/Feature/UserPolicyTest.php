<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_each_role_updates_only_their_own_account(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_BUSINESS_OWNER]);
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $head = User::factory()->create(['role' => User::ROLE_VILLAGE_HEAD]);

        foreach ([$owner, $officer, $head] as $user) {
            $this->assertTrue($user->can('view', $user));
            $this->assertTrue($user->can('update', $user));
        }

        $this->assertFalse($owner->can('update', $officer));
        $this->assertFalse($officer->can('update', $head));
        $this->assertFalse($head->can('view', $owner));
    }
}
