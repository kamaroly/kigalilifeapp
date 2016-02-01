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
	$input = "<p><div dir=\"ltr\"><div class=\"gmail_default\" style=\"font-family:trebuchet ms,sans-serif;color:rgb(68,68,68);\">Hi Icon Media,<br><br></div><div class=\"gmail_default\" style=\"font-family:trebuchet ms,sans-serif;color:rgb(68,68,68);\">Please contact the below address,<br><br><b><span style=\"font-size:8.5pt;font-family:&quot;Calibri&quot;,sans-serif;color:rgb(13,24,251);\" lang=\"EN-GB\">PANKAJ PATEL</span></b><span style=\"font-size:11pt;font-family:&quot;Calibri&quot;,sans-serif;color:rgb(13,24,251);\" lang=\"EN-GB\">| </span><b><span style=\"font-size:8pt;font-family:&quot;Calibri&quot;,sans-serif;color:rgb(13,24,251);\" lang=\"EN-GB\">EXPORTS MANAGER</span></b><span style=\"font-size:9pt;font-family:&quot;Calibri&quot;,sans-serif;color:rgb(13,24,251);\" lang=\"EN-GB\"> |<span class>Ramco</span> <span class>Printing</span> Works Ltd  </span><span style=\"font-size:8pt;font-family:&quot;Tahoma&quot;,sans-serif;color:rgb(13,24,251);\" lang=\"EN-GB\">|</span><span style=\"font-size:11pt;font-family:&quot;Calibri&quot;,sans-serif;color:rgb(13,24,251);\" lang=\"EN-GB\"> </span><b><span style=\"font-size:9pt;font-family:&quot;Calibri&quot;,sans-serif;color:rgb(13,24,251);\" lang=\"EN-GB\">Unit 2</span></b><span style=\"font-size:9pt;font-family:&quot;Calibri&quot;,sans-serif;color:rgb(13,24,251);\" lang=\"EN-GB\">, </span><span style=\"font-size:10pt;font-family:&quot;Calibri&quot;,sans-serif;color:rgb(13,24,251);\" lang=\"EN-GB\"><span class>ramco</span> group industrial park, Mombasa Road</span><span style=\"font-size:8pt;font-family:&quot;Calibri&quot;,sans-serif;color:rgb(13,24,251);\" lang=\"EN-GB\">  </span><span style=\"font-size:9pt;font-family:&quot;Calibri&quot;,sans-serif;color:rgb(13,24,251);\" lang=\"EN-GB\">|  </span><b><span style=\"font-size:10pt;font-family:Wingdings;color:rgb(13,24,251);\" lang=\"EN-GB\">*</span></b><span style=\"font-size:9pt;font-family:&quot;Calibri&quot;,sans-serif;color:rgb(13,24,251);\" lang=\"EN-GB\">: 27750 - 00506, Nairobi, Kenya  | </span><span style=\"font-size:10pt;font-family:Wingdings;color:rgb(13,24,251);\" lang=\"EN-GB\">(</span><span style=\"font-size:7.5pt;font-family:com;color:rgb(13,24,251);\" lang=\"EN-GB\">:</span><span style=\"font-size:9pt;font-family:&quot;Calibri&quot;,sans-serif;color:rgb(13,24,251);\" lang=\"EN-GB\"> </span><span style=\"font-size:8pt;font-family:&quot;Calibri&quot;,sans-serif;color:rgb(13,24,251);\" lang=\"EN-GB\"><a href=\"tel:%2B254%20020%202502301\" value=\"+254202502301\" target=\"_blank\">&#43;254 020 2502301</a> - 8</span><span style=\"font-size:11pt;font-family:&quot;Calibri&quot;,sans-serif;color:rgb(13,24,251);\" lang=\"EN-GB\">  </span><span style=\"font-size:9pt;font-family:&quot;Calibri&quot;,sans-serif;color:rgb(13,24,251);\" lang=\"EN-GB\">| </span><span style=\"font-size:10pt;font-family:Wingdings;color:rgb(13,24,251);\" lang=\"EN-GB\">(</span><b><span style=\"font-size:9pt;font-family:&quot;Calibri&quot;,sans-serif;color:rgb(13,24,251);\" lang=\"EN-GB\">Fax</span></b><span style=\"font-size:9pt;font-family:&quot;Calibri&quot;,sans-serif;color:rgb(13,24,251);\" lang=\"EN-GB\"> : </span><span style=\"font-size:8pt;font-family:&quot;Calibri&quot;,sans-serif;color:rgb(13,24,251);\" lang=\"EN-GB\">&#43;254 020 535 163 , 535 267 </span><span style=\"font-size:9pt;font-family:&quot;Calibri&quot;,sans-serif;color:rgb(13,24,251);\" lang=\"EN-GB\">| </span><span style=\"font-size:10pt;font-family:Wingdings;color:rgb(13,24,251);\" lang=\"EN-GB\">(</span><span style=\"font-size:9pt;font-family:&quot;Calibri&quot;,sans-serif;color:rgb(13,24,251);\" lang=\"EN-GB\">Mobile &#43;2547 04 656 513   </span><span style=\"font-size:10pt;font-family:&quot;Calibri&quot;,sans-serif;color:rgb(13,24,251);\" lang=\"EN-GB\">| </span><b><span style=\"font-size:10pt;font-family:Wingdings;color:rgb(13,24,251);\" lang=\"EN-GB\">*</span></b><span style=\"font-size:10pt;font-family:&quot;Calibri&quot;,sans-serif;color:rgb(13,24,251);\" lang=\"EN-GB\"> </span><span style=\"font-size:10pt;font-family:&quot;Calibri&quot;,sans-serif;color:blue;\" lang=\"EN-GB\"><a href=\"mailto:exports@ramcoprinting.com\" target=\"_blank\">exports@ramcoprinting.com</a></span><span style=\"font-size:8pt;font-family:&quot;Calibri&quot;,sans-serif;color:rgb(13,24,251);\" lang=\"EN-GB\"> </span><span style=\"font-size:9pt;font-family:&quot;Calibri&quot;,sans-serif;color:rgb(13,24,251);\" lang=\"EN-GB\">| <a href=\"http://www.ramco-group.com/\" target=\"_blank\">www.<span class>ramco</span>-group.com</a><br></span></div><div class=\"gmail_default\" style=\"font-family:trebuchet ms,sans-serif;color:rgb(68,68,68);\"><br></div><div class=\"gmail_default\" style=\"font-family:trebuchet ms,sans-serif;color:rgb(68,68,68);\">He will give you the best quality.<br><br></div><div class=\"gmail_default\" style=\"font-family:trebuchet ms,sans-serif;color:rgb(68,68,68);\">Regards,<br></div><div class=\"gmail_default\" style=\"font-family:trebuchet ms,sans-serif;color:rgb(68,68,68);\"><span style=\"font-size:9pt;font-family:&quot;Calibri&quot;,sans-serif;color:rgb(13,24,251);\" lang=\"EN-GB\"><br></span></div></div><div class=\"gmail_extra\"><br clear=\"all\"><div><div class=\"gmail_signature\"><div dir=\"ltr\"><div><div dir=\"ltr\"><div><div dir=\"ltr\"><div><span style=\"color:rgb(102,102,102);\"><span style=\"font-family:trebuchet ms,sans-serif;\">KIMANI JAMES<br><br>T:  &#43;257 76 111 333 / &#43;257 75 425 130 / &#43;254 722 981 403 / &#43;8613113391632  / QQ. 2991209778<br>Skype: kimani.jimmy<br>URL: <a href=\"http://www.egm-advertising.com\" target=\"_blank\">www.egm-advertising.com</a></span><br></span><span style=\"font-family:trebuchet ms,sans-serif;\"><br><b><span style=\"color:rgb(255,0,0);\">DISCLAIMER</span></b></span><br><br><span style=\"color:rgb(61,133,198);\"><span style=\"font-family:trebuchet ms,sans-serif;\"><br>The contents of this electronic mail message and any attachments are <br>confidential and may be legally privileged and protected from discovery or <br>disclosure. This message is intended only for the personal and confidential <br>use of the intended addressee. If you have received this message in error, <br>you are not authorized to view, disseminate, distribute or copy this <br>message or any part of it without my consent, and you are requested to <br>return it to the sender immediately and delete all copies from your system. <br>I cannot guarantee that this message or any attachment is virus free, does <br>not contain malicious code or is incompatible with your electronic system <br>and does not accept liability in respect of viruses, malicious code or any <br>related problems that you might experience.<br></span></span><br><br><br><br><br><br><br><br><br><br><br></div></div></div></div></div></div></div></div>  <br><div class=\"gmail_quote\">On Sun, Jan 31, 2016 at 2:59 PM, <a href=\"mailto:izmaali15@yahoo.com\">izmaali15@yahoo.com</a> [kigalilife] <span dir=\"ltr\">&lt;<a href=\"mailto:kigalilife@yahoogroups.com\" target=\"_blank\">kigalilife@yahoogroups.com</a>&gt;</span> wrote:<br><blockquote class=\"gmail_quote\" style=\"border-left:1px #ccc solid;\">          <u></u>                                         <div style=\"background-color:#fff;\">  <span> </span>          <div>    <div>              <div>                        <p>contact : shades &amp; color </p><div>0731427675</div><p></p>          </div>                       <div style=\"color:#fff;min-height:0;\"></div>          </div>                                          </div></div></blockquote></div><br></div>  </p>";

	return ;

	});
});
