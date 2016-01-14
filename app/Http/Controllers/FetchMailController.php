<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Factories\MailFetcher;

class FetchMailController extends Controller
{
    public function index()
    {
				
		//Your gmail email address and password
		$username = env('KIGALILIFE_USERNAME');
		$password = env('KIGALILIFE_PASSWORD');
		//Select messagestatus as ALL or UNSEEN which is the unread email
        $ToSearch = 'kigalilife';
        $dateToSearch = '"05-JAN-15"';
		$messagestatus = 'SUBJECT "kigalilife" SINCE "1 January 2016"';
		//-------------------------------------------------------------------
		//Gmail host with folder
		$hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
		//Open the connection
    
        $mails = new MailFetcher($username,$password);

        dd($mails->get());
    }
}
