<?php

use Illuminate\Http\Request;

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

// Authentication
Route::post('register', 'Auth\RegisterController@register');
Route::post('login', [ 'as' => 'login', 'uses' => 'Auth\LoginController@login']);
Route::post('logout', 'Auth\LoginController@logout');

Route::group(['middleware' => 'auth:api'], function() {
    // Logged in user's data
    Route::get('user', function (Request $request) {
        return response()->json(['data' => $request->user()->toArray(),]);
    });

    // Job routes
    Route::get('job', 'JobController@index');
    Route::get('job/{id}', 'JobController@show');
    Route::post('job', 'JobController@store');
    // Route::delete('job/{id}', 'JobController@delete');
});
