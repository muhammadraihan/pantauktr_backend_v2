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

Route::group(['prefix' => 'v2'], function() {
    Route::post('deploy', 'DeployController@DeployApps');
    Route::post('register','Api\AuthController@RegisterPelapor');
    Route::post('login', 'Api\AuthController@LoginPelapor');
    Route::get('login/{provider}', 'Api\AuthController@redirect');
    Route::post('login/{provider}/token','Api\AuthController@getToken');
    Route::get('refresh-token','Api\AuthController@RefreshToken');
    Route::post('pelapor/forgot-password','Api\AuthController@ResetPasswordOTP');
    Route::post('pelapor/update-password','Api\AuthController@UpdateForgotPassword');
});

// Route::group(['prefix' => 'v2', 'middleware' => ['jwt']], function() {
Route::group(['prefix' => 'v2'], function(){
    Route::get('checktoken','Api\AuthController@checkToken');
    Route::get('logout', 'Api\AuthController@logout');
    Route::get('/profil/pelapor', 'Api\AuthController@pelapor');
    Route::get('jenis-laporan','Api\ReferensiController@getJenisLaporan');
    Route::get('jenis-pelanggaran','Api\ReferensiController@getJenisPelanggaran');
    Route::get('jenis-apresiasi','Api\ReferensiController@getJenisApresiasi');
    Route::post('lapor','Api\LaporController@lapor');
    Route::get('laporan','Api\LaporanController@listLaporan');

});