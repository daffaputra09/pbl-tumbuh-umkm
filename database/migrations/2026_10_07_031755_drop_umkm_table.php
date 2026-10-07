<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Tabel stub lama (id_umkm, id_user, nama_usaha) sudah digantikan total
     * oleh tabel businesses. Migration ini sengaja dijalankan belakangan,
     * setelah businesses dipastikan aman dipakai.
     */
    public function up(): void
    {
        Schema::dropIfExists('umkm');
    }

    /**
     * Reverse the migrations.
     *
     * Tidak direkonstruksi balik, karena stub ini memang sudah ditinggalkan
     * dan tidak ada kode lain yang masih bergantung padanya.
     */
    public function down(): void
    {
        //
    }
};
