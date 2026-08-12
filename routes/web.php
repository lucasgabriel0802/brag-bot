<?php

use App\Http\Controllers\BragController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BragController::class, 'index'])->name('dashboard');
Route::get('/brag/{id}', [BragController::class, 'show'])->name('brag.show');
Route::post('/brag/generate', [BragController::class, 'generate'])->name('brag.generate');
