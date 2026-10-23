<?php

use App\Http\Controllers\Api\MedicineController;
use App\Support\ApiFormat;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    Route::get('/ping', fn () => ApiFormat::success([
        'message' => 'pong',
        'time' => now()->toIso8601String(),
    ]))->name('ping');

    Route::apiResource('medicines', MedicineController::class);
});
