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
    return view('form');
});

Route::get('products', [
	'uses' 	=> 'ProductController@index',
	'as'	=> 'products.all'
]);

Route::post('products', [
	'uses' 	=> 'ProductController@store',
	'as'	=> 'products.store'
]);
