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

Route::name('users.')->prefix('users')->group(function () {
    Route::prefix('{user}')->group(function () {
        Route::patch('set_is_admin', 'User\AdminUserController@setIsAdmin')->name('set_is_admin');
    });
});

Route::apiResource('users', 'User\ClientUserController');

