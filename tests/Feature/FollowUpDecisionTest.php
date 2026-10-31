<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\FollowUp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FollowUpDecisionTest extends TestCase
{
    use RefreshDatabase;

    public function test_village_head_rejects_a_follow_up(): void
    {
        $head = User::factory()->create(['role' => User::ROLE_VILLAGE_HEAD]);
        $followUp = $this->pendingFollowUp();

        $this->actingAs($head)
            ->patchJson(route('pimpinan.persetujuan-tindak-lanjut.update', $followUp), [
                'status' => 'rejected',
                'decision_note' => 'Kuota program sudah penuh.',
            ])
            ->assertOk()
            ->assertJsonPath('message', 'Keputusan berhasil disimpan.');

        $followUp->refresh();
        $this->assertSame('rejected', $followUp->status);
        $this->assertSame($head->id, $followUp->decided_by);
        $this->assertSame('Kuota program sudah penuh.', $followUp->decision_note);
        $this->assertNotNull($followUp->decided_at);
    }

    public function test_village_head_cannot_decide_their_own_submission(): void
    {
        $head = User::factory()->create(['role' => User::ROLE_VILLAGE_HEAD]);
        $followUp = $this->pendingFollowUp($head);

        $this->actingAs($head)
            ->patchJson(route('pimpinan.persetujuan-tindak-lanjut.update', $followUp), [
                'status' => 'approved',
                'decision_note' => 'Saya setujui sendiri.',
            ])
            ->assertForbidden()
            ->assertJsonPath('message', 'Anda tidak dapat menyetujui atau menolak pengajuan Anda sendiri.');

        $this->assertSame('pending', $followUp->refresh()->status);
    }

    public function test_decision_without_a_note_is_rejected(): void
    {
        $head = User::factory()->create(['role' => User::ROLE_VILLAGE_HEAD]);
        $followUp = $this->pendingFollowUp();

        $this->actingAs($head)
            ->patchJson(route('pimpinan.persetujuan-tindak-lanjut.update', $followUp), [
                'status' => 'rejected',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('decision_note');

        $this->assertSame('pending', $followUp->refresh()->status);
    }

    private function pendingFollowUp(?User $submitter = null): FollowUp
    {
        $officer = $submitter ?? User::factory()->create(['role' => User::ROLE_OFFICER]);
        $owner = User::factory()->create(['role' => User::ROLE_BUSINESS_OWNER]);
        Business::factory()->create([
            'user_id' => $owner->id,
            'created_by' => $officer->id,
        ]);

        return FollowUp::query()->create([
            'business_id' => $owner->id,
            'submitted_by' => $officer->id,
            'planned_activity' => 'Pendampingan modal',
            'planned_on' => '2026-11-20',
            'submission_note' => 'Pemilik sudah mendaftar minat.',
            'status' => 'pending',
        ]);
    }
}
