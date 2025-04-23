<?php

use App\Http\Controllers\Admin\AbsensiController;
use App\Http\Controllers\Admin\AgendaKendaraanController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MasterJenisRapatController;
use App\Http\Controllers\BagianController;
use App\Http\Controllers\DashboardAgendaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\MasterKendaraanController;
use App\Http\Controllers\MasterPenggunaController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\HakAksesController;
use App\Http\Controllers\KamarController;
use App\Http\Controllers\KasKecilController;
use App\Http\Controllers\SendViconController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KonsumsiController;
use App\Models\KasKecil;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MakanSiangController;
use App\Http\Controllers\MakanSiangControllerController;
use App\Http\Controllers\KartuController;
use App\Http\Controllers\MKendaraanController;
use App\Http\Controllers\MdriverController;
use App\Http\Controllers\PKendaraanController;
use App\Http\Controllers\MessController;
use App\Http\Controllers\BookingKamarController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\DashboardDriverController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/review/{token}', [ReviewController::class, 'show'])->name('review.show');
Route::post('/review/{token}', [ReviewController::class, 'store'])->name('review.store');
Route::get('/review/success', function () {
    return view('reviews.success');
})->name('review.success');

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('refresh-captcha', [SendViconController::class, 'refreshCaptcha'])->name('refresh.captcha');

Route::post('admin/login', [UserController::class, 'login'])->name('admin.login');

Route::redirect('admin', 'admin/dashboard');

Route::group(['prefix' => 'vicon', 'as' => 'vicon.'], function () {
    Route::get('data', [SendViconController::class, 'data'])->name('data');
    Route::get('{id}/detail', [SendViconController::class, 'showGuest'])->name('detail');
});

Route::get('absensi/form/{token}/{id}', [AbsensiController::class, 'create'])->name('absensi.create');
Route::post('absensi/form', [AbsensiController::class, 'store'])->name('absensi.store');

Route::post('sendvicon/store', [SendViconController::class, 'store'])->name('sendvicon.store');
Route::post('sendvicon/cancel', [SendViconController::class, 'cancel'])->name('sendvicon.cancel');
Route::get('sendvicon/check-nama', [SendViconController::class, 'ceknama'])->name('sendvicon.ceknama');

Route::group(['prefix' => 'konsumsi', 'as' => 'konsumsi.'], function () {
    Route::get('/', [KonsumsiController::class, 'index'])->name('index');
    Route::put('/update/{id}', [KonsumsiController::class, 'update'])->name('update');
    Route::post('/approve/{id}', [KonsumsiController::class, 'approve'])->name('approve');
    Route::delete('/destroy/{id}', [KonsumsiController::class, 'destroy'])->name('destroy');
    Route::post('/export-excel', [KonsumsiController::class, 'exportExcel'])->name('exportExcel');
    Route::post('/kirim/{id}', [KonsumsiController::class, 'kirim'])->name('kirim');
    Route::get('/data', [KonsumsiController::class, 'data'])->name('data');
});

Route::group(['prefix' => 'makansiang', 'as' => 'makansiang.'], function () {
    Route::get('/', [MakanSiangController::class, 'index'])->name('index');
    Route::post('/store', [MakanSiangController::class, 'store'])->name('store');
    Route::get('/edit/{id}', [MakanSiangController::class, 'edit'])->name('edit');  // Add this route
    Route::put('/update/{id}', [MakanSiangController::class, 'update'])->name('update');
    // Route::post('/approve/{id}', [MakanSiangController::class, 'approve'])->name('approve');
    Route::delete('/destroy/{id}', [MakanSiangController::class, 'destroy'])->name('destroy');
    Route::get('/export', [MakanSiangController::class, 'export'])->name('export');
    // Route::post('/kirim/{id}', [MakanSiangController::class, 'kirim'])->name('kirim');
    // Route::get('/data', [MakanSiangController::class, 'data'])->name('data');
    Route::post('/approve/{id}', [MakanSiangController::class, 'approve'])->name('approve');
    Route::post('/reject/{id}', [MakanSiangController::class, 'reject'])->name('reject');

});

