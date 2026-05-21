<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Patient;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Admin\SettingsController;

// ── Landing ──────────────────────────────────────────────────────
Route::get('/', fn() => view('welcome', [
    'totalSesi' => \App\Models\Screening::whereNotNull('selesai_at')->count(),
]))->name('landing');

// ── Pasien ───────────────────────────────────────────────────────
Route::get('/patient-login',  [Patient\AuthController::class, 'showLogin'])->name('patient.login');
Route::post('/patient-login', [Patient\AuthController::class, 'process'])->name('patient.login.process')->middleware('throttle:10,1');
Route::post('/patient-logout',[Patient\AuthController::class, 'logout'])->name('patient.logout');

Route::get('/screening',  [Patient\ScreeningController::class, 'show'])->name('screening');
Route::post('/screening', [Patient\ScreeningController::class, 'submit'])->name('screening.submit');
Route::post('/screening/autosave', [Patient\ScreeningController::class, 'autosave'])->name('screening.autosave');
Route::get('/hasil/{screening}', [Patient\ScreeningController::class, 'hasil'])->name('hasil');

Route::get('/dashboard',         [Patient\DashboardController::class, 'index'])->name('patient.dashboard');
Route::get('/pengaturan-pasien', [Patient\DashboardController::class, 'settings'])->name('patient.settings');
Route::put('/pengaturan-pasien', [Patient\DashboardController::class, 'updateSettings'])->name('patient.settings.update');

// ── Admin ────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {

    // Auth
    Route::get('/login',  [Admin\AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [Admin\AuthController::class, 'login'])->name('login.post')->middleware('throttle:10,1');
    Route::post('/logout',[Admin\AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Pengaturan
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // Manajemen Pasien
    Route::resource('patients', Admin\PatientController::class);

    // Manajemen Pertanyaan — trash HARUS sebelum resource agar tidak ditangkap sebagai {question}
    Route::get('questions/trash',            [Admin\QuestionController::class, 'trash'])->name('questions.trash');
    Route::patch('questions/{id}/restore',   [Admin\QuestionController::class, 'restore'])->name('questions.restore');
    Route::delete('questions/{id}/force',    [Admin\QuestionController::class, 'forceDelete'])->name('questions.force');
    Route::resource('questions', Admin\QuestionController::class)->except(['show']);

    // Manajemen Akun Admin — trash HARUS sebelum resource
    Route::get('admins/trash',               [Admin\AdminUserController::class, 'trash'])->name('admins.trash');
    Route::patch('admins/{id}/restore',      [Admin\AdminUserController::class, 'restore'])->name('admins.restore');
    Route::delete('admins/{id}/force',       [Admin\AdminUserController::class, 'forceDelete'])->name('admins.force');
    Route::get('admins',                     [Admin\AdminUserController::class, 'index'])->name('admins.index');
    Route::get('admins/create',              [Admin\AdminUserController::class, 'create'])->name('admins.create');
    Route::post('admins',                    [Admin\AdminUserController::class, 'store'])->name('admins.store');
    Route::get('admins/{id}/edit',           [Admin\AdminUserController::class, 'edit'])->name('admins.edit');
    Route::put('admins/{id}',                [Admin\AdminUserController::class, 'update'])->name('admins.update');
    Route::delete('admins/{id}',             [Admin\AdminUserController::class, 'destroy'])->name('admins.delete');
});
