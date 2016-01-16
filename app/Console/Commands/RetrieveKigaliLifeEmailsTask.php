<?php

namespace App\Console\Commands;

use App\Factories\MailFetcher;
use Illuminate\Console\Command;

class RetrieveKigaliLifeEmailsTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kigalilife:retrieveEmails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retrive emails sent to kigalilife group';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try
            {
            //Your gmail email address and password
            $username = env('KIGALILIFE_USERNAME');
            $password = env('KIGALILIFE_PASSWORD');
            //Select messagestatus as ALL or UNSEEN which is the unread email
            $ToSearch = 'kigalilife';
            $dateToSearch = '"05-JAN-15"';
            $messagestatus = 'SUBJECT "kigalilife" SINCE "'.date('d F Y').'"';
            //-------------------------------------------------------------------
            //Gmail host with folder
            $hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
            //Open the connection
        
            $mails = new MailFetcher($username,$password);

            $counts = $mails->get();
            $this->info('Number of email retrieved:'.count($counts));
        }
        catch(\Exception $e)
        {
             $this->error($e->getMessage());
        }
    }
}
