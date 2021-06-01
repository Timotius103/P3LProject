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

Route::group(['middleware' => 'auth:api'], function() {
    Route::post('logout','Api\AuthController@logout');

    Route::get('karyawan', 'Api\AuthController@index');
    Route::post('karyawan', 'Api\AuthController@store');
    Route::get('karyawan/{id}', 'Api\AuthController@show');
    Route::put('karyawan/{id}', 'Api\AuthController@update');
    Route::delete('karyawan/{id}', 'Api\AuthController@destroy');
    Route::get('waiterSaja','Api\AuthController@waiterFind');
//    Route::put('karyawan/{id}','Api\AuthController@updatepassword');

    Route::get('customer', 'Api\CustomerController@index');
    Route::get('customer/{id}', 'Api\CustomerController@show');
    Route::post('customer', 'Api\CustomerController@store');
    Route::put('customer/{id}', 'Api\CustomerController@update');
    Route::delete('customer/{id}', 'Api\CustomerController@destroy');

    Route::get('meja', 'Api\MejaController@index');
    Route::get('meja/{id}', 'Api\MejaController@show');
    Route::post('meja', 'Api\MejaController@store');
    Route::put('meja/{id}', 'Api\MejaController@update');
    Route::delete('meja/{id}', 'Api\MejaController@destroy');
    Route::get('mejaTersedia','Api\MejaController@cekMejaTersedia');

    Route::get('menu', 'Api\MenuController@index');
    Route::get('menu/{id}', 'Api\MenuController@show');
    Route::post('menu', 'Api\MenuController@store');
    Route::put('menu/{id}', 'Api\MenuController@update');
    Route::delete('menu/{id}', 'Api\MenuController@destroy');
    Route::get('top3','Api\MenuController@top3');
    Route::get('Tersedia','Api\MenuController@showMenuTersedia');

    Route::get('bahan', 'Api\BahanController@index');
    Route::get('bahan/{id}', 'Api\BahanController@show');
    Route::post('bahan', 'Api\BahanController@store');
    Route::put('bahan/{id}', 'Api\BahanController@update');
    Route::delete('bahan/{id}', 'Api\BahanController@destroy');

    Route::get('stokbahan', 'Api\StokBahanController@index');
    Route::get('stokbahan/{id}', 'Api\StokBahanController@show');
    Route::post('stokbahan', 'Api\StokBahanController@store');
    Route::put('stokbahan/{id}', 'Api\StokBahanController@update');
    Route::delete('stokbahan/{id}', 'Api\StokBahanController@destroy');

    Route::get('pesanan', 'Api\PesananController@index');
    Route::get('pesanan/{id}', 'Api\PesananController@show');
    Route::post('pesanan', 'Api\PesananController@store');
    Route::put('pesanan/{id}', 'Api\PesananController@update');
    Route::delete('pesanan/{id}', 'Api\PesananController@destroy');
    Route::post('getpesananbyidreservasi/', 'Api\PesananController@getpesananbyIdReservasi');

    Route::get('detailpesanan', 'Api\DetailPesananController@index');
    Route::get('detailpesanan/{id}', 'Api\DetailPesananController@show');
    Route::post('detailpesanan', 'Api\DetailPesananController@store');
    Route::put('detailpesanan/{id}', 'Api\DetailPesananController@update');
    Route::delete('detailpesanan/{id}', 'Api\DetailPesananController@destroy');
    Route::post('getdetailpesananbyid/', 'Api\DetailPesananController@getpesananbyId');

    Route::get('reservasi', 'Api\ReservasiController@index');
    Route::get('reservasi/{id}', 'Api\ReservasiController@show');
    Route::post('reservasi', 'Api\ReservasiController@store');
    Route::put('reservasi/{id}', 'Api\ReservasiController@update');
    Route::delete('reservasi/{id}', 'Api\ReservasiController@destroy');
    Route::get('reservasiOnGoing','Api\ReservasiController@tampilOnGoing');
    Route::post('getreservasionbyid/', 'Api\ReservasiController@getreservasibyid');

    Route::get('history', 'Api\HistoryController@index');
    Route::get('history/{id}', 'Api\HistoryController@show');
    Route::post('history', 'Api\HistoryController@store');
    Route::put('history/{id}', 'Api\HistoryController@update');
    Route::delete('history/{id}', 'Api\HistoryController@destroy');
    Route::get('getlaporanpengeluaranbulanan/{tahun}','Api\HistoryController@laporanPengeluaranBulanan');
    Route::get('getlaporanpengeluarantahunan/{awal}/{akhir}','Api\HistoryController@laporanPengeluaranTahunan');
    Route::get('getlaporanpendapatanbulanan/{tahun}','Api\HistoryController@laporanPendapatanBulanan');
    Route::get('getlaporanpendapatantahunan/{awal}/{akhir}','Api\HistoryController@laporanPendapatanTahunan');

    Route::get('transaksi', 'Api\TransaksiController@index');
    Route::get('transaksi/{id}', 'Api\TransaksiController@show');
    Route::post('transaksi', 'Api\TransaksiController@store');
    Route::put('transaksi/{id}', 'Api\TransaksiController@update');
    Route::put('updatedipembayaran/{id}', 'Api\TransaksiController@updatedipembayaran');
    Route::delete('transaksi/{id}', 'Api\TransaksiController@destroy');
    Route::post('gettransaksibyreservasi/', 'Api\TransaksiController@showtransaksibyreservasi');
    Route::post('gettransaksibyid/', 'Api\TransaksiController@gettransaksibyid');

});

Route::post('login', 'Api\AuthController@login');


