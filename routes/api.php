<?php

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

// old routes version
Route::group(['prefix' => 'v2'], function () {
    Route::match(['get', 'post'], '{any}', 'Api\ReferensiController@OldAPI')->where('any', '.*');
});

// open routes
Route::group(['prefix' => 'v3'], function () {
    Route::post('deploy', 'DeployController@DeployApps');
    // auth
    Route::group(['prefix' => 'auth'], function () {
        Route::post('register', 'Api\AuthController@RegisterPelapor');
        Route::post('login', 'Api\AuthController@LoginPelapor');
        Route::post('refresh-token', 'Api\AuthController@RefreshToken');
        Route::post('logout', 'Api\AuthController@logout');
        Route::post('provider/{provider}', 'Api\AuthController@CreateTokenForSocialLogin');
    });
    // account
    Route::group(['prefix' => 'account'], function () {
        Route::post('forgot-password', 'Api\AuthController@ResetPasswordOTP');
        Route::post('update-password', 'Api\AuthController@UpdateForgotPassword');
    });
});

// secured routes
Route::group(['prefix' => 'v3', 'middleware' => ['auth:pelapors-api']], function () {
    // account
    Route::group(['prefix' => 'account'], function () {
        Route::get('profile', 'Api\AuthController@pelapor');
        Route::post('update/name', 'Api\AuthController@UpdateName');
        Route::post('update/password', 'Api\AuthController@UpdatePassword');
        Route::post('delete', 'Api\AuthController@DeletePelapor');
    });
    // reference
    Route::group(['prefix' => 'reference'], function () {
        Route::get('jenis-laporan', 'Api\ReferensiController@getJenisLaporan');
        Route::get('jenis-pelanggaran', 'Api\ReferensiController@getJenisPelanggaran');
        Route::get('jenis-apresiasi', 'Api\ReferensiController@getJenisApresiasi');
        Route::get('bentuk-pelanggaran', 'Api\ReferensiController@getBentukPelanggaran');
        Route::get('kawasan', 'Api\ReferensiController@getKawasan');
    });
    // report
    Route::group(['prefix' => 'report'], function () {
        Route::get('list', 'Api\LaporController@listLaporan');
        Route::get('detail/{id}', 'Api\LaporController@detailLaporan');
        Route::get('notification/{id}', 'Api\LaporController@notifLaporan');
        Route::post('send', 'Api\LaporController@lapor');
    });
    // link
    Route::group(['prefix' => 'link'], function () {
        Route::get('list', 'Api\ExternalLinkController@listLink');
        Route::get('detail/{id}', 'Api\ExternalLinkController@getOneBlog');
    });
    // content
    Route::group(['prefix' => 'content'], function () {
        Route::get('banner','Api\ContentController@getBanner');
        Route::get('instagram','Api\ContentController@getInstagramContent');
        Route::get('website-content','Api\ContentController@getWebsiteContent');
        Route::get('website-content/{id}','Api\ContentController@getWebsiteContentDetail');
        Route::get('static-page','Api\ContentController@getStaticPageContent');
    });
    // other
    Route::get('notif', 'Api\PushNotificationController@notification');
});
