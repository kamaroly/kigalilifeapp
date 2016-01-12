<?php

namespace App\Http\Controllers;
use App\GmailEmail;
use App\Http\Controllers\ApiController;
use App\Services\GoogleGmail;
use Illuminate\Support\Facades\Redirect;
class GmailController extends ApiController
{
  protected $gmailService;
  function __construct(GoogleGmail $googleGmail)
    {
    $this->gmailService = $googleGmail;
  }
   //get code access /**

  // @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector 
  public function googleAuth()
    { 
      return $this->gmailService->redirectToAuth();
  }


   // get list of messages , get their contents and save all data to db

  public function getEmails()
    {
       $messagesList = null;
       $messages = GmailEmail::all();
       if(!$messages)
       {
        $messagesList = $this->gmailService->getEmailsList();
       } else {
          $messagesHistoryList = $this->gmailService->listHistory();
          $messagesList = $this->gmailService->prepareHistoryList($messagesHistoryList);
       }
       $messagesContents = $this->gmailService->getMailsContents($messagesList);
       dd($messagesContents);
         $this->gmailService->saveMailsToDb($messagesContents);
        return redirect(url('/dashboard/messages/gmail/emails'));
  }
}