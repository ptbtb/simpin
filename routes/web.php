<?php

use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CodeController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\JkkPrintedController;
use App\Http\Controllers\MigrationController;
use App\Http\Controllers\PendapatanController;
use App\Http\Controllers\PinjamanController;
use App\Http\Controllers\SHUController;
use App\Http\Controllers\SumberDanaController;
use App\Http\Controllers\TransferredSHUController;
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

Route::get('generate-jkk', [MigrationController::class, 'generateJkkPrinted']);
Route::get('audit', [App\Http\Controllers\AuditController::class, 'index'])->name('audit');
Route::post('audit', [App\Http\Controllers\AuditController::class, 'index'])->name('audit');
Route::get('auditJurnal', [App\Http\Controllers\AuditJurnalController::class, 'index'])->name('auditJurnal');
Route::get('auditJurnalAjax', [App\Http\Controllers\AuditJurnalController::class, 'auditJurnalAjax'])->name('audit-jurnal-ajax');
Route::post('auditJurnal', [App\Http\Controllers\AuditJurnalController::class, 'index'])->name('auditJurnal');
Route::post('auditJurnal/delete', [App\Http\Controllers\AuditJurnalController::class, 'destroy'])->name('auditJurnal-delete');

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::post('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('resend-email-activation', [App\Http\Controllers\Auth\ResendEmailActivationController::class, 'create'])->name('resend-email-activation');
Route::post('resend-email-activation', [App\Http\Controllers\Auth\ResendEmailActivationController::class, 'store'])->name('resend-email-activation');

Route::get('testEvent', [App\Http\Controllers\MigrationController::class, 'index'])->name('test-event');

Route::get('migrationJurnalTransaction/{month}', [App\Http\Controllers\MigrationController::class, 'migrationJurnalTransaction'])->name('migration-jurnal-transaction');
Route::get('migrationJurnalTransaction2/{month}', [App\Http\Controllers\Migration2Controller::class, 'migrationJurnalTransaction'])->name('migration-jurnal-transaction');

Route::get('migrationAnggotaPensiunan', [App\Http\Controllers\MigrationController::class, 'migrationAnggotaPensiun'])->name('migration-anggota-pensiunan');

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

        Route::get('search-pinjaman/{id}', [PinjamanController::class, 'searchPinjamanAnggota'])->name('searchPinjamanAnggota');
        Route::get('keluar-anggota/{id}', [AnggotaController::class, 'keluarAnggota'])->name('keluar-anggota');
        Route::get('batal-keluar-anggota/{id}', [AnggotaController::class, 'batalKeluarAnggota'])->name('keluar-anggota');
        Route::get('history/{id}', [App\Http\Controllers\AnggotaController::class, 'history'])->where('id', '[0-9]+')->name('anggota-history');
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
    Route::get('/codetrans/destroy/{id}', [App\Http\Controllers\SettingCodeTransController::class, 'destroy'])->name('kode-transaksi-delete');
    Route::get('/codetrans/create', [App\Http\Controllers\SettingCodeTransController::class, 'create'])->name('kode-transaksi-create');
    Route::post('/codetrans/store', [App\Http\Controllers\SettingCodeTransController::class, 'store'])->name('kode-transaksi-store');
    Route::get('/codetrans/excel', [App\Http\Controllers\SettingCodeTransController::class, 'createExcel'])->name('kode-transaksi-excel');
    Route::get('/codetrans/import/excel', [App\Http\Controllers\SettingCodeTransController::class, 'importExcel'])->name('coa-import-excel');
    Route::post('/codetrans/import/excel', [App\Http\Controllers\SettingCodeTransController::class, 'storeImportExcel'])->name('coa-import-excel');

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

    Route::group(['prefix' => 'simpin-rule', 'middleware' => 'auth'], function ()
    {
        Route::get('', [App\Http\Controllers\SimpinRuleController::class, 'index'])->name('simpin-rule-list');
        Route::get('create', [App\Http\Controllers\SimpinRuleController::class, 'create'])->name('simpin-rule-create');
        Route::post('create', [App\Http\Controllers\SimpinRuleController::class, 'store'])->name('simpin-rule-create');
        Route::get('edit/{id}', [App\Http\Controllers\SimpinRuleController::class, 'edit'])->where('id', '[0-9]+')->name('simpin-rule-edit');
        Route::post('edit/{id}', [App\Http\Controllers\SimpinRuleController::class, 'update'])->where('id', '[0-9]+')->name('simpin-rule-edit');
    });
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
        Route::get('list/{id}', [App\Http\Controllers\PinjamanController::class, 'indexSingle'])->where('id', '[0-9]+')->name('pinjaman-list');
        Route::post('list', [App\Http\Controllers\PinjamanController::class, 'index'])->name('pinjaman-list');
        Route::get('history', [App\Http\Controllers\PinjamanController::class, 'history'])->name('pinjaman-history');
        Route::post('history', [App\Http\Controllers\PinjamanController::class, 'history'])->name('pinjaman-history');
        Route::get('create', [App\Http\Controllers\PinjamanController::class, 'create'])->name('pinjaman-create');
        Route::post('create', [App\Http\Controllers\PinjamanController::class, 'store'])->name('pinjaman-create');
        Route::get('detail/{id}', [App\Http\Controllers\PinjamanController::class, 'show'])->name('pinjaman-detail');
        Route::get('detail/{id}/excel', [App\Http\Controllers\PinjamanController::class, 'createExcelDetail'])->name('pinjaman-detail-excel');
        Route::get('edit', [App\Http\Controllers\PinjamanController::class, 'edit'])->name('pinjaman-edit');
        Route::post('edit', [App\Http\Controllers\PinjamanController::class, 'update'])->name('pinjaman-edit');
        Route::post('detail/{id}/set-discount', [App\Http\Controllers\PinjamanController::class, 'setDiscount'])->name('pinjaman-set-discount');
        Route::get('detail-pembayaran/{id}', [App\Http\Controllers\PinjamanController::class, 'detailPembayaran'])->name('pinjaman-detail-pembayaran');
        Route::get('download/pdf', [App\Http\Controllers\PinjamanController::class, 'createPDF'])->name('pinjaman-download-pdf');
        Route::get('download/pdf/single', [App\Http\Controllers\PinjamanController::class, 'createPDFSingle'])->name('pinjaman-download-pdf-single');
        Route::get('download/excel', [App\Http\Controllers\PinjamanController::class, 'createExcel'])->name('pinjaman-download-excel');
        Route::get('download/excel/single', [App\Http\Controllers\PinjamanController::class, 'createExcelSingle'])->name('pinjaman-download-excel-single');
        Route::get('download-form-pinjaman', [App\Http\Controllers\PinjamanController::class, 'downloadFormPinjaman'])->name('download-form-pinjaman')->middleware(['pinjaman']);
        Route::post('download-form-pinjaman', [App\Http\Controllers\PinjamanController::class, 'simulasiPinjaman'])->name('download-form-pinjaman')->middleware(['pinjaman']);
        Route::get('generate-form-pinjaman', [App\Http\Controllers\PinjamanController::class, 'generateFormPinjaman'])->name('generate-form-pinjaman')->middleware(['pinjaman']);
        Route::post('bayar-angsuran/{id}', [App\Http\Controllers\PinjamanController::class, 'bayarAngsuran'])->name('pinjaman-bayar-angsuran');
        Route::post('bayar-angsuran/{id}/dipercepat', [App\Http\Controllers\PinjamanController::class, 'bayarAngsuranDipercepat'])->name('pinjaman-bayar-angsuran-dipercepat');
        Route::post('edit-angsuran', [App\Http\Controllers\PinjamanController::class, 'editAngsuran'])->name('pinjaman-edit-angsuran');
        Route::post('update-status', [App\Http\Controllers\PinjamanController::class, 'updateStatusAngsuran'])->name('pinjaman-angsuran-update-status');
        Route::post('/pinjaman/editsaldo', [App\Http\Controllers\PinjamanController::class, 'updatesaldoawal'])->name('edit-saldo-awalpinjaman');
        Route::get('report/download/excel', [App\Http\Controllers\PinjamanController::class, 'createExcelReport'])->name('laporan-pinjaman-download-excel');

        // import batch saldo pinjaman
        Route::get('import', [App\Http\Controllers\PinjamanController::class, 'importPinjaman'])->name('pinjaman-import');
        Route::get('importData', [App\Http\Controllers\PinjamanController::class, 'importDataPinjaman'])->name('pinjaman-importdata');
        Route::post('import', [App\Http\Controllers\PinjamanController::class, 'storeImportPinjaman'])->name('pinjaman-import');
        Route::post('importData', [App\Http\Controllers\PinjamanController::class, 'storeImportDataPinjaman'])->name('pinjaman-importdata');
        Route::delete('delete/{id}', [App\Http\Controllers\PinjamanController::class, 'destroy'])->name('pinjaman-delete');

        // import angsuran
        Route::get('import-angsuran', [App\Http\Controllers\AngsuranController::class, 'importAngsuran'])->name('import_angsuran');
        Route::post('import-angsuran', [App\Http\Controllers\AngsuranController::class, 'storeImportAngsuran'])->name('import_angsuran');
        Route::get('angsuran/jurnal/{id}', [App\Http\Controllers\AngsuranController::class, 'jurnalShow'])->name('angsuran.jurnal');
        // laporan
        Route::get('report', [App\Http\Controllers\PinjamanController::class, 'report'])->name('pinjaman-report');

        Route::group(['prefix' => 'pengajuan'], function () {
            Route::get('list', [App\Http\Controllers\PinjamanController::class, 'indexPengajuan'])->name('pengajuan-pinjaman-list');
            Route::get('list/data/', [App\Http\Controllers\PinjamanController::class, 'indexPengajuanAjax'])->name('pengajuan-pinjaman-ajax');
            Route::get('download/excel', [App\Http\Controllers\PinjamanController::class, 'createExcelPengajuanPinjaman'])->name('download-pengajuan-pinjaman-excel');
            Route::get('data-jurnal/{kodepengajuan}', [App\Http\Controllers\PinjamanController::class, 'viewDataJurnalPinjaman'])->name('view-data-jurnal-pengajuan-pinjaman');
            Route::get('data-coa/{kodepengajuan}', [App\Http\Controllers\PinjamanController::class, 'viewDataCoaBank'])->name('view-data-coa-pengajuan-pinjaman');
            Route::post('update/data-coa/{kodepengajuan}', [App\Http\Controllers\PinjamanController::class, 'storeDataCoaBank'])->name('vupdate-coa-pengajuan-pinjaman');
            Route::get('print-jkk', [App\Http\Controllers\PengajuanController::class, 'indexJkk'])->name('pengajuan-pinjaman-print-jkk');
            Route::post('print-jkk-store', [App\Http\Controllers\PengajuanController::class, 'printJkk'])->name('store-pengajuan-pinjaman-print-jkk');
            Route::get('create', [App\Http\Controllers\PinjamanController::class, 'createPengajuanPinjaman'])->name('pengajuan-pinjaman-add')->middleware(['pinjaman']);
            Route::get('maxPinjaman', [App\Http\Controllers\PinjamanController::class, 'calculateMaxPinjaman'])->name('pengajuan-pinjaman-calculate-max-pinjaman');
            Route::post('create', [App\Http\Controllers\PinjamanController::class, 'storePengajuanPinjaman'])->name('pengajuan-pinjaman-add');
            Route::get('calculate-angsuran', [App\Http\Controllers\PinjamanController::class, 'calculateAngsuran'])->name('pengajuan-pinjaman-calculate-angsuran');
            Route::post('update-status', [App\Http\Controllers\PinjamanController::class, 'updateStatusPengajuanPinjaman'])->name('pengajuan-pinjaman-update-status');

        });

        Route::get('saldo-awal/excel', [PinjamanController::class, 'exportSaldoAwalPinjaman'])->name('export-saldo-awal-pinjaman');
    });
});


