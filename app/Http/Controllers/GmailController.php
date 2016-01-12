<?php

namespace App\Http\Controllers;
// use App\GmailEmail;
use App\Services\GoogleGmail;
use Illuminate\Support\Facades\Redirect;
class GmailController extends Controller
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

    /**
     * @todo get the messages from the database
     */
       // $messages = GmailEmail::all();
       // if(!$messages)
       // {
        $messagesList = $this->gmailService->getEmailsList();

       // } else {
          // $messagesHistoryList = $this->gmailService->listHistory();
          // $messagesList = $this->gmailService->prepareHistoryList($messagesHistoryList);
       // }
       
       dd($this->gmailService->getLebels());
       $messagesContents = $this->gmailService->getMailsContents($messagesList);

       dd($messagesContents);
         $this->gmailService->saveMailsToDb($messagesContents);
        return redirect(url('/dashboard/messages/gmail/emails'));
  }
}