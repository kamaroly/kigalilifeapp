<?php 
namespace App\Factories;

/**
* Authentication Exception
*/
class MailFetcherAuthenticationException extends \Exception
{
	
	public function getMessage()
	{
		return 'Cannot connect to imap: ' . imap_last_error();
	}
}