<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
  |--------------------------------------------------------------------------
  | Web Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register web routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | contains the "web" middleware group. Now create something great!
  |
 */

//Route::get('/', function () {
//    return view('welcome');
//});

Auth::routes();
Route::get('/clear-cache', function() {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('config:cache');
    return 'DONE'; //Return anything
});

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::post('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('resend-email-activation', [App\Http\Controllers\Auth\ResendEmailActivationController::class, 'create'])->name('resend-email-activation');
Route::post('resend-email-activation', [App\Http\Controllers\Auth\ResendEmailActivationController::class, 'store'])->name('resend-email-activation');

Route::get('testEvent', [App\Http\Controllers\MigrationController::class, 'index'])->name('test-event');

// anggota
Route::group(['prefix' => 'anggota'], function () {
    Route::group(['middleware' => ['auth', 'check']], function () {
        Route::get('list', [App\Http\Controllers\AnggotaController::class, 'index'])->name('anggota-list');
        Route::post('list', [App\Http\Controllers\AnggotaController::class, 'index'])->name('anggota-list');
        Route::get('list/data', [App\Http\Controllers\AnggotaController::class, 'indexAjax'])->name('anggota-list-ajax');
        Route::get('create', [App\Http\Controllers\AnggotaController::class, 'create'])->name('anggota-create');
        Route::post('create', [App\Http\Controllers\AnggotaController::class, 'store'])->name('anggota-create');
        Route::get('edit/{id}', [App\Http\Controllers\AnggotaController::class, 'edit'])->where('id', '[0-9]+')->name('anggota-edit');
        Route::post('update/{id}', [App\Http\Controllers\AnggotaController::class, 'update'])->where('id', '[0-9]+')->name('anggota-edit');
        Route::delete('delete/{id}', [App\Http\Controllers\AnggotaController::class, 'delete'])->where('id', '[0-9]+')->name('anggota-delete');
        Route::get('ajax-detail/{id}', [App\Http\Controllers\AnggotaController::class, 'ajaxDetail'])->name('anggota-ajax-detail');
        Route::get('ajax/search', [App\Http\Controllers\AnggotaController::class, 'search'])->name('anggota-ajax-search');
        Route::get('ajax/search/{id}', [App\Http\Controllers\AnggotaController::class, 'searchId'])->where('id', '[0-9]+')->name('anggota-ajax-searchid');
        Route::get('ajax/detailAnggota', [App\Http\Controllers\AnggotaController::class, 'getDetail'])->name('anggota-ajax-getDetail');
        Route::get('ajax/getKelasCompany', [App\Http\Controllers\AnggotaController::class, 'getKelasCompany'])->name('anggota-ajax-getKelasCompany');

        Route::get('download/pdf', [App\Http\Controllers\AnggotaController::class, 'createPDF'])->name('anggota-download-pdf');
        Route::get('download/excel', [App\Http\Controllers\AnggotaController::class, 'createExcel'])->name('anggota-download-excel');

        Route::get('import/excel', [App\Http\Controllers\AnggotaController::class, 'importExcel'])->name('anggota-import-excel');
        Route::post('import/excel', [App\Http\Controllers\AnggotaController::class, 'storeImportExcel'])->name('anggota-import-excel');
    });
});

// setting
Route::group(['prefix' => 'setting'], function () {
    //simpanan
    Route::get('/simpanan', [App\Http\Controllers\SettingSimpananController::class, 'index'])->name('jenis-simpanan-list');
    Route::get('/simpanan/edit/{id}', [App\Http\Controllers\SettingSimpananController::class, 'edit'])->name('jenis-simpanan-edit');
    Route::post('/simpanan/edit/{id}', [App\Http\Controllers\SettingSimpananController::class, 'update'])->name('jenis-simpanan-edit');
    Route::get('/simpanan/destroy/{id}', [App\Http\Controllers\SettingSimpananController::class, 'destroy'])->name('jenis-simpanan-delete');
    Route::get('/simpanan/create', [App\Http\Controllers\SettingSimpananController::class, 'create'])->name('jenis-simpanan-add');
    Route::post('/simpanan/create', [App\Http\Controllers\SettingSimpananController::class, 'store'])->name('jenis-simpanan-add');
    Route::get('/simpanan/jenis/search', [App\Http\Controllers\JenisSimpananController::class, 'search'])->name('jenis-simpanan-search');
    Route::get('/simpanan/jenis/search/{id}', [App\Http\Controllers\JenisSimpananController::class, 'searchId'])->name('jenis-simpanan-searchId');
    Route::get('/simpanan/jenis/searchByUser', [App\Http\Controllers\JenisSimpananController::class, 'searchSimpananByUser'])->name('jenis-simpanan-searchByUser');
    Route::get('/simpanan/import/excel', [App\Http\Controllers\SettingSimpananController::class, 'importExcel'])->name('saldosimpanan-import-excel');
    Route::post('/simpanan/import/excel', [App\Http\Controllers\SettingSimpananController::class, 'storeImportExcel'])->name('saldosimpanan-import-excel');

    //pinjaman
    Route::get('/pinjaman', [App\Http\Controllers\SettingPinjamanController::class, 'index'])->name('jenis-pinjaman-list');
    Route::get('/pinjaman/edit/{id}', [App\Http\Controllers\SettingPinjamanController::class, 'edit'])->name('jenis-pinjaman-edit');
    Route::post('/pinjaman/edit/{id}', [App\Http\Controllers\SettingPinjamanController::class, 'update'])->name('jenis-pinjaman-edit');
    Route::get('/pinjaman/destroy/{id}', [App\Http\Controllers\SettingPinjamanController::class, 'destroy'])->name('jenis-pinjaman-delete');
    Route::get('/pinjaman/create', [App\Http\Controllers\SettingPinjamanController::class, 'create'])->name('jenis-pinjaman-add');
    Route::post('/pinjaman/create', [App\Http\Controllers\SettingPinjamanController::class, 'store'])->name('jenis-pinjaman-add');
    Route::get('/pinjaman/import/excel', [App\Http\Controllers\SettingPinjamanController::class, 'importExcel'])->name('saldopinjaman-import-excel');
    Route::post('/pinjaman/import/excel', [App\Http\Controllers\SettingPinjamanController::class, 'storeImportExcel'])->name('saldopinjaman-import-excel');

    //codetrans
    Route::get('/codetrans', [App\Http\Controllers\SettingCodeTransController::class, 'index'])->name('kode-transaksi-list');
    Route::get('/codetrans/edit/{id}', [App\Http\Controllers\SettingCodeTransController::class, 'edit'])->name('kode-transaksi-edit');
    Route::post('/codetrans/update/{id}', [App\Http\Controllers\SettingCodeTransController::class, 'update'])->name('kode-transaksi-update');
    Route::get('/codetrans/destroy/{id}', [App\Http\Controllers\SettingCodeTransController::class, 'destroy']);
    Route::get('/codetrans/create', [App\Http\Controllers\SettingCodeTransController::class, 'create'])->name('kode-transaksi-create');
    Route::post('/codetrans/store', [App\Http\Controllers\SettingCodeTransController::class, 'store'])->name('kode-transaksi-store');

    // Jenis Anggota
    Route::group(['prefix' => 'jenis-anggota'], function () {
        Route::group(['middleware' => ['auth', 'check']], function () {
            Route::get('list', [App\Http\Controllers\JenisAnggotaController::class, 'index'])->name('jenis-anggota-list');
            Route::get('create', [App\Http\Controllers\JenisAnggotaController::class, 'create'])->name('jenis-anggota-create');
            Route::post('create', [App\Http\Controllers\JenisAnggotaController::class, 'store'])->name('jenis-anggota-create');
            Route::get('edit/{id}', [App\Http\Controllers\JenisAnggotaController::class, 'edit'])->where('id', '[0-9]+')->name('jenis-anggota-edit');
            Route::post('edit/{id}', [App\Http\Controllers\JenisAnggotaController::class, 'update'])->where('id', '[0-9]+')->name('jenis-anggota-edit');
            Route::delete('delete/{id}', [App\Http\Controllers\JenisAnggotaController::class, 'delete'])->where('id', '[0-9]+')->name('jenis-anggota-delete');
        });
    });

    // Status Pengajuan
    Route::get('/status-pengajuan', [App\Http\Controllers\SettingStatusPengajuanController::class, 'index'])->name('status-pengajuan-list');
    Route::get('/status-pengajuan/edit/{id}', [App\Http\Controllers\SettingStatusPengajuanController::class, 'edit'])->where('id', '[0-9]+')->name('status-pengajuan-edit');
    Route::post('/status-pengajuan/edit/{id}', [App\Http\Controllers\SettingStatusPengajuanController::class, 'update'])->where('id', '[0-9]+')->name('status-pengajuan-edit');
    // Route::get('/status-pengajuan/destroy/{id}', [App\Http\Controllers\SettingStatusPengajuanController::class, 'destroy'])->name('status-pengajuan-delete');
    // Route::get('/status-pengajuan/create', [App\Http\Controllers\SettingStatusPengajuanController::class, 'create'])->name('status-pengajuan-add');
    // Route::post('/status-pengajuan/create', [App\Http\Controllers\SettingStatusPengajuanController::class, 'store'])->name('status-pengajuan-add');
});

// user
Route::group(['prefix' => 'user'], function () {
    Route::get('validation/{validation_id}', [App\Http\Controllers\UserController::class, 'validation'])->name('user-validation');
    Route::group(['middleware' => ['auth', 'check']], function () {
        Route::get('list', [App\Http\Controllers\UserController::class, 'index'])->name('user-list');
        Route::post('list', [App\Http\Controllers\UserController::class, 'index'])->name('user-list');
        Route::get('list/data', [App\Http\Controllers\UserController::class, 'indexAjax'])->name('user-list-ajax');
        Route::get('create', [App\Http\Controllers\UserController::class, 'create'])->name('user-create');
        Route::post('create', [App\Http\Controllers\UserController::class, 'store'])->name('user-create');
        Route::get('/edit/{id}', [App\Http\Controllers\UserController::class, 'edit'])->where('id', '[0-9]+')->name('user-edit');
        Route::post('/edit/{id}', [App\Http\Controllers\UserController::class, 'update'])->where('id', '[0-9]+')->name('user-edit');
        Route::delete('delete/{id}', [App\Http\Controllers\UserController::class, 'delete'])->where('id', '[0-9]+')->name('user-delete');

        Route::get('/edit/permission/{id}', [App\Http\Controllers\UserController::class, 'editPermission'])->where('id', '[0-9]+')->name('user-edit-permission');
        Route::post('/edit/permission/{id}', [App\Http\Controllers\UserController::class, 'updatePermission'])->where('id', '[0-9]+')->name('user-edit-permission');

        Route::get('profile', [App\Http\Controllers\UserController::class, 'profile'])->name('user-profile');
        Route::post('profile', [App\Http\Controllers\UserController::class, 'updateProfile'])->name('user-profile');

        Route::get('change-password', [App\Http\Controllers\UserController::class, 'changePassword'])->name('user-change-password');
        Route::post('change-password', [App\Http\Controllers\UserController::class, 'updatePassword'])->name('user-change-password');

        Route::get('import/excel', [App\Http\Controllers\UserController::class, 'importExcel'])->name('user-import-excel');
        Route::post('import/excel', [App\Http\Controllers\UserController::class, 'storeImportExcel'])->name('user-import-excel');
        Route::get('download/excel', [App\Http\Controllers\UserController::class, 'createExcel'])->name('user-download-excel');
    });
});

// role
Route::group(['prefix' => 'role'], function () {
    Route::group(['middleware' => ['auth', 'check']], function () {
        Route::get('list', [App\Http\Controllers\RoleController::class, 'index'])->name('role-list');
        Route::get('create', [App\Http\Controllers\RoleController::class, 'create'])->name('role-create');
        Route::post('create', [App\Http\Controllers\RoleController::class, 'store'])->name('role-create');
        Route::get('/edit/{id}', [App\Http\Controllers\RoleController::class, 'edit'])->where('id', '[0-9]+')->name('role-edit');
        Route::post('/edit/{id}', [App\Http\Controllers\RoleController::class, 'update'])->where('id', '[0-9]+')->name('role-edit');
        Route::delete('/delete/{id}', [App\Http\Controllers\RoleController::class, 'delete'])->where('id', '[0-9]+')->name('role-delete');
    });
});

// transaksi
Route::group(['prefix' => 'transaksi'], function () {
    Route::group(['middleware' => ['auth', 'check']], function () {
        Route::get('', [App\Http\Controllers\TransaksiController::class, 'listTransaksiAnggota'])->name('transaksi-list-anggota');
        Route::post('', [App\Http\Controllers\TransaksiController::class, 'listTransaksiAnggota'])->name('transaksi-list-anggota');
        Route::get('download/pdf', [App\Http\Controllers\TransaksiController::class, 'createPDF'])->name('transaksi-download-pdf');
        Route::get('download/excel', [App\Http\Controllers\TransaksiController::class, 'createExcel'])->name('transaksi-download-excel');
    });
});

// pinjaman
Route::group(['prefix' => 'pinjaman'], function () {
    Route::group(['middleware' => ['auth', 'check']], function () {
        Route::get('list', [App\Http\Controllers\PinjamanController::class, 'index'])->name('pinjaman-list');
        Route::get('list/{id}', [App\Http\Controllers\PinjamanController::class, 'index'])->where('id', '[0-9]+')->name('pinjaman-list');
        Route::post('list', [App\Http\Controllers\PinjamanController::class, 'index'])->name('pinjaman-list');
        Route::get('history', [App\Http\Controllers\PinjamanController::class, 'history'])->name('pinjaman-history');
        Route::post('history', [App\Http\Controllers\PinjamanController::class, 'history'])->name('pinjaman-history');
        Route::get('create', [App\Http\Controllers\PinjamanController::class, 'create'])->name('pinjaman-create');
        Route::post('create', [App\Http\Controllers\PinjamanController::class, 'store'])->name('pinjaman-create');
        Route::get('detail/{id}', [App\Http\Controllers\PinjamanController::class, 'show'])->name('pinjaman-detail');
        Route::get('detail-pembayaran/{id}', [App\Http\Controllers\PinjamanController::class, 'detailPembayaran'])->name('pinjaman-detail-pembayaran');
        Route::get('download/pdf', [App\Http\Controllers\PinjamanController::class, 'createPDF'])->name('pinjaman-download-pdf');
        Route::get('download/excel', [App\Http\Controllers\PinjamanController::class, 'createExcel'])->name('pinjaman-download-excel');
        Route::get('download-form-pinjaman', [App\Http\Controllers\PinjamanController::class, 'downloadFormPinjaman'])->name('download-form-pinjaman')->middleware(['pinjaman']);
        Route::post('download-form-pinjaman', [App\Http\Controllers\PinjamanController::class, 'simulasiPinjaman'])->name('download-form-pinjaman')->middleware(['pinjaman']);
        Route::get('generate-form-pinjaman', [App\Http\Controllers\PinjamanController::class, 'generateFormPinjaman'])->name('generate-form-pinjaman')->middleware(['pinjaman']);
        Route::post('bayar-angsuran/{id}', [App\Http\Controllers\PinjamanController::class, 'bayarAngsuran'])->name('pinjaman-bayar-angsuran');
        Route::post('bayar-angsuran/{id}/dipercepat', [App\Http\Controllers\PinjamanController::class, 'bayarAngsuranDipercepat'])->name('pinjaman-bayar-angsuran-dipercepat');

        // import batch saldo pinjaman
        Route::get('import', [App\Http\Controllers\PinjamanController::class, 'importPinjaman'])->name('pinjaman-import');
        Route::post('import', [App\Http\Controllers\PinjamanController::class, 'storeImportPinjaman'])->name('pinjaman-import');
        Route::delete('delete/{id}', [App\Http\Controllers\PinjamanController::class, 'destroy'])->name('pinjaman-delete');

        Route::group(['prefix' => 'pengajuan'], function () {
            Route::get('list', [App\Http\Controllers\PinjamanController::class, 'indexPengajuan'])->name('pengajuan-pinjaman-list');
            Route::get('data-jurnal/{kodepengajuan}', [App\Http\Controllers\PinjamanController::class, 'viewDataJurnalPinjaman'])->name('view-data-jurnal-pengajuan-pinjaman');
            Route::get('print-jkk', [App\Http\Controllers\PengajuanController::class, 'indexJkk'])->name('pengajuan-pinjaman-print-jkk');
            Route::post('print-jkk', [App\Http\Controllers\PengajuanController::class, 'printJkk'])->name('pengajuan-pinjaman-print-jkk');
            Route::get('create', [App\Http\Controllers\PinjamanController::class, 'createPengajuanPinjaman'])->name('pengajuan-pinjaman-add')->middleware(['pinjaman']);
            Route::get('maxPinjaman', [App\Http\Controllers\PinjamanController::class, 'calculateMaxPinjaman'])->name('pengajuan-pinjaman-calculate-max-pinjaman');
            Route::post('create', [App\Http\Controllers\PinjamanController::class, 'storePengajuanPinjaman'])->name('pengajuan-pinjaman-add');
            Route::get('calculate-angsuran', [App\Http\Controllers\PinjamanController::class, 'calculateAngsuran'])->name('pengajuan-pinjaman-calculate-angsuran');
            Route::post('update-status', [App\Http\Controllers\PinjamanController::class, 'updateStatusPengajuanPinjaman'])->name('pengajuan-pinjaman-update-status');
        });
    });
});


//simpanan
Route::group(['prefix' => 'simpanan'], function () {
    Route::group(['middleware' => ['auth', 'check']], function () {
        Route::get('list', [App\Http\Controllers\SimpananController::class, 'index'])->name('simpanan-list');
        Route::post('list', [App\Http\Controllers\SimpananController::class, 'index'])->name('simpanan-list');
        Route::get('list/data', [App\Http\Controllers\SimpananController::class, 'indexAjax'])->name('simpanan-list-ajax');
        Route::get('create', [App\Http\Controllers\SimpananController::class, 'create'])->name('simpanan-add');
        Route::post('create', [App\Http\Controllers\SimpananController::class, 'store'])->name('simpanan-add');
        Route::get('history', [App\Http\Controllers\SimpananController::class, 'history'])->name('simpanan-history');
        Route::get('history/data', [App\Http\Controllers\SimpananController::class, 'historyData'])->name('simpanan-history-data');
        Route::post('history', [App\Http\Controllers\SimpananController::class, 'history'])->name('simpanan-history');
        Route::get('detail/{id}', [App\Http\Controllers\SimpananController::class, 'show'])->where('id', '[0-9]+')->name('simpanan-detail');
        Route::get('download/pdf', [App\Http\Controllers\SimpananController::class, 'createPDF'])->name('simpanan-download-pdf');
        Route::get('download/excel', [App\Http\Controllers\SimpananController::class, 'createExcel'])->name('simpanan-download-excel');
        Route::get('import/excel', [App\Http\Controllers\SimpananController::class, 'importExcel'])->name('simpanan-import-excel');
        Route::post('import/excel', [App\Http\Controllers\SimpananController::class, 'storeImportExcel'])->name('simpanan-import-excel');
        Route::get('ajax/payment-value', [App\Http\Controllers\SimpananController::class, 'paymentValue'])->name('ajax-simpanan-payment-value');

        Route::group(['prefix' => 'card'], function () {
            Route::get('', [App\Http\Controllers\SimpananController::class, 'indexCard'])->name('simpanan-index-card');
            Route::get('view/{kodeAnggota}', [App\Http\Controllers\SimpananController::class, 'showCard'])->name('simpanan-show-card');
            Route::get('download/pdf/{kodeAnggota}', [App\Http\Controllers\SimpananController::class, 'downloadPDFCard'])->name('simpanan-download-pdf-card');
            Route::get('download/excel/{kodeAnggota}', [App\Http\Controllers\SimpananController::class, 'downloadExcelCard'])->name('simpanan-download-pdf-card');
        });
    });
});

// penarikan
Route::group(['prefix' => 'penarikan'], function () {
    Route::group(['middleware' => ['auth', 'check']], function () {
        Route::get('list', [App\Http\Controllers\PenarikanController::class, 'index'])->name('penarikan-index');

        Route::get('create', [App\Http\Controllers\PenarikanController::class, 'create'])->name('penarikan-create');
        Route::post('create', [App\Http\Controllers\PenarikanController::class, 'store'])->name('penarikan-create');

        Route::get('history', [App\Http\Controllers\PenarikanController::class, 'history'])->name('penarikan-history');
        Route::post('history', [App\Http\Controllers\PenarikanController::class, 'history'])->name('penarikan-history');

        Route::get('receipt/{id}', [App\Http\Controllers\PenarikanController::class, 'receipt'])->where('id', '[0-9]+')->name('penarikan-receipt');
        Route::get('receipt/download/{id}', [App\Http\Controllers\PenarikanController::class, 'downloadReceipt'])->where('id', '[0-9]+')->name('penarikan-receipt-download');

        Route::get('anggota/detail/{id}', [App\Http\Controllers\PenarikanController::class, 'detailAnggota'])->where('id', '[0-9]+')->name('penarikan-detail-anggota');
        Route::get('download/pdf', [App\Http\Controllers\PenarikanController::class, 'createPDF'])->name('penarikan-download-pdf');
        Route::get('download/excel', [App\Http\Controllers\PenarikanController::class, 'createExcel'])->name('penarikan-download-excel');

        Route::get('import/excel', [App\Http\Controllers\PenarikanController::class, 'importExcel'])->name('penarikan-import-excel');
        Route::post('import/excel', [App\Http\Controllers\PenarikanController::class, 'storeImportExcel'])->name('penarikan-import-excel');

        Route::post('update-status', [App\Http\Controllers\PenarikanController::class, 'updateStatus'])->name('penarikan-update-status');

        Route::get('print-jkk', [App\Http\Controllers\PenarikanController::class, 'printJkk'])->name('penarikan-print-jkk');
        Route::post('print-jkk', [App\Http\Controllers\PenarikanController::class, 'storePrintJkk'])->name('penarikan-print-jkk');

        Route::get('detail-transfer/{id}', [App\Http\Controllers\PenarikanController::class, 'detailTransfer'])->name('penarikan-detail-transfer');

        Route::get('data-jurnal/{id}', [App\Http\Controllers\PenarikanController::class, 'viewDataJurnalPenarikan'])->name('view-data-jurnal-penarikan');
    });
});

// Tabungan
Route::group(['prefix' => 'tabungan'], function () {
    Route::group(['middleware' => ['auth', 'check']], function () {
        Route::get('list', [App\Http\Controllers\TabunganController::class, 'index'])->name('tabungan-list');
        Route::get('create', [App\Http\Controllers\TabunganController::class, 'create'])->name('tabungan-create');
        Route::post('create', [App\Http\Controllers\TabunganController::class, 'store'])->name('tabungan-create');
        Route::get('edit/{id}', [App\Http\Controllers\TabunganController::class, 'edit'])->where('id', '[0-9]+')->name('tabungan-edit');
        Route::post('edit/{id}', [App\Http\Controllers\TabunganController::class, 'update'])->where('id', '[0-9]+')->name('tabungan-edit');
        Route::delete('delete/{id}', [App\Http\Controllers\TabunganController::class, 'delete'])->where('id', '[0-9]+')->name('tabungan-delete');
        Route::get('import', [App\Http\Controllers\TabunganController::class, 'importTabungan'])->name('tabungan-import');
        Route::post('import', [App\Http\Controllers\TabunganController::class, 'storeImportTabungan'])->name('tabungan-import');
    });
});

// Notifikasi
Route::group(['prefix' => 'notifications'], function() {
    Route::group(['middleware' => ['auth', 'check']], function () {
        Route::get('/', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications');
        Route::post('/', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications');
        Route::post('update-status-notif', [App\Http\Controllers\NotificationController::class, 'updateStatus'])->name('notification-status-update');
    });
});

// Invoice
Route::group(['prefix' => 'invoice'], function() {
    Route::group(['middleware' => ['auth', 'check']], function () {
        Route::get('', [App\Http\Controllers\InvoiceController::class, 'index'])->name('invoice-list');
        Route::post('', [App\Http\Controllers\InvoiceController::class, 'index'])->name('invoice-list');
        Route::get('data', [App\Http\Controllers\InvoiceController::class, 'indexAjax'])->name('invoice-list-ajax');
        Route::get('{id}', [App\Http\Controllers\InvoiceController::class, 'show'])->where('id', '[0-9]+')->name('invoice-detail');
    });
});

// jurnal
Route::group(['prefix' => 'jurnal'], function() {
    Route::group(['middleware' => ['auth']], function () {
        Route::get('', [App\Http\Controllers\JurnalController::class, 'index'])->name('jurnal-list');
        Route::get('data', [App\Http\Controllers\JurnalController::class, 'indexAjax'])->name('jurnal-list-ajax');
        Route::post('', [App\Http\Controllers\JurnalController::class, 'index'])->name('jurnal-list');
    });
});

// jurnal umum
Route::group(['prefix' => 'jurnal-umum'], function() {
    Route::group(['middleware' => ['auth']], function () {
        Route::get('list', [App\Http\Controllers\JurnalUmumController::class, 'index'])->name('jurnal-umum-list');
        Route::get('list/data', [App\Http\Controllers\JurnalUmumController::class, 'indexAjax'])->name('jurnal-umum-list-ajax');
        Route::get('create', [App\Http\Controllers\JurnalUmumController::class, 'create'])->name('jurnal-umum-create');
        Route::post('create', [App\Http\Controllers\JurnalUmumController::class, 'store'])->name('jurnal-umum-create');
        Route::get('/edit/{id}', [App\Http\Controllers\JurnalUmumController::class, 'edit'])->where('id', '[0-9]+')->name('jurnal-umum-edit');
        Route::post('/edit/{id}', [App\Http\Controllers\JurnalUmumController::class, 'update'])->where('id', '[0-9]+')->name('jurnal-umum-edit');
        Route::get('/detail/{id}', [App\Http\Controllers\JurnalUmumController::class, 'show'])->name('jurnal-umum-detail');
    });
});

// saldo awal
Route::group(['prefix' => 'saldo-awal'], function() {
    Route::group(['middleware' => ['auth']], function () {
        Route::get('list', [App\Http\Controllers\SaldoAwalController::class, 'index'])->name('saldo-awal-list');
        Route::get('list/data', [App\Http\Controllers\SaldoAwalController::class, 'indexAjax'])->name('saldo-awal-list-ajax');
        Route::get('create', [App\Http\Controllers\SaldoAwalController::class, 'create'])->name('saldo-awal-create');
        Route::post('create', [App\Http\Controllers\SaldoAwalController::class, 'store'])->name('saldo-awal-create');
        Route::get('/edit/{id}', [App\Http\Controllers\SaldoAwalController::class, 'edit'])->where('id', '[0-9]+')->name('saldo-awal-edit');
        Route::post('/edit/{id}', [App\Http\Controllers\SaldoAwalController::class, 'update'])->where('id', '[0-9]+')->name('saldo-awal-edit');
        Route::get('import/excel', [App\Http\Controllers\SaldoAwalController::class, 'importExcel'])->name('saldo-awal-import-excel');
        Route::post('import/excel', [App\Http\Controllers\SaldoAwalController::class, 'storeImportExcel'])->name('saldo-awal-import-excel');
        Route::get('download/excel', [App\Http\Controllers\SaldoAwalController::class, 'createExcel'])->name('saldo-awal-download-excel');
    });
});

// buku besar
Route::group(['prefix' => 'buku-besar'], function() {
    Route::group(['middleware' => ['auth']], function () {
        Route::get('', [App\Http\Controllers\BukuBesarController::class, 'index'])->name('buku-besar-list');
        Route::get('data', [App\Http\Controllers\BukuBesarController::class, 'indexAjax'])->name('buku-besar-list-ajax');
        Route::post('', [App\Http\Controllers\BukuBesarController::class, 'index'])->name('buku-besar-list');
    });
});
