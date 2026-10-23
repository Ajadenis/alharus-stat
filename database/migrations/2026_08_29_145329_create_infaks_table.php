<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('infaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santris')->onDelete('cascade');
            $table->string('nisa');
            $table->string('bulan');
            $table->year('tahun');
            $table->decimal('jumlah', 10, 2)->default(50000);
            $table->date('tanggal_bayar')->nullable();
            $table->enum('status', ['belum_bayar', 'lunas', 'pending'])->default('belum_bayar');
            $table->string('metode_pembayaran')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('bukti_pembayaran')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->timestamps();

            $table->unique(['santri_id', 'bulan', 'tahun']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('infaks');
    }
};