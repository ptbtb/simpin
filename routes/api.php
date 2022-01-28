<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', 'App\Http\Controllers\api\LoginController@login')->name('api-login');

Route::group(['prefix' => 'user'], function ()
{
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('', 'App\Http\Controllers\api\UserController@getUser')->name('api-get-user');
        Route::get('logout', 'App\Http\Controllers\api\UserController@logout')->name('api-get-user');
        Route::get('disclaimer', 'App\Http\Controllers\api\UserController@disclaimer')->name('api-get-disclaimer');
        Route::get('menu', 'App\Http\Controllers\api\UserController@menu')->name('api-get-menu');
    });
});
Route::group(['prefix' => 'pinjaman'], function ()
{
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('saldo', 'App\Http\Controllers\api\PinjamanController@Saldo')->name('api-get-pinjaman-saldo');
        Route::get('rincian', 'App\Http\Controllers\api\PinjamanController@Detail')->name('api-get-pinjaman-detail');
    });
});

Route::group(['prefix' => 'simpanan'], function ()
{
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('saldo', 'App\Http\Controllers\api\SimpananController@Saldo')->name('api-get-simpanan-saldo');
        Route::get('rincian', 'App\Http\Controllers\api\SimpananController@Detail')->name('api-get-simpanan-detail');
        Route::get('card', 'App\Http\Controllers\api\SimpananController@ShowCard')->name('api-get-simpanan-card');
        Route::get('listambil', 'App\Http\Controllers\api\TransaksiController@list')->name('api-list-penarikan');
        Route::post('ajuambil', 'App\Http\Controllers\api\PenarikanController@ajuAmbil')->name('api-aju-penarikan');
    });
});

Route::get('jenis-pinjaman', 'App\Http\Controllers\api\JenisPinjamanController@index')->name('api-list-jenis-pinjaman');
Route::get('jenis-simpanan', 'App\Http\Controllers\api\JenisSimpananController@index')->name('api-list-jenis-simpanan');
Route::get('jenis-penghasilan', 'App\Http\Controllers\api\JenisPenghasilanController@index')->name('api-list-jenis-penghasilan');

Route::group(['prefix' => 'pengajuan-pinjaman', 'middleware' => 'auth:api'], function ()
{
    Route::get('list', 'App\Http\Controllers\api\PengajuanPinjamanController@index')->name('api-list-pengajuan-pinjaman');
    Route::get('detail/{kode_pengajuan}', 'App\Http\Controllers\api\PengajuanPinjamanController@show')->name('api-detail-pengajuan-pinjaman');
    Route::post('store', 'App\Http\Controllers\api\PengajuanPinjamanController@store')->name('api-store-pengajuan-pinjaman');
    Route::post('simulasi', 'App\Http\Controllers\api\PengajuanPinjamanController@simulasi')->name('api-simulasi-pengajuan-pinjaman');
});
