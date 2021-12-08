<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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



Route::post('/register', 'App\Http\Controllers\UserController@register')->name('/register/'); 
Route::post('/login', 'App\Http\Controllers\UserController@login')->name('/login/'); 

Route::middleware('auth:sanctum')->group(function(){    
// User managment
  Route::put('/update/{id}', 'App\Http\Controllers\UserController@update')->name('/update/'); 
  Route::delete('/delete/{id}', 'App\Http\Controllers\UserController@delete')->name('/delete/'); 
Route::get('/list', 'App\Http\Controllers\UserController@list')->name('/list/'); 
Route::get('/listSingleUser/{id}', 'App\Http\Controllers\UserController@listSingleUser')->name('/listSingleUser/'); 
Route::post('/userBookListing', 'App\Http\Controllers\UserController@userBookListing')->name('/userBookListing/');
Route::post('/userBookListingNotReturnedBooks', 'App\Http\Controllers\UserController@userBookListingNotReturnedBooks')->name('/userBookListingNotReturnedBooks/');

// Book managment

Route::post('/createBooks', 'App\Http\Controllers\BookController@createBooks')->name('/createBooks/'); 
Route::put('/updateBook/{id}', 'App\Http\Controllers\BookController@updateBook')->name('/updateBook/'); 
Route::get('/listBooks', 'App\Http\Controllers\BookController@listBooks')->name('/listBooks/'); 
Route::get('/listSingleBook/{id}', 'App\Http\Controllers\BookController@listSingleBook')->name('/listSingleBook/'); 
Route::delete('/deleteBook/{id}', 'App\Http\Controllers\BookController@deleteBooks')->name('/deleteBook/'); 


// Library managment
Route::post('/bookRentedByUser', 'App\Http\Controllers\BooksUserRentedController@bookRentedByUser')->name('/bookRentedByUser/'); 
Route::post('/bookReturnedByUser', 'App\Http\Controllers\BooksUserRentedController@bookReturnedByUser')->name('/bookReturnedByUser/');
Route::post('/listAllBookRentedByUser', 'App\Http\Controllers\BooksUserRentedController@listAllBookRentedByUser')->name('/listAllBookRentedByUser/');

 

});