//simpanan
Route::group(['prefix' => 'simpanan'], function () {
    Route::group(['middleware' => ['auth', 'check']], function () {
        Route::get('list', [App\Http\Controllers\SimpananController::class, 'index'])->name('simpanan-list');
        Route::post('list', [App\Http\Controllers\SimpananController::class, 'index'])->name('simpanan-list');
        Route::get('list/data', [App\Http\Controllers\SimpananController::class, 'indexAjax'])->name('simpanan-list-ajax');
        Route::get('jurnal/{kode_simpanan}', [App\Http\Controllers\SimpananController::class, 'showJurnal'])->name('simpanan-view-jurnal');
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
        Route::post('edit-simpanan', [App\Http\Controllers\SimpananController::class, 'update'])->name('simpanan-edit');
        Route::post('update-status', [App\Http\Controllers\SimpananController::class, 'updateStatusSimpanan'])->name('simpanan-update-status');
         Route::get('pendingJurnal', [App\Http\Controllers\SimpananController::class, 'pendingJurnal'])->name('simpanan-pending-jurnal');
         Route::post('pendingJurnal', [App\Http\Controllers\SimpananController::class, 'pendingJurnal'])->name('simpanan-pending-jurnal');
         Route::post('postJurnal', [App\Http\Controllers\SimpananController::class, 'postPendingJurnal'])->name('simpanan-post-jurnal');

        Route::group(['prefix' => 'card'], function () {
            Route::get('', [App\Http\Controllers\SimpananController::class, 'indexCard'])->name('simpanan-index-card');
            Route::get('view/{kodeAnggota}', [App\Http\Controllers\SimpananController::class, 'showCard'])->name('simpanan-show-card');
            Route::get('download/pdf/{kodeAnggota}', [App\Http\Controllers\SimpananController::class, 'downloadPDFCard'])->name('simpanan-download-pdf-card');
            Route::get('download/excel/{kodeAnggota}', [App\Http\Controllers\SimpananController::class, 'downloadExcelCard'])->name('simpanan-download-pdf-card');
        });

        Route::get('laporan', [App\Http\Controllers\SimpananController::class, 'laporan'])->name('laporan-simpanan');
        Route::post('laporan', [App\Http\Controllers\SimpananController::class, 'laporan'])->name('filter-laporan-simpanan');
        Route::get('laporan/excel', [App\Http\Controllers\SimpananController::class, 'laporanExcel'])->name('laporan-simpanan-excel');
        Route::post('delete', [App\Http\Controllers\SimpananController::class, 'delete'])->name('simpanan-delete');
        Route::get('data-coa/{kodepengajuan}', [App\Http\Controllers\SimpananController::class, 'viewDataCoaBank'])->name('view-data-coa-penarikan');
        Route::post('update/data-coa/{kodepengajuan}', [App\Http\Controllers\SimpananController::class, 'storeDataCoaBank'])->name('update-coa-penarikan');

    });
});

