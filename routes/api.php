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

Route::prefix('call')->group(function () {
    Route::post('add', 'CallController@newCall');
    Route::post('done', 'CallController@endOfTheCall');
});

Route::prefix('employee')->group(function () {
    Route::post('want', 'CallController@newCallWant');
    Route::post('add', 'CallController@newEmployee');
});