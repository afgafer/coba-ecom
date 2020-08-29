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
Route::get('/products','FrontController@products')->name('front.products');
Route::get('/product/{slug}', 'FrontController@product')->name('front.product');
Route::get('/category/{slug}', 'FrontController@categoryProduct')->name('front.category');
Route::post('cart', 'CartController@add')->name('cart.add');
Route::get('/cart', 'CartController@index')->name('cart.index');
Route::post('/cart/update', 'CartController@update')->name('cart.update');
Route::get('checkout','CartController@checkout')->name('cart.checkout');
Route::post('checkout','CartController@processCheckout')->name('cart.store');
Route::get('checkout/{invoice}','CartController@checkoutFinish')->name('cart.finish');

Auth::routes();

Route::group(['prefix'=>'admin','middleware'=>'auth'],function(){
    Route::get('/home', 'HomeController@index')->name('home');
    Route::resource('category','CategoryController')->except(['create','product']);
    Route::resource('product','ProductController')->except(['show']);
    Route::get('product/bulk','ProductController@massUploadForm')->name('product.bulk');
    Route::post('/product/bulk', 'ProductController@massUpload')->name('product.saveBulk');
});

Route::group(['prefix'=>'member'], function(){
    Route::get('login','LoginController@loginV')->name('customer.login');
    Route::post('login', 'LoginController@login')->name('customer.post_login');
});

Route::group(['middleware'=>'customer'], function(){
    Route::get('dashboard', 'LoginController@dashboard')->name('customer.dashboard');
    Route::get('logout', 'LoginController@logout')->name('customer.logout');
});

