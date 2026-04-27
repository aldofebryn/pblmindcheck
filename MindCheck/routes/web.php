<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScreeningController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\QuestionController;

// ── Pasien ──────────────────────────────────────────────────────
Route::get('/',            [ScreeningController::class, 'landing'])->name('landing');
Route::get('/token',       [ScreeningController::class, 'tokenPage'])->name('token');
Route::post('/token',      [ScreeningController::class, 'processToken'])->name('token.process');
Route::get('/screening',   [ScreeningController::class, 'screening'])->name('screening');
Route::post('/screening',  [ScreeningController::class, 'submitScreening'])->name('screening.submit');
Route::get('/hasil/{screening}', [ScreeningController::class, 'hasil'])->name('hasil');
Route::get('/riwayat',     [ScreeningController::class, 'history'])->name('history');

// ── Admin ───────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login',  [AdminController::class, 'loginPage'])->name('login');
    Route::post('/login', [AdminController::class, 'login'])->name('login.post');
    Route::post('/logout', [AdminController::class, 'logout'])->name('logout');

    Route::get('/',       [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/pengaturan', [AdminController::class, 'settings'])->name('settings');
    Route::get('/token/{token}', [AdminController::class, 'tokenDetail'])->name('token.detail');

    Route::resource('questions', QuestionController::class)->except(['show']);
    Route::get('questions/trash', [QuestionController::class, 'trash'])->name('questions.trash');
    Route::patch('questions/{id}/restore', [QuestionController::class, 'restore'])->name('questions.restore');
    Route::delete('questions/{id}/force', [QuestionController::class, 'forceDelete'])->name('questions.force');
});
