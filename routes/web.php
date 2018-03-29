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

Route::get('/', 'HomeController@index')->name('home');

Auth::routes();

Route::get('/upload', 'UploadController@index')->name('upload');
Route::post('/upload', 'UploadController@processUpload')->name('processUpload');
Route::post('/postForm', 'UploadController@postForm')->name('postForm');