// penarikan
Route::group(['prefix' => 'penarikan'], function () {
    Route::group(['middleware' => ['auth', 'check']], function () {
        Route::get('list', [App\Http\Controllers\PenarikanController::class, 'index'])->name('penarikan-index');
        Route::post('list', [App\Http\Controllers\PenarikanController::class, 'index'])->name('penarikan-index');
        Route::get('list/data/', [App\Http\Controllers\PenarikanController::class, 'indexAjax'])->name('penarikan-index-ajax');
        Route::get('list/export-excel', [App\Http\Controllers\PenarikanController::class, 'exportExcel'])->name('penarikan-list-export-excel');

        Route::get('create', [App\Http\Controllers\PenarikanController::class, 'create'])->name('penarikan-create');
        Route::get('createSpv', [App\Http\Controllers\PenarikanController::class, 'createspv'])->name('penarikan-createspv');
        Route::post('create', [App\Http\Controllers\PenarikanController::class, 'store'])->name('penarikan-create');
        Route::post('createSpv', [App\Http\Controllers\PenarikanController::class, 'storespv'])->name('penarikan-createspv');

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

        Route::get('keluar-anggota', [App\Http\Controllers\PenarikanController::class, 'showFormKeluarAnggota'])->name('show-form-keluar-anggota');
        Route::post('keluar-anggota', [App\Http\Controllers\PenarikanController::class, 'storeFormKeluarAnggota'])->name('store-form-keluar-anggota');
        Route::get('delete/{id}', [App\Http\Controllers\PenarikanController::class, 'delete'])->name('penarikan-delete');
        Route::post('delete', [App\Http\Controllers\PenarikanController::class, 'delete'])->name('penarikan-delete');
        Route::get('data-coa/{kodepengajuan}', [App\Http\Controllers\PenarikanController::class, 'viewDataCoaBank'])->name('view-data-coa-penarikan');
        Route::post('update/data-coa/{kodepengajuan}', [App\Http\Controllers\PenarikanController::class, 'storeDataCoaBank'])->name('update-coa-penarikan');

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
        Route::get('download-excel', [App\Http\Controllers\InvoiceController::class, 'downloadExcel'])->name('invoice-list-download-excel');
        Route::get('{id}', [App\Http\Controllers\InvoiceController::class, 'show'])->where('id', '[0-9]+')->name('invoice-detail');
    });
});

