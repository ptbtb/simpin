<?php

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

// anggota
Route::group(['prefix' => 'anggota'], function ()
{
    Route::group(['middleware' => 'auth'], function ()
    {
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

        Route::get('download/pdf', [App\Http\Controllers\AnggotaController::class, 'createPDF'])->name('anggota-download-pdf');
        Route::get('download/excel', [App\Http\Controllers\AnggotaController::class, 'createExcel'])->name('anggota-download-excel');
    });
});

//simpanan
Route::get('/simpanan', [App\Http\Controllers\SimpananController::class, 'index']);

// setting
Route::group(['prefix' => 'setting'], function ()
{
        //simpanan
    Route::get('/simpanan', [App\Http\Controllers\SettingSimpananController::class, 'index']);
    Route::get('/simpanan/edit/{id}', [App\Http\Controllers\SettingSimpananController::class, 'edit']);
    Route::post('/simpanan/update', [App\Http\Controllers\SettingSimpananController::class, 'update']);
    Route::get('/simpanan/destroy/{id}', [App\Http\Controllers\SettingSimpananController::class, 'destroy']);
    Route::get('/simpanan/create', [App\Http\Controllers\SettingSimpananController::class, 'create']);
    Route::post('/simpanan/store', [App\Http\Controllers\SettingSimpananController::class, 'store']);

        //pinjaman
    Route::get('/pinjaman', [App\Http\Controllers\SettingPinjamanController::class, 'index']);
    Route::get('/pinjaman/edit/{id}', [App\Http\Controllers\SettingPinjamanController::class, 'edit']);
    Route::post('/pinjaman/update', [App\Http\Controllers\SettingPinjamanController::class, 'update']);
    Route::get('/pinjaman/destroy/{id}', [App\Http\Controllers\SettingPinjamanController::class, 'destroy']);
    Route::get('/pinjaman/create', [App\Http\Controllers\SettingPinjamanController::class, 'create']);
    Route::post('/pinjaman/store', [App\Http\Controllers\SettingPinjamanController::class, 'store']);

        //codetrans
    Route::get('/codetrans', [App\Http\Controllers\SettingCodeTransController::class, 'index']);
    Route::get('/codetrans/edit/{id}', [App\Http\Controllers\SettingCodeTransController::class, 'edit']);
    Route::post('/codetrans/update', [App\Http\Controllers\SettingCodeTransController::class, 'update']);
    Route::get('/codetrans/destroy/{id}', [App\Http\Controllers\SettingCodeTransController::class, 'destroy']);
    Route::get('/codetrans/create', [App\Http\Controllers\SettingCodeTransController::class, 'create']);
    Route::post('/codetrans/store', [App\Http\Controllers\SettingCodeTransController::class, 'store']);

    // Jenis Anggota
    Route::group(['prefix' => 'jenis-anggota'], function ()
    {
        Route::group(['middleware' => 'auth'], function ()
        {
            Route::get('list', [App\Http\Controllers\JenisAnggotaController::class, 'index'])->name('jenis-anggota-list');
            Route::get('create', [App\Http\Controllers\JenisAnggotaController::class, 'create'])->name('jenis-anggota-create');
            Route::post('create', [App\Http\Controllers\JenisAnggotaController::class, 'store'])->name('jenis-anggota-create');
            Route::get('edit/{id}', [App\Http\Controllers\JenisAnggotaController::class, 'edit'])->where('id', '[0-9]+')->name('jenis-anggota-edit');
            Route::post('edit/{id}', [App\Http\Controllers\JenisAnggotaController::class, 'update'])->where('id', '[0-9]+')->name('jenis-anggota-edit');
            Route::delete('delete/{id}', [App\Http\Controllers\JenisAnggotaController::class, 'delete'])->where('id', '[0-9]+')->name('jenis-anggota-delete');
        });
    });
});

// user
Route::group(['prefix' => 'user'], function ()
{
	Route::group(['middleware' => 'auth'], function ()
	{
        Route::get('list', [App\Http\Controllers\UserController::class, 'index'])->name('user-list');
        Route::post('list', [App\Http\Controllers\UserController::class, 'index'])->name('user-list');
        Route::get('list/data', [App\Http\Controllers\UserController::class, 'indexAjax'])->name('user-list-ajax');
        Route::get('create', [App\Http\Controllers\UserController::class, 'create'])->name('user-create');
        Route::post('create', [App\Http\Controllers\UserController::class, 'store'])->name('user-create');
        Route::get('/edit/{id}', [App\Http\Controllers\UserController::class, 'edit'])->where('id', '[0-9]+')->name('user-edit');
        Route::post('/edit/{id}', [App\Http\Controllers\UserController::class, 'update'])->where('id', '[0-9]+')->name('user-edit');
        Route::delete('delete/{id}', [App\Http\Controllers\UserController::class, 'delete'])->where('id', '[0-9]+')->name('user-delete');
        
		Route::get('profile', [App\Http\Controllers\UserController::class, 'profile'])->name('user-profile');
        Route::post('profile', [App\Http\Controllers\UserController::class, 'updateProfile'])->name('user-profile');
        
        Route::get('change-password', [App\Http\Controllers\UserController::class, 'changePassword'])->name('user-change-password');
		Route::post('change-password', [App\Http\Controllers\UserController::class, 'updatePassword'])->name('user-change-password');
	});
});

// role
Route::group(['prefix' => 'role'], function ()
{
	Route::group(['middleware' => 'auth'], function ()
	{
        Route::get('list', [App\Http\Controllers\RoleController::class, 'index'])->name('role-list');
        Route::get('create', [App\Http\Controllers\RoleController::class, 'create'])->name('role-create');
        Route::post('create', [App\Http\Controllers\RoleController::class, 'store'])->name('role-create');
        Route::get('/edit/{id}', [App\Http\Controllers\RoleController::class, 'edit'])->where('id', '[0-9]+')->name('role-edit');
        Route::post('/edit/{id}', [App\Http\Controllers\RoleController::class, 'update'])->where('id', '[0-9]+')->name('role-edit');
        Route::delete('/delete/{id}', [App\Http\Controllers\RoleController::class, 'delete'])->where('id', '[0-9]+')->name('role-delete');
	});
});