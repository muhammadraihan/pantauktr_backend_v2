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
    Route::resource('users', 'UserController');
    Route::resource('permissions', 'PermissionController');
    Route::resource('roles', 'RoleController');
    Route::resource('pelapor', 'PelaporController');
    Route::resource('pelanggaran', 'PelanggaranController');
    Route::resource('operator_type', 'Operator_typeController');
    Route::resource('jenis_laporan', 'Jenis_LaporanController');
    Route::resource('jenis_apresiasi', 'Jenis_ApresiasiController');
    Route::resource('kota', 'KotaController');
    Route::resource('province', 'ProvinceController');
    Route::resource('laporan', 'LaporanController');
    Route::resource('operator', 'OperatorController');
    Route::resource('external_link', 'ExternalController');
    Route::resource('chart', 'ChartController');
    Route::resource('bentuk_pelanggaran','BentukPelanggaranController');
    Route::resource('bentuk_apresiasi','BentukApresiasiController');
    Route::resource('kawasan','KawasanController');
    Route::resource('banner','BannerController');
    Route::resource('dynamic_menu','DynamicMenuController');
    Route::get('filters', 'ChartController@filter')->name('get.filters');
    Route::get('filter', 'LaporanController@filter')->name('get.filter');
    Route::get('cetak-pdf-pelanggaran', 'LaporanController@cetakpelanggaran')->name('cetak.laporan_pelanggaran');
    Route::get('cetak-pdf-apresiasi', 'LaporanController@cetakapresiasi')->name('cetak.laporan_apresiasi');
    Route::get('tindak_lanjut/{id}', 'LaporanController@tindaklanjut')->name('tindaklanjut.index');
    Route::get('tindak_lanjut_notification', 'LaporanController@sendNotifToAndroid')->name('tindaklanjut.notif');
    Route::post('tindak_lanjut', 'LaporanController@storetindaklanjut')->name('tindaklanjut.store');


    // user Profile
    Route::get('profile', 'UserController@profile')->name('profile');
    Route::patch('profile/{user}/update', 'UserController@ProfileUpdate')->name('profile.update');
    Route::patch('profile/{user}/password', 'UserController@ChangePassword')->name('profile.password');
});
