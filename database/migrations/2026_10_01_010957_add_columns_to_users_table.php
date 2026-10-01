<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('business_owner')->change();
            $table->string('password')->nullable()->change();
            $table->string('phone')->nullable();
            $table->boolean('is_active')->default(true);
        });

        DB::table('users')->where('role', 'umkm')->update(['role' => 'business_owner']);
        DB::table('users')->where('role', 'petugas')->update(['role' => 'officer']);
        DB::table('users')->where('role', 'pimpinan')->update(['role' => 'village_head']);

        if (Schema::hasColumn('users', 'status_akun')) {
            DB::table('users')->where('status_akun', '!=', 'aktif')->update(['is_active' => false]);

            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('status_akun');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * Rows with a null password cannot be restored, because the original column was required.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('status_akun')->default('aktif');
        });

        DB::table('users')->where('is_active', false)->update(['status_akun' => 'nonaktif']);

        DB::table('users')->where('role', 'business_owner')->update(['role' => 'umkm']);
        DB::table('users')->where('role', 'officer')->update(['role' => 'petugas']);
        DB::table('users')->where('role', 'village_head')->update(['role' => 'pimpinan']);

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['petugas', 'pimpinan', 'umkm'])->default('umkm')->change();
            $table->string('password')->nullable(false)->change();
            $table->dropColumn(['phone', 'is_active']);
        });
    }
};
