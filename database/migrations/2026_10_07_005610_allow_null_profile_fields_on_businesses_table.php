<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Registration stores the business name before the owner finishes the profile form.
     */
    public function up(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropForeign(['business_type_id']);
        });

        Schema::table('businesses', function (Blueprint $table) {
            $table->unsignedBigInteger('business_type_id')->nullable()->change();
            $table->string('phone')->nullable()->change();
            $table->text('address')->nullable()->change();
            $table->string('hamlet')->nullable()->change();
            $table->unsignedSmallInteger('established_year')->nullable()->change();
            $table->unsignedSmallInteger('employee_count')->nullable()->change();
        });

        Schema::table('businesses', function (Blueprint $table) {
            $table->foreign('business_type_id')->references('id')->on('business_types');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropForeign(['business_type_id']);
        });

        Schema::table('businesses', function (Blueprint $table) {
            $table->unsignedBigInteger('business_type_id')->nullable(false)->change();
            $table->string('phone')->nullable(false)->change();
            $table->text('address')->nullable(false)->change();
            $table->string('hamlet')->nullable(false)->change();
            $table->unsignedSmallInteger('established_year')->nullable(false)->change();
            $table->unsignedSmallInteger('employee_count')->nullable(false)->change();
        });

        Schema::table('businesses', function (Blueprint $table) {
            $table->foreign('business_type_id')->references('id')->on('business_types');
        });
    }
};
