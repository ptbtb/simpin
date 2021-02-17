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

Route::group(['prefix' => 'user'], function ()
{
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('', 'App\Http\Controllers\api\UserController@getUser')->name('api-get-user');
        Route::get('logout', 'App\Http\Controllers\api\UserController@logout')->name('api-get-user');
    });
});

Route::get('jenis-pinjaman', 'App\Http\Controllers\api\JenisPinjamanController@index')->name('api-list-jenis-pinjaman');
Route::get('jenis-penghasilan', 'App\Http\Controllers\api\JenisPenghasilanController@index')->name('api-list-jenis-penghasilan');

Route::group(['prefix' => 'pengajuan-pinjaman', 'middleware' => 'auth:api'], function ()
{
    Route::post('store', 'App\Http\Controllers\api\PengajuanPinjamanController@store')->name('api-store-pengajuan-pinjaman');
});