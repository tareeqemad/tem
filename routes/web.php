<?php

use Illuminate\Support\Facades\Route;

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
Route::get('change-language/{locale}', ['as' => 'frontend_change_locale', 'uses' => 'GeneralController@changeLanguage']);
Route::get('invoice/print/{id}', ['as' => 'invoice.print', 'uses' => 'InvoiceController@print']);
Route::get('invoice/pdf/{id}', ['as' => 'invoice.pdf', 'uses' => 'InvoiceController@pdf']);

Route::get('/', 'InvoiceController@index')->name('index');
Route::resource('invoice', 'InvoiceController');
Route::get('/list_pdf', 'InvoiceController@list_pdf')->name('list_pdf');
