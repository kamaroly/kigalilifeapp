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
		/** REGISTRATION ROUTES */
		Route::get('register', '\App\Http\Controllers\Apis\AuthController@register');
		Route::get('verifyotp', '\App\Http\Controllers\Apis\AuthController@verifyotp');


		/** FETCHING ADS ROUTES */
		Route::get('/ads', '\App\Http\Controllers\Apis\AdsController@all');
		Route::get('/ads/{message_number}', '\App\Http\Controllers\Apis\AdsController@show');
		Route::get('/ads/after/{message_number}', '\App\Http\Controllers\Apis\AdsController@after');

	});

	Route::get('test', function(){
		$ad = App\Models\Ad::find(17);
		$html = 'Delivered-To: gerageza@gmail.com\r\nReceived: by 10.140.19.163 with SMTP id 32csp3082348qgh;\r\n        Tue, 19 Jan 2016 23:40:39 -0800 (PST)\r\nX-Received: by 10.140.18.114 with SMTP id 105mr43429433qge.41.1453275639569;\r\n        Tue, 19 Jan 2016 23:40:39 -0800 (PST)\r\nReturn-Path: \r\nReceived: from ng15.bullet.mail.bf1.yahoo.com (ng15.bullet.mail.bf1.yahoo.com. [98.139.164.110])\r\n        by mx.google.com with ESMTPS id h33si42363303qge.30.2016.01.19.23.40.39\r\n        for \r\n        (version=TLS1 cipher';
		preg_match_all('/Delivered\-To(.*?)\(version\=TLS1/s', $html,$email);
		dd($email);
	});
});
