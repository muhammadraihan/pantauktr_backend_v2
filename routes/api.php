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
        Route::get('jenis-pelanggaran', 'Api\ReferensiController@getJenisPelanggaran');
        Route::get('jenis-pelanggaran/{uuid}', 'Api\ReferensiController@getSingleJenisPelanggaran');
        Route::get('bentuk-pelanggaran', 'Api\ReferensiController@getBentukPelanggaran');
        Route::get('bentuk-pelanggaran/filter/{uuid}', 'Api\ReferensiController@getBentukByPelanggaran');
        Route::get('bentuk-pelanggaran/{uuid}', 'Api\ReferensiController@getSingleBentukPelanggaran');
        Route::get('kawasan', 'Api\ReferensiController@getKawasan');
        Route::get('kawasan/{uuid}', 'Api\ReferensiController@getSingleKawasan');
    });
    // report
    Route::group(['prefix' => 'report'], function () {
        Route::get('list', 'Api\LaporController@listLaporan');
        Route::get('detail/{id}', 'Api\LaporController@detailLaporan');
        Route::get('notification/{id}', 'Api\LaporController@notifLaporan');
        Route::post('send', 'Api\LaporController@lapor');
    });
    // content
    Route::group(['prefix' => 'content'], function () {
        Route::get('banner', 'Api\ContentController@getBanner');
        Route::get('external-link', 'Api\ExternalLinkController@listLink');
        Route::get('external-link/{id}', 'Api\ExternalLinkController@getOneBlog');
        Route::get('instagram', 'Api\ContentController@getInstagramContent');
        Route::get('static-page', 'Api\ContentController@getStaticPageContent');
        Route::get('website-content', 'Api\ContentController@getWebsiteContent');
        Route::get('website-content/{id}', 'Api\ContentController@getWebsiteContentDetail');
    });
    // other
    Route::get('notif', 'Api\PushNotificationController@notification');
});
