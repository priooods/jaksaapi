<?php

use App\Http\Controllers\UsersController;
use App\Http\Controllers\PerkaraController;
use App\Http\Controllers\ATKController;
use App\Http\Controllers\PembayaranController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => 'api',
    'prefix' => 'v1'
], function ($router) {
    //
    Route::post('login', [UsersController::class, 'login']);
    Route::post('register', [UsersController::class, 'register']);
    Route::post('me', [UsersController::class, 'me']);
    Route::post('all', [UsersController::class, 'all']);
    Route::post('findall', [UsersController::class, 'findall']);
    Route::post('logout', [UsersController::class, 'logout']);
    Route::post('update', [UsersController::class, 'update']);
    Route::post('delete', [UsersController::class, 'delete']);
    Route::get('show',[UsersController::class, 'show']);
    
    Route::post('atk/add',[ATKController::class, 'add']);
    Route::post('atk/get',[ATKController::class, 'get']);
    Route::post('atk/update',[ATKController::class,'update']);
    Route::delete('atk/delete',[ATKController::class,'delete']);
    Route::get('atk/show',[ATKController::class, 'show']);

    Route::post('atk/req',[ATKController::class,'request']);
    Route::get('atk/req/my',[ATKController::class,'my_request']);
    Route::get('atk/req/show',[ATKController::class,'show_request']);
    Route::delete('atk/req/delete',[ATKController::class,'delete_request']);

    Route::get('atk/ppk',[ATKController::class,'ppk_notif']);
    Route::post('atk/ppk/acc',[ATKController::class,'ppk_acc']);
    Route::get('atk/ppk/show',[ATKController::class,'ppk_show']);
    Route::get('atk/log',[ATKController::class,'log_notif']);
    Route::post('atk/log/acc',[ATKController::class,'log_acc']);
    Route::get('atk/log/show',[ATKController::class,'log_show']);
    Route::get('atk/pp',[ATKController::class,'pp_notif']);
    Route::post('atk/pp/acc',[ATKController::class,'pp_acc']);
    
    // PERKARA CRUD
    Route::get('perkara/all',[PerkaraController::class,'all']);
    Route::post('perkara/create',[PerkaraController::class,'create']);
    Route::post('perkara/update',[PerkaraController::class,'update']);
    Route::post('perkara/delete',[PerkaraController::class,'delete']);
    // PROSES PERKARA
    Route::post('perkara/proses',[PerkaraController::class,'pp_input']);
    Route::get('perkara/proses/show',[PerkaraController::class,'pp_show']);
    Route::delete('perkara/proses/delete',[PerkaraController::class,'pp_delete']);
    Route::post('perkara/proses/update',[PerkaraController::class,'pp_update']);
    
    Route::post('perkara/pp',[PerkaraController::class,'pp_perkara']); // Panitera Pengganti List Perkara
    Route::post('perkara/jurusita',[PerkaraController::class,'jurusita_perkara']); // Jurusita List Perkara

    Route::post('tugas/create',[PerkaraController::class,'panmud_surat']);
    Route::post('tugas/update',[PerkaraController::class,'update_surat']);
    Route::post('tugas/delete',[PerkaraController::class,'delete_surat']);
    Route::post('tugas/all',[PerkaraController::class,'all_surat']);

    Route::get('tugas/jurusita',[PerkaraController::class,'jurusita_notif']);
    Route::post('tugas/jurusita/all',[PerkaraController::class,'jurusita_all']);
    Route::post('tugas/bukti',[PerkaraController::class,'jurusita_surat']);
    Route::post('tugas/acc',[PerkaraController::class,'acc_surat']);
    Route::post('tugas/ppk',[PerkaraController::class,'ppk_notif']);
    Route::get('tugas/ppk/all',[PerkaraController::class,'ppk_surat']);

    // PEMBAYARAN CRUD
    Route::get('bayar/show',[PembayaranController::class,'show']);
    Route::post('bayar/create',[PembayaranController::class,'create']);
    Route::post('bayar/update',[PembayaranController::class,'update']);
    Route::delete('bayar/delete',[PembayaranController::class,'delete']);
    Route::get('bayar/notif',[PembayaranController::class,'bayar_notif']);
    Route::post('bayar/acc',[PembayaranController::class,'kuitansi']);
});
