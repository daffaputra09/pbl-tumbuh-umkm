<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign('products_business_id_foreign');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreign('business_id')
                ->references('id')
                ->on('businesses')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign('products_business_id_foreign');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreign('business_id')
                ->references('user_id')
                ->on('businesses')
                ->cascadeOnDelete();
        });
    }
};
