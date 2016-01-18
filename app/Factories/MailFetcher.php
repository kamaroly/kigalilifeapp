<?php 
namespace App\Factories;


/**
 *	Gmail attachment extractor.
 *
 *  @author ; Kamaro Lambert
 *	Downloads attachments from Gmail and saves it to a file.
 *	Uses PHP IMAP extension, so make sure it is enabled in your php.ini,
 *	extension=php_imap.dll
 *
 */
//include email parser
use Exception;
use  Sunra\PhpSimple\HtmlDomParser;
use App\Models\Ad;

class MailFetcher 
{

	/* connect to gmail with your credentials */
	/**
	 * Imap host server
	 * @var string
	 */
	public $hostname = '{imap.gmail.com:993/imap/ssl}INBOX';

	/**
	 * Imap username 
	 * # e.g somebody@gmail.com
	 * @var string
	 */
	public $username ; 

	/**
	 * Imap password
	 * @var string
	 */
	public $password;

	/**
	 * Setting maximum execution time
	 * @var integer
	 */
	public $timeLimit  = 3000;

	/**
	 * Inbox connection
	 * 		
	 * @var imap_open
	 */
	private $connection;

	/**
	 * $searchQuery to filter imap inbox
	 * @var string
	 */
	public $searchQuery  ;

	/**
	 * Emails returned by the imap
	 * @var array
	 */
	private $emails;

	/**
	 * This variable will help us to know if we need to use 
	 * new set search or not
	 * @var boolean
	 */
	private $isDefaultSeach = true;
	/**
	 * get all new emails. If set to 'ALL' instead 
	 * of 'NEW' retrieves all the emails, but can be 
	 * resource intensive, so the following variable, 
	 * $max_emails, puts the limit on the number of emails downloaded
	 * @var numeric
	 */
	public $max_emails = 1000;

	/**
	 * Set path to the attachment
	 * @var string
	 */
	public $attachmentPath = "/attachments/";

	/**
	 * This variable holds the title of the current mail 
	 * that is being processed
	 * @var string
	 */
	private $currentMailTitle;

	/**
	 * Entry point for out class
	 * @param string  $hostname  imap hostname
	 * @param string  $username  imap username
	 * @param string  $password  imap password
	 * @param integer $timeLimit maximum execution time in seconds
	 */
	function __construct($username,$password,$hostname = '{imap.gmail.com:993/imap/ssl}INBOX',$timeLimit = 3000) {

		$this->username = $username;
		$this->password = $password;
		$this->hostname = $hostname;
		$this->timeLimit= $timeLimit;
		$this->searchQuery =  'SUBJECT "[kigalilife]" SINCE "'.date('d F Y',strtotime('-14 day',time())).'"';
		$this->attachmentPath =  public_path().$this->attachmentPath;

		/** Authenticate upon initiating this class */
		$this->open();
	}


	/**
	 * Try to authenticate
	 * @return [type] [description]
	 */
	private function open()
	{
		try {
			/* try to connect */
			$this->connection = imap_open($this->hostname,$this->username,$this->password) or die('Cannot connect to Gmail: ' . imap_last_error());
		
		} catch (Exception $e) {
			dd($e->getMessage());
		}
		
		return $this;
	}

	/**
	 * Perform seach on the imap 
	 * @return void 
	 */
	public function search($query = null)
	{	
		/** update search query if not null */
		if (is_null($query) == false) {
			$this->searchQuery = $query;
		}

		$emails = imap_search($this->connection,$this->searchQuery);
		if ($emails) {
			/* put the newest emails on top */
			// rsort($emails);
			$this->emails = $emails;
		}

		$this->isDefaultSeach = false;
		return $this;
	}

