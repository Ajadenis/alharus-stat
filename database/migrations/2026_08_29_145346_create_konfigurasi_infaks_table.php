<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('konfigurasi_infaks', function (Blueprint $table) {
            $table->id();
            $table->string('nama_infak')->default('Infak Bulanan');
            $table->decimal('nominal', 10, 2)->default(50000);
            $table->integer('tahun')->nullable();
            $table->boolean('is_default')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('konfigurasi_infaks');
    }
};