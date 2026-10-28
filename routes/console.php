<?php

use Illuminate\Support\Facades\Schedule;
use App\Helpers\SantriHelper;

// ========================================
// KENAIAKAN KELAS OTOMATIS SETIAP 1 JUNI
// ========================================
Schedule::call(function () {
    SantriHelper::prosesKenaikanKelas();
})->yearlyOn(6, 1, '00:01')->name('kenaikan-kelas-otomatis');