// jurnal
Route::group(['prefix' => 'jurnal'], function() {
    Route::group(['middleware' => ['auth']], function () {
        Route::get('', [App\Http\Controllers\JurnalController::class, 'index'])->name('jurnal-list');
        Route::get('data', [App\Http\Controllers\JurnalController::class, 'indexAjax'])->name('jurnal-list-ajax');
        Route::get('excel', [App\Http\Controllers\JurnalController::class, 'createExcel'])->name('jurnal-export-excel');
        Route::post('', [App\Http\Controllers\JurnalController::class, 'index'])->name('jurnal-list');
    });
});

// jurnal umum
Route::group(['prefix' => 'jurnal-umum'], function() {
    Route::group(['middleware' => ['auth']], function () {
        Route::get('list', [App\Http\Controllers\JurnalUmumController::class, 'index'])->name('jurnal-umum-list');
        Route::post('list', [App\Http\Controllers\JurnalUmumController::class, 'index'])->name('jurnal-umum-list');
        Route::get('list/data', [App\Http\Controllers\JurnalUmumController::class, 'indexAjax'])->name('jurnal-umum-list-ajax');
        Route::get('create', [App\Http\Controllers\JurnalUmumController::class, 'create'])->name('jurnal-umum-create');
        Route::post('create', [App\Http\Controllers\JurnalUmumController::class, 'store'])->name('jurnal-umum-create');
        Route::get('/edit/{id}', [App\Http\Controllers\JurnalUmumController::class, 'edit'])->where('id', '[0-9]+')->name('jurnal-umum-edit');
        Route::post('/edit/{id}', [App\Http\Controllers\JurnalUmumController::class, 'update'])->where('id', '[0-9]+')->name('jurnal-umum-edit');
        Route::get('/detail/{id}', [App\Http\Controllers\JurnalUmumController::class, 'show'])->name('jurnal-umum-detail');
        Route::post('update-status', [App\Http\Controllers\JurnalUmumController::class, 'updateStatusJurnalumum'])->name('jurnal-umum-update-status');
        Route::get('print-jkk', [App\Http\Controllers\JurnalUmumController::class, 'indexJkk'])->name('jurnal-umum-index-jkk');
        Route::post('print-jkk', [App\Http\Controllers\JurnalUmumController::class, 'printJkk'])->name('jurnal-umum-print-jkk');
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
        Route::get('download/excel', [App\Http\Controllers\BukuBesarController::class, 'createExcel'])->name('buku-besar-download-excel');
    });
});

