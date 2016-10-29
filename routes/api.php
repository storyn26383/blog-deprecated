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

Route::group([
    'middleware' => 'auth:api',
    'namespace' => 'Api\V1',
    'prefix' => 'v1',
], function ($router) {
    Route::post('category', 'CategoryController@store');
    Route::get('category/{category}', 'CategoryController@show');
    Route::put('category/{category}', 'CategoryController@update');
    Route::delete('category/{category}', 'CategoryController@destroy');
    Route::get('categories', 'CategoryController@index');
});
