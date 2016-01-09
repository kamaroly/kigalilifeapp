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
	Route::get('/fetch', function(){
 
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
$username =  env('KIGALILIFE_USERNAME'); # e.g somebody@gmail.com
$password =  env('KIGALILIFE_PASSWORD');
 
 
/* try to connect */
$inbox = imap_open($hostname,$username,$password) or die('Cannot connect to Gmail: ' . imap_last_error());
 
 
/* get all new emails. If set to 'ALL' instead 
 * of 'NEW' retrieves all the emails, but can be 
 * resource intensive, so the following variable, 
 * $max_emails, puts the limit on the number of emails downloaded.
 *
 *ALL - return all messages matching the rest of the criteria
  ANSWERED - match messages with the \\ANSWERED flag set
  BCC "string" - match messages with "string" in the Bcc: field
  BEFORE "date" - match messages with Date: before "date"
  BODY "string" - match messages with "string" in the body of the message
  CC "string" - match messages with "string" in the Cc: field
  DELETED - match deleted messages
  FLAGGED - match messages with the \\FLAGGED flag set
  FROM "string" - match messages with "string" in the From: field
  KEYWORD "string" - match messages with "string" as a keyword
  NEW - match new messages
  OLD - match old messages
  ON "date" - match messages with Date: matching "date"
  RECENT - match messages with the \\RECENT flag set
  SEEN - match messages that have been read (the \\SEEN flag is set)
  SINCE "date" - match messages with Date: after "date"
  SUBJECT "string" - match messages with "string" in the Subject:
  TEXT "string" - match messages with text "string"
  TO "string" - match messages with "string" in the To:
  UNANSWERED - match messages that have not been answered
  UNDELETED - match messages that are not deleted
  UNFLAGGED - match messages that are not flagged
  UNKEYWORD "string" - match messages that do not have the keyword "string"
  UNSEEN - match messages which have not been read yet

 * 'FROM "person" SUBJECT "something in subject"'
 *
 * /* Only read emails which has 'gmail attach' string in the subject field 
   after 21st July 2014 
 */
$emails = imap_search($inbox,'SUBJECT "[kigalilife]" SINCE "09 January 2016"');
 
// /* UNSEEN emails since 29th July 2014 */
// $emails = imap_search($inbox, 'UNSEEN SINCE "29 July 2014"');
 
//  ALL emails since 29th July 2014 
// $emails = imap_search($inbox, 'ALL SINCE "29 July 2014"');
//  */
// $emails = imap_search($inbox,'ALL');
 
/* useful only if the above search is set to 'ALL' */
$max_emails = 16;
 
 
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
                $fp = fopen("./" . $email_number . "-" . $filename, "w+");
                fwrite($fp, $attachment['attachment']);
                fclose($fp);
            }
 
        }
 
        if($count++ >= $max_emails) break;
    }
 
} 
 
/* close the connection */
imap_close($inbox);
 
echo "Done";
	});
});