// neraca
Route::group(['prefix' => 'neraca'], function() {
    Route::group(['middleware' => ['auth']], function () {
        Route::get('', [App\Http\Controllers\NeracaController::class, 'index'])->name('neraca-list');
        Route::post('', [App\Http\Controllers\NeracaController::class, 'index'])->name('neraca-list');
        Route::get('download/excel/{period}', [App\Http\Controllers\NeracaController::class, 'createExcel'])->name('neraca-download-excel');
        Route::get('download/pdf/{period}', [App\Http\Controllers\NeracaController::class, 'createPdf'])->name('neraca-download-pdf');
    });
});

// laba rugi
Route::group(['prefix' => 'laba-rugi'], function() {
    Route::group(['middleware' => ['auth']], function () {
        Route::get('', [App\Http\Controllers\LabaRugiController::class, 'index'])->name('laba-rugi-list');
        Route::get('getshu', [App\Http\Controllers\LabaRugiController::class, 'getSHU'])->name('laba-rugi-shu');
        Route::post('', [App\Http\Controllers\LabaRugiController::class, 'index'])->name('laba-rugi-list');
        Route::get('download/excel', [App\Http\Controllers\LabaRugiController::class, 'createExcel'])->name('laba-rugi-download-excel');
    });
});

Route::group(['prefix' => 'global'], function () {
     Route::group(['middleware' => ['auth']], function () {
        Route::get('transaksiuser', [App\Http\Controllers\GlobalTransaksiController::class, 'importTransaksiUser'])->name('global-transaksiuser-import-excel');
        Route::post('', [App\Http\Controllers\GlobalTransaksiController::class, 'storeTransaksiUser'])->name('global-transaksiuser-import-excel');
     });
});

