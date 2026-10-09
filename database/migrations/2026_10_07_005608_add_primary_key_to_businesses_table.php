<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Production created businesses before id was added to the original
     * migration. program_recommendations references businesses.id.
     */
    public function up(): void
    {
        if (Schema::hasColumn('businesses', 'id')) {
            return;
        }

        Schema::table('businesses', function (Blueprint $table) {
            $table->id();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Fresh installs get id from create_businesses_table. Dropping it
        // here would remove that primary key and break foreign keys.
    }
};
