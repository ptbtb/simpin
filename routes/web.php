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

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/anggota', [App\Http\Controllers\AnggotaController::class, 'index']);
Route::get('/anggota/nonaktif', [App\Http\Controllers\AnggotaController::class, 'nonaktif']);
Route::get('/anggota/all', [App\Http\Controllers\AnggotaController::class, 'all']);
Route::get('/anggota/add', [App\Http\Controllers\AnggotaController::class, 'add']);
Route::get('/anggota/edit/{id}', [App\Http\Controllers\AnggotaController::class, 'edit']);
Route::get('/anggota/destroy/{id}', [App\Http\Controllers\AnggotaController::class, 'destroy']);
Route::post('/anggota/store', [App\Http\Controllers\AnggotaController::class, 'store']);
Route::post('/anggota/update/{id}', [App\Http\Controllers\AnggotaController::class, 'update']);


//simpanan
Route::get('/simpanan', [App\Http\Controllers\SimpananController::class, 'index']);

// setting
    //simpanan
Route::get('/setting/simpanan', [App\Http\Controllers\SettingSimpananController::class, 'index']);
Route::get('/setting/simpanan/edit/{id}', [App\Http\Controllers\SettingSimpananController::class, 'edit']);
Route::post('/setting/simpanan/update', [App\Http\Controllers\SettingSimpananController::class, 'update']);
Route::get('/setting/simpanan/destroy/{id}', [App\Http\Controllers\SettingSimpananController::class, 'destroy']);
Route::get('/setting/simpanan/create', [App\Http\Controllers\SettingSimpananController::class, 'create']);
Route::post('/setting/simpanan/store', [App\Http\Controllers\SettingSimpananController::class, 'store']);
// setting
    //pinjaman
Route::get('/setting/pinjaman', [App\Http\Controllers\SettingPinjamanController::class, 'index']);
Route::get('/setting/pinjaman/edit/{id}', [App\Http\Controllers\SettingPinjamanController::class, 'edit']);
Route::post('/setting/pinjaman/update', [App\Http\Controllers\SettingPinjamanController::class, 'update']);
Route::get('/setting/pinjaman/destroy/{id}', [App\Http\Controllers\SettingPinjamanController::class, 'destroy']);
Route::get('/setting/pinjaman/create', [App\Http\Controllers\SettingPinjamanController::class, 'create']);
Route::post('/setting/pinjaman/store', [App\Http\Controllers\SettingPinjamanController::class, 'store']);

    //codetrans
Route::get('/setting/codetrans', [App\Http\Controllers\SettingCodeTransController::class, 'index']);
Route::get('/setting/codetrans/edit/{id}', [App\Http\Controllers\SettingCodeTransController::class, 'edit']);
Route::post('/setting/codetrans/update', [App\Http\Controllers\SettingCodeTransController::class, 'update']);
Route::get('/setting/codetrans/destroy/{id}', [App\Http\Controllers\SettingCodeTransController::class, 'destroy']);
Route::get('/setting/codetrans/create', [App\Http\Controllers\SettingCodeTransController::class, 'create']);
Route::post('/setting/codetrans/store', [App\Http\Controllers\SettingCodeTransController::class, 'store']);