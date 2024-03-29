<?php

use App\Http\Middleware\CheckIsAdmin;
use App\Http\Middleware\CheckIsAuth;
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

Route::name('users.')->prefix('users')->middleware(CheckIsAdmin::class)->group(function () {
    Route::prefix('{user}')->group(function () {
        Route::patch('set_is_admin', 'User\AdminUserController@setIsAdmin')->name('set_is_admin');
        Route::delete('destroy', 'User\AdminUserController@destroy')->name('destroy');
    });
});

Route::name('users.')->prefix('users')->middleware(CheckIsAuth::class)->group(function () {
    Route::patch('self_update', 'User\ClientUserController@selfUpdate')->name('self_update');
    Route::delete('self_destroy', 'User\ClientUserController@selfDestroy')->name('self_destroy');
});

Route::name('users.')->prefix('users')->group(function () {
    Route::get('cities', 'User\ClientUserController@getCities')->name('cities');
    Route::get('countries', 'User\ClientUserController@getCountries')->name('countries');
    Route::get('index', 'User\ClientUserController@index')->name('index');

    Route::prefix('{user}')->group(function () {
        Route::get('show', 'User\ClientUserController@show')->name('show');
    });
});
