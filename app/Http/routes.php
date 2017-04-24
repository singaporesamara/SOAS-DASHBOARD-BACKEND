<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: false');
header('Access-Control-Allow-Headers: Authorization, Content-Type, X-Requested-With');

Route::get('/',								function(){return view('welcome');});

Route::post('/auth/sign-up/',				'AuthController@signup');
Route::post('/auth/sign-in/',				'AuthController@signin');
Route::post('/auth/forgot/',				'AuthController@forgot');
Route::post('/auth/set-password/',			'AuthController@setPassword');

Route::post('/profile/',					'ProfileController@profile');
Route::get(	'/profile/news/',				'ProfileController@news');	
Route::post('/profile/registration-form/',	'ProfileController@registrationForm');
Route::post('/profile/check/',				'ProfileController@check');

Route::post('/transaction/create/',			'TransactionsController@create');
Route::post('/transaction/topup/',			'TransactionsController@topup');