	/**
	 * Retrieve emails
	 * @return 
	 */
	public function get()
	{
		    set_time_limit($this->timeLimit); 

		    /**
		     * Perform search if it was not performed
		     */
		    if ($this->isDefaultSeach) {
		    	$this->search();
		    }

			$count = 1;

			$emailsFound = array();

			// Get the latest email we retrieved
			$latestAd = (int) Ad::max('message_number');

			// If the latest email retrieve is same as the latest email 
			// at the emap server at the moment, then stop by here
			
			rsort($this->emails);
			if($latestAd==reset($this->emails)){
				$this->close();
				return [];
			}
			
			// Change back to the old order
			rsort($this->emails);
			/* for every email... */
		    foreach($this->emails as $email_number) 
		    {	
		    	$ad = new Ad;
		    	// Get headers 
		    	$email = $this->getHeaders($email_number);
		    	
		    	try
		    	{
				    	
						$ad->owner			=  trim(str_replace('[kigalilife]', '',$email->from[0]->personal));
						$ad->subject		=  trim(str_replace('[kigalilife]', '',$email->subject));

						$ad->slug		    =  $this->slug(str_slug($ad->subject));
						$ad->message_id		=  $email->message_id;
						$ad->message_number	=  $email_number;
						$ad->sent_date		=  $email->date;
						$ad->recieved_date	=  $email->maildate;
						$ad->sender_address	=  $email->senderaddress;
						$ad->from_address	=  trim(substr($email->fromaddress,1,strpos($email->fromaddress,'[kigalilife]')-1));
						$ad->size			=  $email->size;
						$ad->udate			=  $email->udate;

				    	// Get mail number
				    	$email->message_number = $email_number;

				    	// Get body of the current message
				    	$body_data = imap_fetchbody($this->connection,$email_number,"");

				    	// Get clean body
				    	$ad->body = trim($this->getBody($body_data));

				    	// Get attachments if they are inline
				    	$email->attachments = $this->getAttachments($body_data,$email_number);

				    	// Add more attachments if any attachment that is not inline exist
				    	$otherAttachemnts = $this->fetchAttachments($email_number);

				    	if (!empty($otherAttachemnts)) {
							$email->attachments[] = $otherAttachemnts;
				    	}

				    	// Save all this emails information
				 		$emailsFound[$email_number] = $email;

				 		$ad->attachments	=  json_encode($email->attachments);

				 		$ad->save();

				    
				}
				catch(\Exception $e)
				{
					print $e->getMessage();
				}
		        if($count++ >= $this->max_emails) break;
		   }

		   $this->close();
		   return $emailsFound;
	}

	private function slug($slug){
		$latestSlug = Ad::whereRaw("slug RLIKE '^{$slug}(-[0-9]*)?$'")
						 ->latest('id')
						 ->pluck('slug');

	    if($latestSlug){
	    	$pieces = explode('-',$latestSlug);

	    	$number = end($pieces);

	    	$slug   .= '-'.($number + 1);
	    }

	    return $slug;
	}

	/**
	 * Get specific mail headers
	 * @param  numeric $number mail number
	 * @return array         headers info
	 */
	public function getHeaders($email_number)
	{
		$headers = new \stdClass();
		$headerInfo = imap_headerinfo($this->connection,$email_number);
		foreach ($headerInfo as $key => $value) {
			$key = strtolower($key);
			$headers->$key  = $value;
		}

		return $headers;
	}

	 /**
	 * Get specific mail body
	 * @param  numeric $number mail number
	 * @return array         headers info
	 */
	public function getBody($body_data)
	{
		$body = quoted_printable_decode($body_data);

		if (strpos(strtolower($body),strtolower('id="ygrp-text"')) != false) {
			$dom = HtmlDomParser::str_get_html( $body );
	        $elems = $dom->find('div[id=ygrp-text]');

	        if (empty($elems) == false) {
		        $body = $elems[0]->innertext;
	        }
		}

		return strip_tags($body);
	}

