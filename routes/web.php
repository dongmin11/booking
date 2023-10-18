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

Route::get('/', function () {
    return view('welcome');
});

Route::group(['middleware' => ['web']], function () {
    Route::get('/booklist', 'App\Http\Controllers\BooklistController@booklist')->name('booklist');

    Route::get('/search', 'App\Http\Controllers\BooklistController@search')->name('search');

    Route::get('/add', 'App\Http\Controllers\BooklistController@add')->name('add');

    Route::post('/add', 'App\Http\Controllers\BooklistController@create')->name('create');

    Route::get('/edit', 'App\Http\Controllers\BooklistController@edit')->name('edit');

    Route::post('/edit', 'App\Http\Controllers\BooklistController@update')->name('update');

    Route::get('/lendInfo', 'App\Http\Controllers\BooklistController@lendInfo')->name('lendInfo');

    Route::post('/lendInfo', 'App\Http\Controllers\BooklistController@lend')->name('lend');

    Route::get('/detail', 'App\Http\Controllers\BooklistController@detail')->name('detail');

    route::get('return', 'App\Http\Controllers\BooklistController@return')->name('return');

    Route::get('/editLendInfo', 'App\Http\Controllers\BooklistController@editLendInfo')->name('editLendInfo');

    Route::post('/updateLendInfo', 'App\Http\Controllers\BooklistController@updateLendInfo')->name('updateLendInfo');

    Route::get('/login', 'App\Http\Controllers\UserLoginController@login')->name('login');

    Route::post('/login', 'App\Http\Controllers\UserLoginController@UserLogin')->name('UserLogin');

    Route::post('/logout', 'App\Http\Controllers\UserLoginController@logout')->name('logout');

    Route::get('/register', 'App\Http\Controllers\UserLoginController@regist')->name('register');

    Route::post('/register', 'App\Http\Controllers\UserLoginController@UserRegister')->name('UserRegister');

    route::get('practice','App\Http\Controllers\BooklistController@practice')->name('practice');

    route::get('bookLendHistory','App\Http\Controllers\BooklistController@bookLendHistory')->name('bookLendHistory');

    route::get('userLendHistory','App\Http\Controllers\BooklistController@userLendHistory')->name('userLendHistory');

    route::post('update-favorite','App\Http\Controllers\BooklistController@updateFavorite')->withoutMiddleware(['web']);
});







// Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
