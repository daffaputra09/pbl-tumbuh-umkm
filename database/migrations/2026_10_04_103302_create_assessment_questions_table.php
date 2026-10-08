<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('obstacle_category_id')->constrained()->cascadeOnDelete();
            $table->string('type')->default('likert');
            $table->text('prompt');
            $table->text('help_text')->nullable();
            $table->decimal('weight', 4, 2)->default(1);
            $table->boolean('is_reverse_scored')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_questions');
    }
};