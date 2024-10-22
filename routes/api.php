<?php

use App\Http\Controllers\Api\Admin\BagianController;
use App\Http\Controllers\Api\Admin\JenisRapatController;
use App\Http\Controllers\Api\Admin\KendaraanController;
use App\Http\Controllers\Api\Admin\KonsumsiController;
use App\Http\Controllers\Api\Admin\LinkController;
use App\Http\Controllers\Api\Admin\PresensiController;
use App\Http\Controllers\Api\Admin\RuanganController;
use App\Http\Controllers\Api\Admin\SendViconController as AdminSendViconController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\SendViconController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('auth/login', [AuthController::class, 'login'])->name('login');
Route::middleware('auth.api')->group(function () {
    Route::get('auth/user', [AuthController::class, 'currentUser']);
    Route::post('auth/logout', [AuthController::class, 'logout']);
});

Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
Route::get('vicon', [SendViconController::class, 'index'])->name('vicon.index');

// API membuat absensi
Route::post('absensis/store', [PresensiController::class, 'store'])->name('store');

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'auth.api'], function () {

    // Sendvicon Admin
    Route::group(['prefix' => 'vicon', 'as' => 'vicon.'], function () {
        Route::get('/', [AdminSendViconController::class, 'index'])->name('index');
        Route::post('store', [AdminSendViconController::class, 'store'])->name('store');
        Route::get('{id}/show', [AdminSendViconController::class, 'show'])->name('show');
        Route::get('{id}/presensis', [AdminSendViconController::class, 'presensis'])->name('presensis');
        Route::post('{id}/presensis/status', [AdminSendViconController::class, 'presensiStatus'])->name('presensis.status');
        Route::post('cancel', [AdminSendViconController::class, 'destroy'])->name('destroy');
        Route::get('export', [AdminSendViconController::class, 'export'])->name('export');

        Route::group(['prefix' => 'paginate', 'as' => 'paginate.'], function () {
            Route::get('/', [AdminSendViconController::class, 'pagination'])->name('index');
            Route::get('today', [AdminSendViconController::class, 'paginationToday'])->name('today');
            Route::get('report', [AdminSendViconController::class, 'paginationReport'])->name('report');
        });

        // Belum digunakan (petugas tidak ada)
        Route::group(['prefix' => 'notification', 'as' => 'notification.'], function () {
            Route::get('/', [AdminSendViconController::class, 'notification'])->name('index');
            // Route::get('today', [AdminSendViconController::class, 'notificationToday'])->name('today');
        });

        // Presensi
        Route::group(['prefix' => 'presensis', 'as' => 'presensis.'], function () {
            Route::get('/', [PresensiController::class, 'index'])->name('index');
            Route::get('{id}/show', [PresensiController::class, 'show'])->name('presensis.show');
        });
    });

    // agenda kendaraan
    Route::group(['prefix' => 'agenda-kendaraan', 'as' => 'agenda-kendaraan.'], function () {});

    // user
    Route::group(['prefix' => 'users', 'as' => 'users.'], function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
    });

    // bagian
    Route::group(['prefix' => 'bagians', 'as' => 'bagians.'], function () {
        Route::get('/', [BagianController::class, 'index'])->name('index');
        Route::get('{id}/show', [BagianController::class, 'show'])->name('show');
    });

    // Jenis rapat
    Route::group(['prefix' => 'jenis-rapat', 'as' => 'jenis-rapat.'], function () {
        Route::get('/', [JenisRapatController::class, 'index'])->name('index');
        Route::get('{id}/show', [JenisRapatController::class, 'show'])->name('show');
    });

    // Kendaraan
    Route::group(['prefix' => 'kendaraans', 'as' => 'kendaraans.'], function () {
        Route::get('/', [KendaraanController::class, 'index'])->name('index');
        Route::get('{id}/show', [KendaraanController::class, 'show'])->name('show');
    });

    // links
    Route::group(['prefix' => 'links', 'as' => 'links.'], function () {
        Route::get('/', [LinkController::class, 'index'])->name('index');
        Route::get('{id}/show', [LinkController::class, 'show'])->name('show');
    });

    // Ruangans
    Route::group(['prefix' => 'ruangans', 'as' => 'ruangans.'], function () {
        Route::get('/', [RuanganController::class, 'index'])->name('index');
        Route::get('{id}/show', [RuanganController::class, 'show'])->name('show');
    });

    // Konsumsi
    Route::group(['prefix' => 'comsumptions', 'as' => 'comsumptions.'], function () {
        Route::get('/', [KonsumsiController::class, 'index'])->name('index');
        Route::get('{id}/show', [KonsumsiController::class, 'show'])->name('show');
        Route::post('{id}/update', [KonsumsiController::class, 'update'])->name('update');
        Route::post('{id}/destroy', [KonsumsiController::class, 'destroy'])->name('destroy');
        Route::post('{id}/approve', [KonsumsiController::class, 'approve'])->name('approve');
    });
});