Route::get('list-shu', [SHUController::class, 'index'])->middleware('auth')->name('list-shu');
Route::get('list-shu/import-excel', [SHUController::class, 'import'])->middleware('auth')->name('list-shu.import');
Route::post('list-shu/import-excel', [SHUController::class, 'storeImport'])->middleware('auth')->name('list-shu.storeImport');
Route::get('list-shu/export-excel', [SHUController::class, 'exportExcel'])->middleware('auth')->name('list-shu.exportExcel');
Route::get('list-shu/{id}/download-card', [SHUController::class, 'downloadCard'])->middleware('auth')->name('list-shu.downloadCard');

Route::get('transferred-shu', [TransferredSHUController::class, 'index'])->middleware('auth')->name('transferred-shu.index');
Route::get('transferred-shu/import-excel', [TransferredSHUController::class, 'import'])->middleware('auth')->name('transferred-shu.import');
Route::post('transferred-shu/import-excel', [TransferredSHUController::class, 'storeImport'])->middleware('auth')->name('transferred-shu.storeImport');
Route::get('transferred-shu/export-excel', [TransferredSHUController::class, 'exportExcel'])->middleware('auth')->name('transferred-shu.exportExcel');

Route::Group(['prefix' => 'pendapatan', 'middleware' => 'auth'], function ()
{
    /*Route::get('laporan', [App\Http\Controllers\SimpananController::class, 'laporan'])->name('laporan-simpanan');
    Route::post('laporan', [App\Http\Controllers\SimpananController::class, 'laporan'])->name('filter-laporan-simpanan');
    Route::get('laporan/excel', [App\Http\Controllers\SimpananController::class, 'laporanExcel'])->name('laporan-simpanan-excel');*/

    Route::get('laporan', [PendapatanController::class,  'laporan'])->name('laporan.pendapatan');
    Route::post('laporan', [PendapatanController::class,  'laporan'])->name('filter.laporan.pendapatan');
    Route::get('laporan/excel', [PendapatanController::class,  'downloadExcel'])->name('excel.laporan.pendapatan');
});

Route::Group(['prefix' => 'bank', 'middleware' => 'auth'], function (){
    Route::get('list', [App\Http\Controllers\BankController::class,  'index'])->name('bank.list');
    Route::get('create', [App\Http\Controllers\BankController::class,  'create'])->name('bank.create');
    Route::post('create', [App\Http\Controllers\BankController::class,  'store'])->name('bank.create');
    Route::get('edit/{id}', [App\Http\Controllers\BankController::class,  'edit'])->name('bank.edit');
    Route::post('edit/{id}', [App\Http\Controllers\BankController::class,  'update'])->name('bank.edit');
    Route::post('delete/{id}', [App\Http\Controllers\BankController::class,  'destroy'])->name('bank.delete');
});

// company route
Route::group(['prefix' => 'company', 'middleware' => ['auth','cors']], function ()
{
    Route::get('', [App\Http\Controllers\CompanyController::class, 'index'])->name('company.index');
    Route::get('create', [App\Http\Controllers\CompanyController::class, 'create'])->name('company.create');
    Route::post('create', [App\Http\Controllers\CompanyController::class, 'store'])->name('company.create');
    Route::get('{id}/edit', [App\Http\Controllers\CompanyController::class, 'edit'])->name('company.edit');
    Route::put('{id}/edit', [App\Http\Controllers\CompanyController::class, 'update'])->name('company.update');
    Route::get('{id}/kelas', [App\Http\Controllers\KelasCompanyController::class, 'index'])->name('company.kelas.index');
    Route::get('{id}/kelas/edit', [App\Http\Controllers\KelasCompanyController::class, 'edit'])->name('company.kelas.edit');
    Route::put('{id}/kelas/edit', [App\Http\Controllers\KelasCompanyController::class, 'update'])->name('company.kelas.update');
    Route::get('{id}/kelas/create', [App\Http\Controllers\KelasCompanyController::class, 'create'])->name('company.kelas.create');
    Route::put('{id}/kelas/create', [App\Http\Controllers\KelasCompanyController::class, 'store'])->name('company.kelas.create');

});


// jenis penghasilan route
Route::group(['prefix' => 'jenis-penghasilan', 'middleware' => 'auth'], function ()
{
    Route::get('create', [App\Http\Controllers\JenisPenghasilanController::class, 'create'])->name('jenis.penghasilan.create');
    Route::post('create', [App\Http\Controllers\JenisPenghasilanController::class, 'create'])->name('jenis.penghasilan.create');
    Route::post('store', [App\Http\Controllers\JenisPenghasilanController::class, 'store'])->name('jenis.penghasilan.store');
});

