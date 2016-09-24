<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::post('auth/login', 'Web\AuthController@login');
Route::get('auth/logout', 'Web\AuthController@logout');
Route::post('/company', 'Web\CompaniesController@byDomain');

Route::get('/{vuePage}', function () {
    return view('welcome');
})->where(['vuePage' => '(?!api).*']);
