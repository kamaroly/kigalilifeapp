<?php

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

Route::get('fetch', 'FetchMailController@index');

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
	Route::get('fetch', function(){
		//Your gmail email address and password
		$username = env('KIGALILIFE_USERNAME');
		$password = env('KIGALILIFE_PASSWORD');

		$fetcher = new App\Factories\MailFetcher($username,$password);
		$fetcher->attachmentPath = "imap-dump";

		$found_mails = $fetcher->get();
		$foundMail = null;
		foreach ($found_mails as $key => $value) {
			$foundMail = $value;
		}
		
		dd($foundMail->body);
	});
});