	/**
	 * Get specific mail attachments
	 * @param  numeric $number mail number
	 * @return array         headers info
	 */
	public function getAttachments($decode,$email_number)
	{
		        $retrivedAttachments = array();
		        /* get information specific to this email */
				  preg_match_all('/< *img[^>]*src *= *["\']?([^"\']*)/i', $decode, $images);
		 		 
				 if (isset($images[1]) && count($images[1]) > 0) {
				 	foreach ($images[1] as $key => $value) {

				 		if (empty(strpos($value,'xa.yimg.com')))
				 		{
				 			continue;
				 		}
				 		// For the sake of gaining time and performance, we won't save our
				 		// images locally, instead we are going to add them to the  array
				 		// which we will be saving in our db
			 			$retrivedAttachments[] = [
			 										'thumbnail' => $value,
			 										'full_image'=> str_replace('/tn/', '/', $value)
			 									 ];
			 		}
				 }
        return $retrivedAttachments;
	}

	/**
	 * Get specific mail attachments
	 * @param  numeric $number mail number
	 * @return array         headers info
	 */
	public function fetchAttachments($email_number)
	{
				  /* get information specific to this email */
		        $overview = imap_fetch_overview($this->connection,$email_number,0);
		 
		        /* get mail message, not actually used here. 
		           Refer to http://php.net/manual/en/function.imap-fetchbody.php
		           for details on the third parameter.
		         */
		        $message = imap_fetchbody($this->connection,$email_number,2);
		 
		        /* get mail structure */
		        $structure = imap_fetchstructure($this->connection, $email_number);
		 
		        $attachments = array();
		 
		        /* if any attachments found... */
		 
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
		                    $attachments[$i]['attachment'] = imap_fetchbody($this->connection, $email_number, $i+1);
		 
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
		        foreach($attachments as $key => $attachment)
		        {
		        	// if it's not attachment remove it 
					// and go to the next element
		 			if ($attachment['is_attachment']==false) {
		 				unset($attachments[$key]);
		 				continue;
		 			}

		 		   // For us to reach here we have an attachment
		 		   // Let's extract it then
	               $filename = $attachment['name'];
	                if(empty($filename)) $filename = $attachment['filename'];
	 
	                if(empty($filename)) $filename = time() . ".dat";
	 
	                /* prefix the email number to the filename in case two emails
	                 * have the attachment with the same file name.
	                 */
	                $absolutePath = $this->attachmentPath . $email_number . "-" . $filename;

	                file_put_contents($absolutePath, $attachment['attachment']);

		        }

        return $attachments;
	}
	/**
	 * Set search query
	 * 
	 * ALL - return all messages matching the rest of the criteria
	 * ANSWERED - match messages with the \\ANSWERED flag set
	 * BCC "string" - match messages with "string" in the Bcc: field
	 * BEFORE "date" - match messages with Date: before "date"
	 * BODY "string" - match messages with "string" in the body of the message
	 * CC "string" - match messages with "string" in the Cc: field
	 * DELETED - match deleted messages
	 * FLAGGED - match messages with the \\FLAGGED flag set
	 * FROM "string" - match messages with "string" in the From: field
	 * KEYWORD "string" - match messages with "string" as a keyword
	 * NEW - match new messages
	 * OLD - match old messages
	 * ON "date" - match messages with Date: matching "date"
	 * RECENT - match messages with the \\RECENT flag set
	 * SEEN - match messages that have been read (the \\SEEN flag is set)
	 * SINCE "date" - match messages with Date: after "date"
	 * SUBJECT "string" - match messages with "string" in the Subject:
	 * TEXT "string" - match messages with text "string"
	 * TO "string" - match messages with "string" in the To:
	 * UNANSWERED - match messages that have not been answered
	 * UNDELETED - match messages that are not deleted
	 * UNFLAGGED - match messages that are not flagged
	 * UNKEYWORD "string" - match messages that do not have the keyword "string"
	 * UNSEEN - match messages which have not been read yet
	 *
	 * @example
	 *  UNSEEN emails since 29th July 2014 
	 * 'UNSEEN SINCE "09 January 2016"'
	 * @param string $query
	 */
	public function setSearchQuery($query='ALL')
	{
		$this->searchQuery = $query;
		return $this;
	}

	private function close()
	{
		imap_close($this->connection);

	}
}