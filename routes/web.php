<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KendaraanGsController;
use App\Http\Controllers\KendaraanOperasionalController;
use App\Http\Controllers\MasterHargaBbmVendorController;
use App\Http\Controllers\TransaksiPengisianBbmController;
use App\Http\Controllers\ScanQrController;
use App\Http\Controllers\MonitoringMingguanController;
use App\Http\Controllers\MonitoringBulananController;
use App\Http\Controllers\StandarKonsumsiBbmController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.attempt');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    Route::middleware('role:Admin Finance,View Only')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard.index');
    });

    Route::middleware('role:Admin Finance')->group(function () {
        Route::resource(
            'kendaraan-operasional',
            KendaraanOperasionalController::class
        )->except(['show']);

        Route::resource(
            'kendaraan-gs',
            KendaraanGsController::class
        )
            ->except(['show'])
            ->parameters([
                'kendaraan-gs' => 'kendaraanGs',
            ]);

        Route::resource(
            'master-harga-bbm-vendor',
            MasterHargaBbmVendorController::class
        )
            ->except(['show'])
            ->parameters([
                'master-harga-bbm-vendor' => 'masterHargaBbmVendor',
            ]);

        Route::resource(
            'standar-konsumsi-bbm',
            StandarKonsumsiBbmController::class
        )
            ->except([
                'create',
                'store',
                'show',
                'destroy',
            ])
            ->parameters([
                'standar-konsumsi-bbm' => 'id',
            ]);

        Route::get(
            '/transaksi-pengisian-bbm',
            [TransaksiPengisianBbmController::class, 'index']
        )->name('transaksi-pengisian-bbm.index');

        Route::get(
            '/transaksi-pengisian-bbm/{id}/edit',
            [TransaksiPengisianBbmController::class, 'edit']
        )->name('transaksi-pengisian-bbm.edit');

        Route::put(
            '/transaksi-pengisian-bbm/{id}',
            [TransaksiPengisianBbmController::class, 'update']
        )->name('transaksi-pengisian-bbm.update');

        Route::get(
            '/monitoring/mingguan',
            [MonitoringMingguanController::class, 'index']
        )->name('monitoring.mingguan');

        Route::get(
            '/monitoring/mingguan/pdf',
            [MonitoringMingguanController::class, 'downloadPdf']
        )->name('monitoring.mingguan.pdf');

        Route::get(
            '/monitoring/bulanan',
            [MonitoringBulananController::class, 'index']
        )->name('monitoring.bulanan');

        Route::get(
            '/monitoring/bulanan/ai-insight',
            [MonitoringBulananController::class, 'aiInsight']
        )->name('monitoring.bulanan.ai-insight');

        Route::get(
            '/monitoring/bulanan/pdf',
            [MonitoringBulananController::class, 'downloadPdf']
        )->name('monitoring.bulanan.pdf');
    });
});

Route::get(
    '/scan',
    [ScanQrController::class, 'index']
)->name('scan.index');

Route::post(
    '/scan/find',
    [ScanQrController::class, 'find']
)->name('scan.find');

Route::get(
    '/scan/qr/{qrCode}',
    [ScanQrController::class, 'redirectFromQr']
)->name('scan.qr');

Route::get(
    '/transaksi-pengisian-bbm/create',
    [TransaksiPengisianBbmController::class, 'create']
)->name('transaksi-pengisian-bbm.create');

Route::post(
    '/transaksi-pengisian-bbm',
    [TransaksiPengisianBbmController::class, 'store']
)->name('transaksi-pengisian-bbm.store');

Route::delete(
    '/transaksi-pengisian-bbm/{id}',
    [TransaksiPengisianBbmController::class, 'destroy']
)->name('transaksi-pengisian-bbm.destroy');

Route::get('/', function () {
    return redirect()->route('login');
});