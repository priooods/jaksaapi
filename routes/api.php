<?php

use App\Http\Controllers\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => 'api',
    'prefix' => 'v1'
], function ($router) {
    Route::post('login', [UsersController::class, 'login']);
    Route::post('register', [UsersController::class, 'register']);
    Route::post('me', [UsersController::class, 'me']);
    Route::post('all', [UsersController::class, 'all']);
    Route::post('logout', [UsersController::class, 'logout']);
    Route::post('update', [UsersController::class, 'update']);
    Route::post('delete', [UsersController::class, 'delete']);
});
