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
        Schema::create('businesses', function (Blueprint $table) {
	    $table->id();
            // Kosong kalau UMKM didata petugas dan belum punya akun sendiri.
            $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->foreignId('business_type_id')->constrained();
            $table->foreignId('created_by')->constrained('users'); // petugas atau pemilik yang mengisi

            $table->string('business_name');
            $table->string('owner_name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->text('address');
            $table->string('hamlet'); // Dusun
            $table->string('rt')->nullable();
            $table->string('rw')->nullable();
            $table->unsignedSmallInteger('established_year');
            $table->unsignedSmallInteger('employee_count');
            $table->text('description')->nullable();

            $table->string('operational_status')->default('active'); // active, inactive
            $table->string('verification_status')->default('pending'); // pending, verified, needs_revision, rejected
            $table->text('verification_note')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->timestamp('verified_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
