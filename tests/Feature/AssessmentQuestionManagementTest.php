<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentQuestion;
use App\Models\Business;
use App\Models\BusinessType;
use App\Models\ObstacleCategory;
use App\Models\QuestionOption;
use App\Models\User;
use Database\Seeders\AssessmentQuestionSeeder;
use Database\Seeders\ObstacleCategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssessmentQuestionManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $officer;

    private ObstacleCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->officer = User::factory()->create([
            'role' => User::ROLE_OFFICER,
            'is_active' => true,
        ]);

        $this->category = ObstacleCategory::factory()->create([
            'name' => 'Modal',
            'slug' => 'modal',
            'sort_order' => 1,
            'is_active' => true,
        ]);
    }

    public function test_officer_can_view_bank_soal_page(): void
    {
        $response = $this->actingAs($this->officer)->get(route('petugas.bank-soal.index'));

        $response->assertOk();
        $response->assertViewIs('petugas.bank-soal');
        $response->assertSee('Bank Soal Kuesioner');
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('petugas.bank-soal.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_unauthorized_roles_cannot_access_bank_soal(): void
    {
        $owner = User::factory()->create([
            'role' => User::ROLE_BUSINESS_OWNER,
            'is_active' => true,
        ]);

        $response = $this->actingAs($owner)->get(route('petugas.bank-soal.index'));
        $this->assertTrue(in_array($response->status(), [302, 403], true));

        $villageHead = User::factory()->create([
            'role' => User::ROLE_VILLAGE_HEAD,
            'is_active' => true,
        ]);

        $responseHead = $this->actingAs($villageHead)->get(route('petugas.bank-soal.index'));
        $this->assertTrue(in_array($responseHead->status(), [302, 403], true));
    }

    public function test_officer_can_create_likert_question_with_automatic_five_options(): void
    {
        $response = $this->actingAs($this->officer)->post(route('petugas.bank-soal.store'), [
            'obstacle_category_id' => $this->category->id,
            'type' => 'likert',
            'prompt' => 'Saya kesulitan menyediakan modal untuk mengembangkan usaha.',
            'help_text' => 'Fokus pada modal tunai untuk operasional.',
            'weight' => 1.00,
            'is_reverse_scored' => 0,
            'sort_order' => 1,
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('petugas.bank-soal.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('assessment_questions', [
            'obstacle_category_id' => $this->category->id,
            'type' => 'likert',
            'prompt' => 'Saya kesulitan menyediakan modal untuk mengembangkan usaha.',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $question = AssessmentQuestion::where('prompt', 'Saya kesulitan menyediakan modal untuk mengembangkan usaha.')->first();
        $this->assertNotNull($question);
        $this->assertCount(5, $question->options);

        $options = $question->options->sortBy('sort_order')->values();
        $this->assertSame('Sangat tidak setuju', $options[0]->label);
        $this->assertSame(0, $options[0]->score);
        $this->assertSame(1, $options[0]->value);

        $this->assertSame('Tidak setuju', $options[1]->label);
        $this->assertSame(25, $options[1]->score);

        $this->assertSame('Netral', $options[2]->label);
        $this->assertSame(50, $options[2]->score);

        $this->assertSame('Setuju', $options[3]->label);
        $this->assertSame(75, $options[3]->score);

        $this->assertSame('Sangat setuju', $options[4]->label);
        $this->assertSame(100, $options[4]->score);
        $this->assertSame(5, $options[4]->value);
    }

    public function test_officer_can_create_likert_question_even_when_form_sends_empty_options(): void
    {
        $response = $this->actingAs($this->officer)->post(route('petugas.bank-soal.store'), [
            'obstacle_category_id' => $this->category->id,
            'type' => 'likert',
            'prompt' => 'Saya kesulitan memisahkan uang pribadi dan usaha.',
            'help_text' => 'Pencatatan kas UMKM',
            'weight' => 1.00,
            'is_reverse_scored' => 0,
            'sort_order' => 2,
            'is_active' => 1,
            'options' => [
                ['label' => '', 'score' => '', 'value' => 1],
                ['label' => '', 'score' => '', 'value' => 2],
            ],
        ]);

        $response->assertRedirect(route('petugas.bank-soal.index'));
        $response->assertSessionHas('success');

        $question = AssessmentQuestion::where('prompt', 'Saya kesulitan memisahkan uang pribadi dan usaha.')->first();
        $this->assertNotNull($question);
        $this->assertCount(5, $question->options);
    }

    public function test_officer_can_create_single_choice_question_with_custom_options(): void
    {
        $response = $this->actingAs($this->officer)->post(route('petugas.bank-soal.store'), [
            'obstacle_category_id' => $this->category->id,
            'type' => 'single_choice',
            'prompt' => 'Seberapa sering Anda mencatat kas?',
            'weight' => 1.00,
            'sort_order' => 2,
            'is_active' => 1,
            'options' => [
                ['label' => 'Tidak pernah', 'score' => 100, 'value' => 1, 'sort_order' => 1],
                ['label' => 'Kadang-kadang', 'score' => 50, 'value' => 2, 'sort_order' => 2],
                ['label' => 'Setiap hari', 'score' => 0, 'value' => 3, 'sort_order' => 3],
            ],
        ]);

        $response->assertRedirect(route('petugas.bank-soal.index'));

        $question = AssessmentQuestion::where('prompt', 'Seberapa sering Anda mencatat kas?')->first();
        $this->assertNotNull($question);
        $this->assertSame('single_choice', $question->type);
        $this->assertCount(3, $question->options);
    }

    public function test_officer_can_update_question(): void
    {
        $question = AssessmentQuestion::factory()->create([
            'obstacle_category_id' => $this->category->id,
            'prompt' => 'Pertanyaan awal',
            'weight' => 1.00,
            'sort_order' => 1,
        ]);

        $response = $this->actingAs($this->officer)->put(route('petugas.bank-soal.update', $question), [
            'obstacle_category_id' => $this->category->id,
            'prompt' => 'Pertanyaan yang diperbarui',
            'help_text' => 'Catatan revisi',
            'weight' => 1.50,
            'sort_order' => 3,
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('petugas.bank-soal.index'));
        $this->assertDatabaseHas('assessment_questions', [
            'id' => $question->id,
            'prompt' => 'Pertanyaan yang diperbarui',
            'weight' => 1.50,
            'sort_order' => 3,
        ]);
    }

    public function test_officer_can_toggle_question_status(): void
    {
        $question = AssessmentQuestion::factory()->create([
            'obstacle_category_id' => $this->category->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->officer)->patch(route('petugas.bank-soal.toggle', $question));

        $response->assertRedirect();
        $this->assertFalse($question->refresh()->is_active);

        $this->actingAs($this->officer)->patch(route('petugas.bank-soal.toggle', $question));
        $this->assertTrue($question->refresh()->is_active);
    }

    public function test_validation_fails_for_invalid_input(): void
    {
        $response = $this->actingAs($this->officer)->post(route('petugas.bank-soal.store'), [
            'obstacle_category_id' => 99999, // non-existent
            'type' => 'invalid_type',
            'prompt' => '', // empty prompt
            'weight' => 'bukan_angka',
            'sort_order' => -1,
        ]);

        $response->assertSessionHasErrors([
            'obstacle_category_id' => 'Kategori kendala yang dipilih tidak ditemukan.',
            'type' => 'Tipe pertanyaan harus berupa Likert atau Pilihan Tunggal.',
            'prompt' => 'Teks pertanyaan wajib diisi.',
            'weight' => 'Bobot pertanyaan harus berupa angka.',
            'sort_order' => 'Urutan tampilan minimal bernilai 0.',
        ]);
    }

    public function test_officer_can_manage_question_options(): void
    {
        $question = AssessmentQuestion::factory()->create([
            'obstacle_category_id' => $this->category->id,
            'type' => 'single_choice',
        ]);

        // Add option
        $responseStore = $this->actingAs($this->officer)->post(route('petugas.bank-soal.options.store', $question), [
            'label' => 'Opsi Tambahan',
            'value' => 4,
            'score' => 60,
            'sort_order' => 4,
        ]);
        $responseStore->assertRedirect();

        $option = QuestionOption::where('assessment_question_id', $question->id)->where('label', 'Opsi Tambahan')->first();
        $this->assertNotNull($option);

        // Update option
        $responseUpdate = $this->actingAs($this->officer)->put(route('petugas.bank-soal.options.update', $option), [
            'label' => 'Opsi Direvisi',
            'value' => 4,
            'score' => 65,
            'sort_order' => 4,
        ]);
        $responseUpdate->assertRedirect();
        $this->assertSame('Opsi Direvisi', $option->refresh()->label);
        $this->assertSame(65, $option->score);

        // Delete option
        $responseDestroy = $this->actingAs($this->officer)->delete(route('petugas.bank-soal.options.destroy', $option));
        $responseDestroy->assertRedirect();
        $this->assertDatabaseMissing('question_options', ['id' => $option->id]);
    }

    public function test_deleting_question_with_assessment_answers_is_prevented(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_BUSINESS_OWNER]);
        $businessType = BusinessType::create(['name' => 'Kuliner', 'slug' => 'kuliner']);
        $business = Business::factory()->create([
            'user_id' => $owner->id,
            'business_type_id' => $businessType->id,
            'created_by' => $owner->id,
        ]);

        $question = AssessmentQuestion::factory()->create([
            'obstacle_category_id' => $this->category->id,
        ]);
        $option = QuestionOption::factory()->create([
            'assessment_question_id' => $question->id,
            'score' => 50,
        ]);

        $assessment = Assessment::create([
            'business_id' => $business->id,
            'filled_by' => $owner->id,
            'status' => 'completed',
            'is_current' => true,
        ]);

        AssessmentAnswer::create([
            'assessment_id' => $assessment->id,
            'assessment_question_id' => $question->id,
            'question_option_id' => $option->id,
            'score' => 50,
            'weight' => 1.00,
        ]);

        $response = $this->actingAs($this->officer)->delete(route('petugas.bank-soal.destroy', $question));

        $response->assertRedirect(route('petugas.bank-soal.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('assessment_questions', ['id' => $question->id]);
    }

    public function test_deleting_option_with_assessment_answers_is_prevented(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_BUSINESS_OWNER]);
        $businessType = BusinessType::create(['name' => 'Kerajinan', 'slug' => 'kerajinan']);
        $business = Business::factory()->create([
            'user_id' => $owner->id,
            'business_type_id' => $businessType->id,
            'created_by' => $owner->id,
        ]);

        $question = AssessmentQuestion::factory()->create([
            'obstacle_category_id' => $this->category->id,
        ]);
        $option = QuestionOption::factory()->create([
            'assessment_question_id' => $question->id,
            'score' => 50,
        ]);

        $assessment = Assessment::create([
            'business_id' => $business->id,
            'filled_by' => $owner->id,
            'status' => 'completed',
            'is_current' => true,
        ]);

        AssessmentAnswer::create([
            'assessment_id' => $assessment->id,
            'assessment_question_id' => $question->id,
            'question_option_id' => $option->id,
            'score' => 50,
            'weight' => 1.00,
        ]);

        $response = $this->actingAs($this->officer)->delete(route('petugas.bank-soal.options.destroy', $option));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('question_options', ['id' => $option->id]);
    }

    public function test_officer_can_safely_delete_unused_question_and_its_options(): void
    {
        $question = AssessmentQuestion::factory()->create([
            'obstacle_category_id' => $this->category->id,
        ]);
        $option = QuestionOption::factory()->create([
            'assessment_question_id' => $question->id,
        ]);

        $response = $this->actingAs($this->officer)->delete(route('petugas.bank-soal.destroy', $question));

        $response->assertRedirect(route('petugas.bank-soal.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('assessment_questions', ['id' => $question->id]);
        $this->assertDatabaseMissing('question_options', ['id' => $option->id]);
    }

    public function test_assessment_question_seeder_populates_twenty_likert_questions(): void
    {
        $this->seed(ObstacleCategorySeeder::class);
        $this->seed(AssessmentQuestionSeeder::class);

        $this->assertSame(20, AssessmentQuestion::count());
        $this->assertSame(100, QuestionOption::count());

        $categories = ObstacleCategory::whereIn('slug', ['modal', 'pemasaran', 'legalitas', 'produksi', 'digitalisasi'])->get();
        $this->assertCount(5, $categories);

        foreach ($categories as $cat) {
            $catQuestions = AssessmentQuestion::where('obstacle_category_id', $cat->id)->get();
            $this->assertCount(4, $catQuestions);

            foreach ($catQuestions as $q) {
                $this->assertSame('likert', $q->type);
                $this->assertCount(5, $q->options);
            }
        }
    }
}
