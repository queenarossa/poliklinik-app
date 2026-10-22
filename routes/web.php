<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\Admin\AdminPoliController;
use App\Http\Controllers\Admin\DokterController;
use App\Http\Controllers\Admin\PasienController;
use App\Http\Controllers\Admin\ObatController;
use App\Http\Controllers\Admin\PembayaranController as AdminPembayaranController;
use App\Http\Controllers\Dokter\JadwalPeriksaController;
use App\Http\Controllers\Dokter\PeriksaPasienController;
use App\Http\Controllers\Dokter\RiwayatPasienController;
use App\Http\Controllers\Pasien\DashboardController as PasienDashboardController;
use App\Http\Controllers\Pasien\PembayaranController as PasienPembayaranController;
use App\Http\Controllers\Pasien\PoliController as PasienPoliController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', function () {
    return view('welcome');
});

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::resource('polis', AdminPoliController::class);
    Route::resource('dokter', DokterController::class);
    Route::resource('pasien', PasienController::class);
    Route::resource('obat', ObatController::class);

    // Verifikasi pembayaran
    Route::get('/pembayaran', [AdminPembayaranController::class, 'index'])->name('admin.pembayaran.index');
    Route::get('/pembayaran/{id}', [AdminPembayaranController::class, 'show'])->name('admin.pembayaran.show');
    Route::post('/pembayaran/{id}/konfirmasi', [AdminPembayaranController::class, 'konfirmasi'])->name('admin.pembayaran.konfirmasi');
    Route::get('/pembayaran/{id}/bukti', [AdminPembayaranController::class, 'bukti'])->name('admin.pembayaran.bukti');

    // Export Excel
    Route::get('/export/dokter', [ExportController::class, 'dokter'])->name('admin.export.dokter');
    Route::get('/export/pasien', [ExportController::class, 'pasien'])->name('admin.export.pasien');
    Route::get('/export/obat', [ExportController::class, 'obat'])->name('admin.export.obat');
});

// Dokter Routes
Route::middleware(['auth', 'role:dokter'])->prefix('dokter')->group(function () {
    Route::get('/dashboard', function () {
        return view('dokter.dashboard');
    })->name('dokter.dashboard');

    Route::resource('jadwal-periksa', JadwalPeriksaController::class);

    Route::get('/periksa-pasien', [PeriksaPasienController::class, 'index'])
        ->name('periksa-pasien.index');
    Route::post('/periksa-pasien', [PeriksaPasienController::class, 'store'])
        ->name('periksa-pasien.store');
    Route::get('/periksa-pasien/{id}', [PeriksaPasienController::class, 'create'])
        ->name('periksa-pasien.create');

    Route::get('/riwayat-pasien', [RiwayatPasienController::class, 'index'])
        ->name('riwayat-pasien.index');
    Route::get('/riwayat-pasien/{id}', [RiwayatPasienController::class, 'show'])
        ->name('riwayat-pasien.show');

    // Export Excel (hanya data milik dokter yang login)
    Route::get('/export/jadwal-periksa', [ExportController::class, 'jadwalPeriksa'])->name('dokter.export.jadwal');
    Route::get('/export/riwayat-pasien', [ExportController::class, 'riwayatPasien'])->name('dokter.export.riwayat');
});

// Pasien Routes
Route::middleware(['auth', 'role:pasien'])->prefix('pasien')->group(function () {
    Route::get('/dashboard', [PasienDashboardController::class, 'index'])
        ->name('pasien.dashboard');
    Route::get('/antrian-live', [PasienDashboardController::class, 'live'])
        ->name('pasien.antrian.live');

    Route::get('/daftar', [PasienPoliController::class, 'get'])
        ->name('pasien.daftar');
    Route::post('/daftar', [PasienPoliController::class, 'submit'])
        ->name('pasien.daftar.submit');

    // Pembayaran
    Route::get('/pembayaran', [PasienPembayaranController::class, 'index'])->name('pasien.pembayaran.index');
    Route::get('/pembayaran/{id}', [PasienPembayaranController::class, 'show'])->name('pasien.pembayaran.show');
    Route::post('/pembayaran/{id}/upload', [PasienPembayaranController::class, 'upload'])->name('pasien.pembayaran.upload');
    Route::get('/pembayaran/{id}/bukti', [PasienPembayaranController::class, 'bukti'])->name('pasien.pembayaran.bukti');
});

// Notifikasi (tiap role memakai awalan URL sendiri supaya sidebar tampil benar)
foreach (['admin', 'dokter', 'pasien'] as $role) {
    Route::middleware(['auth', 'role:' . $role])->prefix($role)->group(function () use ($role) {
        Route::get('/notifikasi', [NotifikasiController::class, 'index'])
            ->name($role . '.notifikasi.index');
        Route::post('/notifikasi/baca-semua', [NotifikasiController::class, 'bacaSemua'])
            ->name($role . '.notifikasi.baca-semua');
        Route::post('/notifikasi/{id}/baca', [NotifikasiController::class, 'baca'])
            ->name($role . '.notifikasi.baca');
    });
}

// Profil & ganti password (semua role; tiap role memakai awalan URL sendiri agar sidebar tampil benar)
foreach (['admin', 'dokter', 'pasien'] as $role) {
    Route::middleware(['auth', 'role:' . $role])->prefix($role)->group(function () use ($role) {
        Route::get('/profil', [ProfilController::class, 'edit'])
            ->name($role . '.profil.edit');
        Route::put('/profil/password', [ProfilController::class, 'updatePassword'])
            ->middleware('throttle:6,1')   // batasi percobaan menebak password lama
            ->name($role . '.profil.password');
    });
}
