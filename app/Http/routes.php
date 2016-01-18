<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: X-Authorization");

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {
	Route::get('fetch','FetchMailController@index');
	Route::get('auth', 'GmailController@googleAuth');

	Route::group(['prefix'=>'api/v1'], function(){
		Route::get('/ads', '\App\Http\Controllers\Apis\AdsController@all');
		Route::get('/ads/{message_number}', '\App\Http\Controllers\Apis\AdsController@show');
		Route::get('/ads/after/{message_number}', '\App\Http\Controllers\Apis\AdsController@after');
	});
	Route::get('test', function(){
		App\Models\Ad::truncate();
		return App\Models\Ad::all();
	});
});