Route::group(['prefix' => 'masterkendaraan', 'as' => 'masterkendaraan.'], function () {
    Route::get('/', [MKendaraanController::class, 'index'])->name('index');
    Route::post('/store', [MKendaraanController::class, 'store'])->name('store');
    Route::get('/edit/{id}', [MKendaraanController::class, 'edit'])->name('edit');  // Add this route
    Route::put('/update/{id}', [MKendaraanController::class, 'update'])->name('update');
    Route::delete('/destroy/{id}', [MKendaraanController::class, 'destroy'])->name('destroy');
    Route::get('/export', [MKendaraanController::class, 'export'])->name('export');
});

Route::group(['prefix' => 'masterdriver', 'as' => 'masterdriver.'], function () {
    Route::get('/', [MdriverController::class, 'index'])->name('index');
    Route::post('/store', [MdriverController::class, 'store'])->name('store');
    Route::get('/edit/{id}', [MdriverController::class, 'edit'])->name('edit');  // Add this route
    Route::put('/update/{id}', [MdriverController::class, 'update'])->name('update');
    Route::delete('/destroy/{id}', [MdriverController::class, 'destroy'])->name('destroy');
    Route::get('/export', [MdriverController::class, 'export'])->name('export');
});

Route::group(['prefix' => 'mess', 'as' => 'mess.'], function () {
    Route::get('/', [MessController::class, 'index'])->name('index');
    Route::post('/store', [MessController::class, 'store'])->name('store');
    Route::get('/edit/{id}', [MessController::class, 'edit'])->name('mess.edit');
    Route::put('/update/{id}', [MessController::class, 'update'])->name('mess.update');

    // Route::get('/edit/{id}', [MdriverController::class, 'edit'])->name('edit');  // Add this route
    // Route::put('/update/{id}', [MdriverController::class, 'update'])->name('update');
    Route::delete('/destroy/{id}', [MessController::class, 'destroy'])->name('destroy');
    Route::delete('/aktif/{id}', [MessController::class, 'aktif'])->name('aktif');
    Route::get('/export', [MdriverController::class, 'export'])->name('export');
    Route::delete('/destroy-photo-mess/{id}', [MessController::class, 'destroyphotomess'])->name('destroyphotomess');
});

Route::group(['prefix' => 'kamar', 'as' => 'kamar.'], function () {
    Route::get('/', [KamarController::class, 'index'])->name('index');
    Route::post('/store', [KamarController::class, 'store'])->name('store');
    Route::get('/edit/{id}', [KamarController::class, 'edit'])->name('kamar.edit');
    Route::put('/update/{id}', [KamarController::class, 'update'])->name('kamar.update');
    Route::get('/{id}/reviews', [KamarController::class, 'getReviews'])->name('review');
    Route::delete('/destroy-photo-kamar/{id}', [KamarController::class, 'destroyphotokamar'])->name('destroyphotokamar');


    // Route::get('/edit/{id}', [MdriverController::class, 'edit'])->name('edit');  // Add this route
    // Route::put('/update/{id}', [MdriverController::class, 'update'])->name('update');
    Route::delete('/destroy/{id}', [KamarController::class, 'destroy'])->name('destroy');
    Route::delete('/aktif/{id}', [KamarController::class, 'aktif'])->name('aktif');
    Route::get('/export', [MdriverController::class, 'export'])->name('export');
});

Route::group(['prefix' => 'bookingkamar', 'as' => 'bookingkamar.'], function () {
    Route::get('/', [BookingKamarController::class, 'index'])->name('index');
    // Route::get('/booking', [BookingKamarController::class, 'index'])->name('booking.index');
    Route::post('/store', [BookingKamarController::class, 'store'])->name('store');
    Route::post('/booking/{id}/checkout', [BookingKamarController::class, 'checkout'])->name('checkout');
    Route::get('/list_booking', [BookingKamarController::class, 'list_booking'])->name('list_booking');
    Route::patch('/booking/approve/{id}', [BookingKamarController::class, 'approve'])->name('approve');
    Route::patch('/booking/reject/{id}', [BookingKamarController::class, 'reject'])->name('reject');
    Route::patch('/booking/cancel/{id}', [BookingKamarController::class, 'cancel'])->name('cancel');
    Route::patch('/booking/perpanjangan/{id}', [BookingKamarController::class, 'perpanjangan'])->name('perpanjangan');
    // Route::patch('/booking/perpanjangan', [BookingKamarController::class, 'perpanjangan'])->name('perpanjangan');
    Route::patch('/booking/checkout/{id}', [BookingKamarController::class, 'checkout'])->name('checkout');
    Route::get('/export', [BookingKamarController::class, 'export'])->name('export');
    // Route::get('/list-booking', [BookingKamarController::class, 'list_booking'])->name('bookingkamar.list');
});


