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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('transactions','TransactionController@index');
Route::get('customers', 'CustomerController@index');
Route::get('search/{val}','TransactionController@search');
Route::delete('transaction/{id}','TransactionController@destroy');
// insert or update
Route::post('transaction','TransactionController@store'); // insert
Route::put('transaction','TransactionController@store');  // update
Route::post('customer', 'CustomerController@store'); // insert new customer