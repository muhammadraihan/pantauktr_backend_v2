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
Route::group(['prefix' => 'v2'], function () {
    Route::match(['get', 'post'], '{any}', 'Api\ReferensiController@OldAPI')->where('any', '.*');
});
Route::group(['prefix' => 'v3'], function () {
    Route::post('deploy', 'DeployController@DeployApps');
    Route::group(['prefix' => 'auth'], function () {
        Route::post('register', 'Api\AuthController@RegisterPelapor');
        Route::post('login', 'Api\AuthController@LoginPelapor');
        Route::post('refresh-token', 'Api\AuthController@RefreshToken');
        Route::post('logout', 'Api\AuthController@logout');
        Route::post('provider/{provider}', 'Api\AuthController@CreateTokenForSocialLogin');
    });
    Route::group(['prefix' => 'account'], function () {
        Route::post('forgot-password', 'Api\AuthController@ResetPasswordOTP');
        Route::post('update-password', 'Api\AuthController@UpdateForgotPassword');
    });
});
Route::group(['prefix' => 'v3', 'middleware' => ['auth:pelapors-api']], function () {
    Route::group(['prefix' => 'account'], function () {
        Route::get('profile', 'Api\AuthController@pelapor');
        Route::post('update/name', 'Api\AuthController@UpdateName');
        Route::post('update/password', 'Api\AuthController@UpdatePassword');
        Route::post('delete', 'Api\AuthController@DeletePelapor');
    });
    Route::group(['prefix' => 'reference'], function () {
        Route::get('jenis-pelanggaran', 'Api\ReferensiController@getJenisPelanggaran');
        Route::get('jenis-pelanggaran/{uuid}', 'Api\ReferensiController@getSingleJenisPelanggaran');
        Route::get('bentuk-pelanggaran', 'Api\ReferensiController@getBentukPelanggaran');
        Route::get('bentuk-pelanggaran/filter/{uuid}', 'Api\ReferensiController@getBentukByPelanggaran');
        Route::get('bentuk-pelanggaran/{uuid}', 'Api\ReferensiController@getSingleBentukPelanggaran');
        Route::get('kawasan', 'Api\ReferensiController@getKawasan');
        Route::get('kawasan/{uuid}', 'Api\ReferensiController@getSingleKawasan');
    });
    Route::group(['prefix' => 'report'], function () {
        Route::post('send', 'Api\LaporController@lapor');
        Route::get('list', 'Api\LaporController@listLaporan');
        Route::get('detail/{id}', 'Api\LaporController@detailLaporan');
    });
    Route::group(['prefix' => 'content'], function () {
        Route::get('banner', 'Api\ContentController@getBanner');
        Route::get('external-link', 'Api\ExternalLinkController@listLink');
        Route::get('external-link/{id}', 'Api\ExternalLinkController@getOneBlog');
        Route::get('instagram', 'Api\ContentController@getInstagramContent');
        Route::get('static-page', 'Api\ContentController@getStaticPageContent');
        Route::get('website-content', 'Api\ContentController@getWebsiteContent');
        Route::get('website-content/{id}', 'Api\ContentController@getWebsiteContentDetail');
    });
    Route::group(['prefix' => 'notification'], function () {
        Route::post('save-token', 'Api\PushNotificationController@SaveToken');
        Route::post('revoke-token', 'Api\PushNotificationController@RevokeToken');
    });
});