Route::group(['prefix' => 'pkendaraan', 'as' => 'pkendaraan.'], function () {
    Route::get('/', [PKendaraanController::class, 'index'])->name('index');
    Route::post('/store', [PKendaraanController::class, 'store'])->name('store');
    Route::get('/edit/{id}', [PKendaraanController::class, 'edit'])->name('edit');  // Add this route
    Route::put('/update/{id}', [PKendaraanController::class, 'update'])->name('update');
    Route::delete('/destroy/{id}', [PKendaraanController::class, 'destroy'])->name('destroy');
    Route::get('/export', [PKendaraanController::class, 'export'])->name('export');
    Route::post('/approve/{id}', [PKendaraanController::class, 'approve'])->name('approve');
    Route::post('/reject/{id}', [PKendaraanController::class, 'reject'])->name('reject');
    Route::get('/get-available-drivers', [PKendaraanController::class, 'getAvailableDrivers'])
        ->name('getAvailableDrivers');
    Route::get('/get-available-drivers-admin', [PKendaraanController::class, 'getAvailableDriversAdmin'])
        ->name('getAvailableDrivers');
});

Route::group(['prefix' => 'kartu', 'as' => 'kartu.'], function () {
    Route::get('/', [KartuController::class, 'index'])->name('index');
    Route::post('/store', [KartuController::class, 'store'])->name('store');
    Route::get('/edit/{id}', [KartuController::class, 'edit'])->name('edit');  // Add this route
    Route::put('/update/{id}', [KartuController::class, 'update'])->name('update');
    // Route::post('/approve/{id}', [MakanSiangController::class, 'approve'])->name('approve');
    Route::delete('/destroy/{id}', [KartuController::class, 'destroy'])->name('destroy');
    Route::get('/export', [KartuController::class, 'export'])->name('export');
    // Route::post('/kirim/{id}', [MakanSiangController::class, 'kirim'])->name('kirim');
    // Route::get('/data', [MakanSiangController::class, 'data'])->name('data');
    Route::post('/approve/{id}', [KartuController::class, 'approve'])->name('approve');
    Route::post('/reject/{id}', [KartuController::class, 'reject'])->name('reject');

});

