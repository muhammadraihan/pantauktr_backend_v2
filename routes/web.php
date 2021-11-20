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
    if (Auth::check()) {
        return redirect()->route('backoffice.dashboard');
    }
    return view('welcome');
});

Auth::routes(['register' => false]);

Route::group(['prefix' => 'backoffice', 'middleware' => ['auth']], function () {
    Route::get('/', 'DashboardController@index');
    Route::get('dashboard', 'DashboardController@dashboard')->name('backoffice.dashboard');
    Route::get('logs', 'ActivityController@index')->name('logs');
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
    Route::resource('static-page', 'StaticPageController');
    Route::resource('tindak-lanjut', 'TindakLanjutController');
    Route::resource('users', 'UserController');
    Route::resource('website', 'WebsiteController');
    Route::get('profile', 'UserController@profile')->name('profile');
    Route::patch('profile/{user}/update', 'UserController@ProfileUpdate')->name('profile.update');
    Route::patch('profile/{user}/password', 'UserController@ChangePassword')->name('profile.password');
    Route::get('filter-chart', 'ChartController@filter')->name('get.filter-chart');
    Route::get('filter', 'LaporanController@filter')->name('get.filter');
    Route::get('cetak-pdf-pelanggaran', 'LaporanController@cetakpelanggaran')->name('cetak.laporan_pelanggaran');
    Route::get('cetak-pdf-apresiasi', 'LaporanController@cetakapresiasi')->name('cetak.laporan_apresiasi');
    Route::get('get-bentuk-panggaran', 'BentukPelanggaranController@getBentukPelanggaranByJenis')->name('get.bentuk');
    Route::get('get-kawasan', 'KawasanController@getKawasan')->name('get.kawasan');
});
