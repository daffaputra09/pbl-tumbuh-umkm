<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * PostgreSQL keeps the old enum check after the role column becomes a string.
     * That check still allows only petugas, pimpinan, and umkm.
     */
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check');

        DB::table('users')->where('role', 'umkm')->update(['role' => 'business_owner']);
        DB::table('users')->where('role', 'petugas')->update(['role' => 'officer']);
        DB::table('users')->where('role', 'pimpinan')->update(['role' => 'village_head']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'pgsql') {
            return;
        }

        DB::table('users')->where('role', 'business_owner')->update(['role' => 'umkm']);
        DB::table('users')->where('role', 'officer')->update(['role' => 'petugas']);
        DB::table('users')->where('role', 'village_head')->update(['role' => 'pimpinan']);

        DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('petugas', 'pimpinan', 'umkm'))");
    }
};