Route::group(['prefix' => 'kaskecil', 'as' => 'kaskecil.', 'middleware' => 'role:admin,GA,read'], function () {
    Route::get('/', [KasKecilController::class, 'index'])->name('index');
    Route::get('/data', [KasKecilController::class, 'data'])->name('data');
    Route::post('/store', [KasKecilController::class, 'store'])->name('store');
    Route::get('/edit/{id}', [KasKecilController::class, 'edit'])->name('edit');  // Add this route
    Route::post('/update/{id}', [KasKecilController::class, 'update'])->name('update');  // Add this route
    Route::delete('/{id}', [KasKecilController::class, 'destroy'])->name('destroy');  // Update this line
    Route::get('/export', [KasKecilController::class, 'export'])->name('export');


    // Route untuk mengakses bukti_kaskecil
    Route::get('/bukti/{filename}', function ($filename) {
        // Cek di folder bukti_nota
        $filePath = storage_path('app/public/bukti_nota/' . $filename);

        // Jika ditemukan file, kembalikan sebagai response
        if (file_exists($filePath)) {
            return response()->file($filePath);
        }

        // Jika tidak ditemukan di bukti_nota, cek di bukti_bayar
        $filePath = storage_path('app/public/bukti_bayar/' . $filename);

        if (file_exists($filePath)) {
            return response()->file($filePath);
        }

        // Jika tidak ditemukan, tampilkan error 404
        abort(404);
    })->name('bukti');
});

Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    route::get('logout', [UserController::class, 'logout'])->name('logout');

    Route::group(['prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
        Route::group(['middleware' => 'role:admin,GA,divisi,sekper,read'], function () {
            Route::get('/', [DashboardController::class, 'index'])->name('index');
            Route::get('/ajax', [DashboardController::class, 'getAjaxDashboard'])->name('ajax');
            Route::get('{id}/show', [DashboardController::class, 'show'])->name('show');
            Route::post('export/pdf/vicon', [DashboardController::class, 'exportPdfVicon'])->name('export.pdf.vicon');
            Route::post('export/excel/vicon', [DashboardController::class, 'exportExcelVicon'])->name('export.excel.vicon');
        });

        Route::group(['prefix' => 'kendaraan', 'as' => 'kendaraan.', 'middleware' => 'role:admin,Driver'], function () {
            Route::get('/', [AgendaKendaraanController::class, 'index'])->name('show');
            Route::post('/ajax', [AgendaKendaraanController::class, 'get_ajax'])->name('ajax');
            Route::post('/save', [AgendaKendaraanController::class, 'addagendakendaraan'])->name('insert');
            Route::post('/update', [AgendaKendaraanController::class, 'updateagendakendaraan'])->name('update');
            Route::get('/delete/{id}', [AgendaKendaraanController::class, 'hapusagendakendaraan'])->name('delete');
            Route::get('/cancel', [AgendaKendaraanController::class, 'cancelkendaraan'])->name('cancel');
            Route::get('/reset-filter', [AgendaKendaraanController::class, 'reset_filter'])->name('resetFilter');
            Route::post('/export-excel', [AgendaKendaraanController::class, 'export_agendakendaraan_excel'])->name('exportExcel');
            Route::post('/export-pdf', [AgendaKendaraanController::class, 'export_agendakendaraan'])->name('exportPdf');
        });

        Route::group(['prefix' => 'master-data', 'as' => 'master.', 'middleware' => 'role:admin'], function () {
            Route::group(['prefix' => 'jenis-rapat', 'as' => 'jenis.'], function () {
                Route::get('/', [MasterJenisRapatController::class, 'index'])->name('index');
                Route::post('/tambah', [MasterJenisRapatController::class, 'save'])->name('save');
                Route::post('/update/{id}', [MasterJenisRapatController::class, 'update'])->name('update');
            });

            Route::group(['prefix' => 'kendaraan', 'as' => 'kendaraan.'], function () {
                Route::get('/', [MasterKendaraanController::class, 'index'])->name('index');
                Route::post('/save', [MasterKendaraanController::class, 'save'])->name('save');
                Route::post('/update/{id}', [MasterKendaraanController::class, 'update'])->name('update');
            });

            Route::group(['prefix' => 'pengguna', 'as' => 'pengguna.'], function () {
                Route::get('/', [MasterPenggunaController::class, 'index'])->name('index');
                Route::get('/tambah-pengguna', [MasterPenggunaController::class, 'formCreatePengguna'])->name('formCreate');
                Route::post('/save', [MasterPenggunaController::class, 'createPengguna'])->name('save');
                Route::get('/edit-pengguna/{id}', [MasterPenggunaController::class, 'formEditPengguna'])->name('formUpdate');
                Route::post('/update/{id}', [MasterPenggunaController::class, 'updatePengguna'])->name('update');
                Route::get('/reset-password/{id}', [MasterPenggunaController::class, 'halaman_resetpassword'])->name('formResetPassword');
                Route::post('/reset-password/{id}', [MasterPenggunaController::class, 'resetpassword'])->name('resetPassword');
                Route::post('/hapus', [MasterPenggunaController::class, 'hapuspengguna'])->name('hapus');
            });
        });
    });

    Route::group(['prefix' => 'vicon', 'as' => 'vicon.', 'middleware' => 'role:admin,GA,divisi,sekper,read'], function () {
        Route::get('/', [SendViconController::class, 'index'])->name('index');
        Route::POST('/data', [SendViconController::class, 'getData'])->name('data');
        Route::get('{id}/show', [SendViconController::class, 'show'])->name('show');
        Route::post('update', [SendViconController::class, 'update'])->name('update');
        Route::post('{id}/destroy', [SendViconController::class, 'destroy'])->name('destroy');
        Route::get('reset-filter', [SendViconController::class, 'resetFilter'])->name('resetfilter');
        Route::post('store', [SendViconController::class, 'storeAdmin'])->name('store');
        Route::post('cancel', [SendViconController::class, 'cancelAdmin'])->name('cancel');
        Route::post('check-nama', [SendViconController::class, 'ceknamaAdmin'])->name('checknama');
        Route::get('export-excel', [SendViconController::class, 'exportExcel'])->name('excel');
        Route::get('export-pdf', [SendViconController::class, 'exportPdf'])->name('pdf');
        Route::post('approve', [SendViconController::class, 'approveSendvicond'])->name('approve');
    });

    Route::group(['prefix' => 'absensi', 'as' => 'absensi.'], function () {
        Route::get('rekap/{id}', [AbsensiController::class, 'rekap'])->name('rekap');
        Route::get('{id}/show', [AbsensiController::class, 'show'])->name('show');
        Route::post('edit/{id}/status', [AbsensiController::class, 'status'])->name('edit.status');
        Route::get('export/{id}', [AbsensiController::class, 'exportExcel'])->name('export');
    });

    // Route Notifikasi
    Route::prefix('notification')->as('notification.')->middleware('role:admin,GA,divisi,sekper,read')->group(function () {
        Route::get('driver/{id}', [NotificationController::class, 'listNotifikasiDriver'])->name('driver');
        Route::get('driver/read/{id}', [NotificationController::class, 'listNotifikasiDriverRead'])->name('driver.read');
        Route::get('vicon/{id}', [NotificationController::class, 'listNotifikasiVicon'])->name('vicon');
        Route::get('vicon/{id}/read', [NotificationController::class, 'listNotifikasiViconRead'])->name('vicon.read');
        Route::get('petugas/{id}', function () {
            return "";
        })->name('petugas');
    });

    Route::group(['middleware' => 'role:admin'], function () {
        // Route untuk Link
        Route::group(['prefix' => 'masterlink', 'as' => 'masterlink.'], function () {
            Route::get('/', [LinkController::class, 'index'])->name('index');
            Route::post('/tambah', [LinkController::class, 'store'])->name('store');
            Route::put('/update/{id}', [LinkController::class, 'update'])->name('update');
        });
        // Route untuk Ruangan
        Route::group(['prefix' => 'ruangan', 'as' => 'ruangan.'], function () {
            Route::get('/', [RuanganController::class, 'index'])->name('index');
            Route::post('/tambah', [RuanganController::class, 'store'])->name('store');
            Route::put('/update/{id}', [RuanganController::class, 'update'])->name('update');
        });
        // Route untuk Bagian
        Route::group(['prefix' => 'bagian', 'as' => 'bagian.'], function () {
            Route::get('/', [BagianController::class, 'index'])->name('index');
            Route::post('/tambah', [BagianController::class, 'store'])->name('store');
            Route::put('/update/{id}', [BagianController::class, 'update'])->name('update');
        });
        // Route untuk Hak Akses
        Route::group(['prefix' => 'hak_akses', 'as' => 'hak_akses.'], function () {
            Route::get('/', [HakAksesController::class, 'index'])->name('index');
            Route::post('/tambah', [HakAksesController::class, 'store'])->name('store');
            Route::put('/update/{id}', [HakAksesController::class, 'update'])->name('update');
        });
    });

    Route::group(['prefix' => 'agenda', 'as' => 'agenda.', 'middleware' => 'role:admin,GA,divisi,sekper,read'], function () {
        Route::get('/', [DashboardAgendaController::class, 'index'])->name('index');
        Route::post('/get-content', [DashboardAgendaController::class, 'getContent'])->name('content');
        Route::post('/export-pdf', [DashboardAgendaController::class, 'export_pdf'])->name('exportPdf');
    });

    Route::group(['prefix' => 'driver', 'as' => 'driver.', 'middleware' => 'role:admin,GA,divisi,sekper,read'], function () {
        Route::get('/', [DashboardDriverController::class, 'index'])->name('index');
        Route::get('/get-driver-schedule', [DashboardDriverController::class, 'getDriverSchedule']);

        Route::post('/get-content', [DashboardDriverController::class, 'getContent'])->name('content');
        Route::post('/export-pdf', [DashboardDriverController::class, 'export_pdf'])->name('exportPdf');
    });
});