Route::prefix('budget')->middleware('auth')->group(function ()
{
    Route::get('list', [App\Http\Controllers\BudgetController::class, 'index'])->name('budget.list');
    Route::get('list/data', [App\Http\Controllers\BudgetController::class, 'indexAjax'])->name('budget.data');
    Route::get('create', [App\Http\Controllers\BudgetController::class, 'create'])->name('budget.create');
    Route::post('store', [App\Http\Controllers\BudgetController::class, 'store'])->name('budget.store');
    Route::get('{id}/edit', [App\Http\Controllers\BudgetController::class, 'edit'])->name('budget.edit');
    Route::put('{id}/update', [App\Http\Controllers\BudgetController::class, 'update'])->name('budget.update');
    Route::get('excel', [App\Http\Controllers\BudgetController::class, 'excel'])->name('budget.excel');
    Route::get('import', [App\Http\Controllers\BudgetController::class, 'import'])->name('budget.import');
    Route::post('import/store', [App\Http\Controllers\BudgetController::class, 'importStore'])->name('budget.import.store');
});

Route::Group(['prefix' => 'arus-kas', 'middleware' => 'auth'], function ()
{
    Route::get('laporan', [App\Http\Controllers\ArusKasController::class,  'laporan'])->name('laporan.arus-kas');
    Route::post('laporan', [App\Http\Controllers\ArusKasController::class,  'laporan'])->name('filter.laporan.arus-kas');
    Route::get('laporan/excel', [App\Http\Controllers\ArusKasController::class,  'downloadExcel'])->name('excel.laporan.arus-kas');
});

Route::get('code/search', [App\Http\Controllers\CodeController::class, 'search'])->name('code.search');
Route::get('code/search/{id}', [App\Http\Controllers\CodeController::class, 'searchId'])->name('code.search.id');

Route::group(['prefix' => 'jkk-printed', 'middleware' => 'auth'], function ()
{
    Route::get('list', [App\Http\Controllers\JkkPrintedController::class, 'index'])->name('jkk-printed-list');
    Route::post('list', [App\Http\Controllers\JkkPrintedController::class, 'index'])->name('jkk-printed-list');
    Route::get('list/data', [App\Http\Controllers\JkkPrintedController::class, 'indexAjax'])->name('jkk-printed-data');
    Route::post('reprint/{id}', [App\Http\Controllers\JkkPrintedController::class, 'reprint'])->name('jkk-printed-reprint');
    Route::get('detail/{id}', [App\Http\Controllers\JkkPrintedController::class, 'show'])->name('jkk-printed-show');
});

Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function ()
{
    Route::get('cleandoublesimpanan', [App\Http\Controllers\AdminController::class, 'cleanDoubleSimpanan'])->name('admin-clean-double-simpanan');
    Route::get('cleandoublepenarikan', [App\Http\Controllers\AdminController::class, 'cleanDoublePenarikan'])->name('admin-clean-double-penarikan');
    Route::get('cleandoubleperiod', [App\Http\Controllers\AdminController::class, 'cleanDoublePeriod'])->name('admin-clean-double-period');
     Route::get('cekreupload', [App\Http\Controllers\AdminController::class, 'cekjreupload'])->name('admin-cek-reupload');
     Route::get('ceksimpanannojurnal', [App\Http\Controllers\AdminController::class, 'ceksimpanannojurnal'])->name('admin-cek-simpanan-nojurnal');
     Route::get('postsimpanannojurnal', [App\Http\Controllers\AdminController::class, 'postsimpanannojurnal'])->name('admin-post-simpanan-nojurnal');
     Route::get('cekjurnalnotrans', [App\Http\Controllers\AdminController::class, 'cekjurnalnotrans'])->name('admin-cek-jurnal-notrans');
     Route::get('cekangsuran', [App\Http\Controllers\AdminController::class, 'cekangsuran'])->name('admin-cek-cekangsuran');
     Route::get('cekpinjamantanpaangsuran', [App\Http\Controllers\AdminController::class, 'cekpinjamantanpaangsuran'])->name('admin-pinjaman-noangsuran');
});

Route::resource('sumber-dana', SumberDanaController::class);
