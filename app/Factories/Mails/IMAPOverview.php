<?php 
namespace App\Factories\Mails;

class IMAPOverview extends ArrayObject
{
    private $mailbox;
    public function __construct(IMAPMailbox $mailbox, $sequence)
    {
        $result = imap_fetch_overview($mailbox->getStream(), $sequence);
        if (FALSE === $result) {
            throw new Exception('Overview failed: ' . imap_last_error());
        }
        $this->mailbox = $mailbox;
        foreach ($result as $overview)
        {
            if (!isset($overview->subject)) {
                $overview->subject = '';
            } else {
                $overview->subject = IMAP::decodeToUTF8($overview->subject);
            }
        }
        parent::__construct($result);
    }
    /**
     * @return IMAPMailbox
     */
    public function getMailbox()
    {
        return $this->mailbox;
    }
}
 ?>