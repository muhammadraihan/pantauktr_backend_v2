<?php

use Illuminate\Http\Request;

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

Route::group(['prefix' => 'v1'], function() {
    Route::post('login', 'Api\AuthController@Login');
});

// Auth route
Route::group(['prefix' => 'v1', 'middleware' => ['jwt']], function() {
    Route::get('logout', 'Api\AuthController@Logout');
});

Route::group(['prefix' => 'v2'], function(){
    Route::get('jenisLaporan','Api\ReferensiController@getJenisLaporan');
    Route::get('jenisPelanggaran','Api\ReferensiController@getJenisPelanggaran');
    Route::get('jenisApresiasi','Api\ReferensiController@getJenisApresiasi');
    Route::post('lapor','Api\LaporController@lapor');
});