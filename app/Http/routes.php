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
    //
    Route::get('/test', function(){
    	/**
 *	Gmail attachment extractor.
 *
 *	Downloads attachments from Gmail and saves it to a file.
 *	Uses PHP IMAP extension, so make sure it is enabled in your php.ini,
 *	extension=php_imap.dll
 *
 */
 
 
set_time_limit(3000); 
 
 
/* connect to gmail with your credentials */
$hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
$username = env('KIGALILIFE_USERNAME'); # e.g somebody@gmail.com
$password = env('KIGALILIFE_PASSWORD');
 
 
/* try to connect */
$inbox = imap_open($hostname,$username,$password) or die('Cannot connect to Gmail: ' . imap_last_error());
 
 
/* get all new emails. If set to 'ALL' instead 
 * of 'NEW' retrieves all the emails, but can be 
 * resource intensive, so the following variable, 
 * $max_emails, puts the limit on the number of emails downloaded.
 * 
 */
$emails = imap_search($inbox,'SUBJECT "[kigalilife]"');
 
/* useful only if the above search is set to 'ALL' */
$max_emails = 16;

$numberOfUnseenEmails = 0;
 
/* if any emails found, iterate through each email */
if($emails) {
 
    $count = 1;
 
    /* put the newest emails on top */
    rsort($emails);
 
    /* for every email... */
    foreach($emails as $email_number) 
    {
 
        /* get information specific to this email */
        $overview = imap_fetch_overview($inbox,$email_number,0);
 		
 		// if this mail has been seen skip it
 		if (current($overview)->seen == 1) {
 			continue;
 		}

 		$numberOfUnseenEmails++;
        
        /* get mail message, not actually used here. 
           Refer to http://php.net/manual/en/function.imap-fetchbody.php
           for details on the third parameter.
         */
        $message = imap_fetchbody($inbox,$email_number,2);
 
        /* get mail structure */
        $structure = imap_fetchstructure($inbox, $email_number);
 
        $attachments = array();
 
        /* if any attachments found... */
        if(isset($structure->parts) && count($structure->parts)) 
        {
            for($i = 0; $i < count($structure->parts); $i++) 
            {
                $attachments[$i] = array(
                    'is_attachment' => false,
                    'filename' => '',
                    'name' => '',
                    'attachment' => ''
                );
 
                if($structure->parts[$i]->ifdparameters) 
                {
                    foreach($structure->parts[$i]->dparameters as $object) 
                    {
                        if(strtolower($object->attribute) == 'filename') 
                        {
                            $attachments[$i]['is_attachment'] = true;
                            $attachments[$i]['filename'] = $object->value;
                        }
                    }
                }
 
                if($structure->parts[$i]->ifparameters) 
                {
                    foreach($structure->parts[$i]->parameters as $object) 
                    {
                        if(strtolower($object->attribute) == 'name') 
                        {
                            $attachments[$i]['is_attachment'] = true;
                            $attachments[$i]['name'] = $object->value;
                        }
                    }
                }
 
                if($attachments[$i]['is_attachment']) 
                {
                    $attachments[$i]['attachment'] = imap_fetchbody($inbox, $email_number, $i+1);
 
                    /* 3 = BASE64 encoding */
                    if($structure->parts[$i]->encoding == 3) 
                    { 
                        $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
                    }
                    /* 4 = QUOTED-PRINTABLE encoding */
                    elseif($structure->parts[$i]->encoding == 4) 
                    { 
                        $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
                    }
                }
            }
        }
	 	ini_set('memory_limit', '1024M');
        /* iterate through each attachment and save it */
        foreach($attachments as $attachment)
        {
            if($attachment['is_attachment'] == 1)
            {
                $filename = $attachment['name'];
                if(empty($filename)) $filename = $attachment['filename'];
 
                if(empty($filename)) $filename = time() . ".dat";
 
                /* prefix the email number to the filename in case two emails
                 * have the attachment with the same file name.
                 */
                $filename = preg_replace('/[^A-Za-z0-9\-]/', '', $filename);
                $fp = fopen("../storage/app/attachments/" . $email_number . "-" . $filename.time(), "w+");
                fwrite($fp, $attachment['attachment']);
                fclose($fp);
            }
 
        }
 
        if($count++ >= $max_emails) break;
    }
 
} 


/* close the connection */
imap_close($inbox);

echo $numberOfUnseenEmails;
echo "Done";
    });
});
