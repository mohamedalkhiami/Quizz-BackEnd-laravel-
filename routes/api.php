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

Route::post('auth', 'ApiController@authenticate');
Route::post('register', 'ApiController@register');
Route::post('logout', 'ApiController@logout');

// Receive token from mobile
Route::post('fcm/token', 'ApiController@saveFCMToken');

Route::group(['middleware' => ['auth.jwt']], function() {
	// Authenticated routes go here!
	Route::get('/test', function() {
		return "Hello World!";
	});

	// Auth User
	Route::get('me', 'ApiController@getAuthenticatedUser');

	// Dashboard API
	Route::get('dashboard', 'ApiController@dashboard');

	// Start Quiz 
	Route::get('quiz/start/{id}', 'ApiController@startQuiz');

	// My Quizes
	Route::get('quiz/myquizes', 'ApiController@myQuizes');

	// Attempt Quiz
	Route::post('quiz/attempt/{id}/', 'ApiController@attempt');

	// Get User Attempts
	Route::get('quiz/{id}/attempts', 'ApiController@getAttempts');

	// Change Password
	Route::post('settings/changepassword', 'ApiController@changepassword');

	// Full Quiz API
	Route::get('quiz/full/{id}', 'ApiController@fullQuiz');

	Route::get('quiz/{id}/seen', 'ApiController@seenResults');

});
