<?php

use App\Http\Controllers\Api\V1\BragApiController;
use Illuminate\Support\Facades\Route;

// Rotas de API versionada (V1)
Route::prefix('v1')->group(function () {
    Route::get('/brags', [BragApiController::class, 'index'])->name('api.v1.brags.index');
    Route::post('/brags', [BragApiController::class, 'store'])->name('api.v1.brags.store');
    Route::get('/brags/{id}', [BragApiController::class, 'show'])->name('api.v1.brags.show');
});

// Rota direta para compatibilidade com o micro-BFF / HttpClient
Route::post('/brag', [BragApiController::class, 'store'])->name('api.brag.generate');
