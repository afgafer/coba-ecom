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

Route::get('/','FrontController@index')->name('front.index');
Route::get('/product','FrontController@product')->name('front.product');

Auth::routes();

Route::group(['prefix'=>'admin','middleware'=>'auth'],function(){
    Route::get('/home', 'HomeController@index')->name('home');
    Route::resource('category','CategoryController')->except(['create','show']);
    Route::resource('product','ProductController')->except(['show']);
    Route::get('product/bulk','ProductController@massUploadForm')->name('produk.bulk');
    Route::post('/product/bulk', 'ProductController@massUpload')->name('product.saveBulk');
});
