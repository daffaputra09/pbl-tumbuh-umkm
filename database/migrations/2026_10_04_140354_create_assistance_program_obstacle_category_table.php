<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assistance_program_obstacle_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assistance_program_id')->constrained()->cascadeOnDelete();
            $table->foreignId('obstacle_category_id')->constrained()->cascadeOnDelete();
            $table->string('minimum_level')->default('moderate');
            $table->unique(['assistance_program_id', 'obstacle_category_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assistance_program_obstacle_category');
    }
};
