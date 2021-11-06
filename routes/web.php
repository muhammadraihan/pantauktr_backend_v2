<?php

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

Route::get('/', function () {
    // check if user is auth then redirect to dashboard page
    if (Auth::check()) {
        return redirect()->route('backoffice.dashboard');
    }
    return view('welcome');
});

Auth::routes(['register' => false]);

Route::group(['prefix' => 'backoffice', 'middleware' => ['auth']], function () {
    // backoffice route
    Route::get('/', 'DashboardController@index');
    Route::get('dashboard', 'DashboardController@dashboard')->name('backoffice.dashboard');
    Route::get('logs', 'ActivityController@index')->name('logs');
    // resource
    Route::resource('banner', 'BannerController');
    Route::resource('bentuk-pelanggaran', 'BentukPelanggaranController');
    Route::resource('chart', 'ChartController');
    Route::resource('external-link', 'ExternalController');
    Route::resource('instagram', 'InstagramController');
    Route::resource('kawasan', 'KawasanController');
    Route::resource('kota', 'KotaController');
    Route::resource('laporan', 'LaporanController');
    Route::resource('operator', 'OperatorController');
    Route::resource('operator-type', 'Operator_typeController');
    Route::resource('pelanggaran', 'PelanggaranController');
    Route::resource('pelapor', 'PelaporController');
    Route::resource('permissions', 'PermissionController');
    Route::resource('province', 'ProvinceController');
    Route::resource('roles', 'RoleController');
    Route::resource('users', 'UserController');
    Route::resource('static-page', 'StaticPageController');
    Route::resource('website', 'WebsiteController');
    // user Profile
    Route::get('profile', 'UserController@profile')->name('profile');
    Route::patch('profile/{user}/update', 'UserController@ProfileUpdate')->name('profile.update');
    Route::patch('profile/{user}/password', 'UserController@ChangePassword')->name('profile.password');
    // tindak lanjut
    Route::post('tindak-lanjut', 'LaporanController@storetindaklanjut')->name('tindaklanjut.store');
    Route::get('tindak-lanjut/{id}', 'LaporanController@tindaklanjut')->name('tindaklanjut.index');
    Route::get('tindak-lanjut-notification', 'LaporanController@sendNotifToAndroid')->name('tindaklanjut.notif');
    // filter
    Route::get('filter-chart', 'ChartController@filter')->name('get.filter-chart');
    Route::get('filter', 'LaporanController@filter')->name('get.filter');
    // cetak
    Route::get('cetak-pdf-pelanggaran', 'LaporanController@cetakpelanggaran')->name('cetak.laporan_pelanggaran');
    Route::get('cetak-pdf-apresiasi', 'LaporanController@cetakapresiasi')->name('cetak.laporan_apresiasi');
    // reference
    Route::get('get-bentuk-panggaran', 'BentukPelanggaranController@getBentukPelanggaranByJenis')->name('get.bentuk');
    Route::get('get-kawasan', 'KawasanController@getKawasan')->name('get.kawasan');
});
