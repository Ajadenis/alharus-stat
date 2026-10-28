<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('santris', function (Blueprint $table) {
            // Tahun masuk (angkatan)
            $table->year('tahun_masuk')->nullable()->after('nama');

            // Kelas sekarang (MDA 1, MDA 2, MDA 3, MDA 4)
            $table->string('kelas_sekarang')->nullable()->after('tahun_masuk');

            // Status (aktif, lulus, keluar)
            $table->enum('status_santri', ['aktif', 'lulus', 'keluar'])
                  ->default('aktif')
                  ->after('kelas_sekarang');
        });
    }

    public function down(): void
    {
        Schema::table('santris', function (Blueprint $table) {
            $table->dropColumn(['tahun_masuk', 'kelas_sekarang', 'status_santri']);
        });
    }
};