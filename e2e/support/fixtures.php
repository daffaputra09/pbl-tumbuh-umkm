<?php

/*
 * Fixture data untuk tes Playwright. Dipanggil dari e2e/support/helpers.js:
 *   php e2e/support/fixtures.php <perintah> '<json argumen>'
 * Perintah mencetak satu baris JSON di akhir keluaran.
 */

use App\Models\AssessmentQuestion;
use App\Models\AssistanceProgram;
use App\Models\Business;
use App\Models\BusinessType;
use App\Models\CoachingSession;
use App\Models\FollowUp;
use App\Models\ObstacleCategory;
use App\Models\User;
use Illuminate\Support\Facades\DB;

require __DIR__.'/../../vendor/autoload.php';
$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$command = $argv[1] ?? '';
$args = json_decode($argv[2] ?? '{}', true) ?: [];

function answersFor(string $level = 'high'): array
{
    $answers = [];
    $questions = AssessmentQuestion::query()
        ->where('is_active', true)
        ->whereHas('obstacleCategory', fn ($q) => $q->where('is_active', true))
        ->with('options')
        ->get();

    foreach ($questions as $question) {
        $option = $level === 'high'
            ? $question->options->sortByDesc('score')->first()
            : $question->options->sortBy('score')->first();
        $answers[] = ['questionId' => $question->id, 'optionId' => $option->id];
    }

    return $answers;
}

switch ($command) {
    case 'owner':
        $email = $args['email'];
        $user = User::query()->updateOrCreate(['email' => $email], [
            'name' => $args['name'] ?? 'Pemilik Uji',
            'password' => 'Rahasia123',
            'role' => User::ROLE_BUSINESS_OWNER,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $business = Business::query()->updateOrCreate(['user_id' => $user->id], [
            'business_type_id' => BusinessType::query()->value('id'),
            'created_by' => $user->id,
            'business_name' => $args['business'] ?? 'Usaha Uji',
            'owner_name' => $user->name,
            'phone' => '081200000000',
            'address' => 'Jl. Uji No. 1',
            'hamlet' => 'Krajan',
            'established_year' => 2019,
            'employee_count' => 2,
        ]);
        echo json_encode([
            'userId' => $user->id,
            'businessId' => $business->id,
            'email' => $email,
            'answersHigh' => answersFor('high'),
            'answersLow' => answersFor('low'),
        ]);
        break;

    case 'deactivate':
        User::query()->where('email', $args['email'])->update(['is_active' => false]);
        echo json_encode(['ok' => true]);
        break;

    case 'program':
        $officer = User::query()->where('role', User::ROLE_OFFICER)->firstOrFail();
        $program = AssistanceProgram::query()->create([
            'name' => $args['name'],
            'provider' => 'Desa Tumbuh',
            'description' => 'Program fixture uji.',
            'status' => AssistanceProgram::STATUS_ACTIVE,
            'created_by' => $officer->id,
        ]);
        foreach (ObstacleCategory::query()->where('is_active', true)->get() as $category) {
            $program->obstacleCategories()->attach($category->id, ['minimum_level' => 'moderate']);
        }
        echo json_encode(['programId' => $program->id]);
        break;

    case 'followup':
        $officer = User::query()->where('role', User::ROLE_OFFICER)->firstOrFail();
        $head = User::query()->where('role', User::ROLE_VILLAGE_HEAD)->firstOrFail();
        $status = $args['status'] ?? 'pending';
        $followUp = FollowUp::query()->create([
            'business_id' => $args['userId'],
            'submitted_by' => $officer->id,
            'planned_activity' => $args['activity'] ?? 'Pendampingan uji',
            'planned_on' => '2026-12-01',
            'submission_note' => 'Catatan pengajuan uji.',
            'status' => $status,
            'decided_by' => $status === 'pending' ? null : $head->id,
            'decision_note' => $status === 'pending' ? null : 'Keputusan uji.',
            'decided_at' => $status === 'pending' ? null : now(),
        ]);
        echo json_encode(['followUpId' => $followUp->id]);
        break;

    case 'session':
        $officer = User::query()->where('role', User::ROLE_OFFICER)->firstOrFail();
        $followUp = FollowUp::query()->findOrFail($args['followUpId']);
        $session = CoachingSession::query()->create([
            'follow_up_id' => $followUp->id,
            'business_id' => $followUp->business_id,
            'recorded_by' => $officer->id,
            'title' => $args['title'],
            'held_on' => '2026-11-10',
            'description' => 'Sesi uji.',
            'status' => 'completed',
        ]);
        echo json_encode(['sessionId' => $session->id]);
        break;

    case 'sql':
        echo json_encode(DB::select($args['query']));
        break;

    default:
        fwrite(STDERR, "Perintah tidak dikenal: {$command}\n");
        exit(1);
}
echo "\n";
