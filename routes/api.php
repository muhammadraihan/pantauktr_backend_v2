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
    Route::get('login/{provider}', 'Api\AuthController@RedirectLogin');
    Route::post('login', 'Api\AuthController@LoginPelapor');
    Route::post('register', 'Api\AuthController@RegisterPelapor');
    Route::post('login/{provider}/token','Api\AuthController@CreateTokenForSocialLogin');
    Route::post('deploy', 'DeployController@DeployApps');
    Route::get('refresh-token','Api\AuthController@RefreshToken');
    Route::post('pelapor/forgot-password','Api\AuthController@ResetPasswordOTP');
    Route::post('pelapor/update-password','Api\AuthController@UpdateForgotPassword');
});

Route::group(['prefix' => 'v2', 'middleware' => ['jwt']], function() {
  Route::get('checktoken','Api\AuthController@CheckToken');
  Route::get('logout', 'Api\AuthController@Logout');
  Route::get('profile', 'Api\AuthController@Pelapor');
  Route::get('jenisLaporan','Api\ReferensiController@getJenisLaporan');
  Route::get('jenisPelanggaran','Api\ReferensiController@getJenisPelanggaran');
  Route::get('jenisApresiasi','Api\ReferensiController@getJenisApresiasi');
  Route::post('lapor','Api\LaporController@lapor');
});