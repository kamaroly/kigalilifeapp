<?php 
namespace App\Factories\Mails;

class IMAPMessage
{
    private $mailbox;
    private $number;
    private $stream;
    public function __construct(IMAPMailbox $mailbox, $number)
    {
        $this->mailbox = $mailbox;
        $this->number = $number;
        $this->stream = $mailbox->getStream();
    }
    public function getNumber()
    {
        return $this->number;
    }
    /**
     * @param int $number
     * @return string
     */
    public function fetchBody($number)
    {
        return imap_fetchbody($this->stream, $this->number, $number);
    }
    /**
     * @return stdClass
     * @throws Exception
     */
    public function fetchOverview()
    {
        $result = imap_fetch_overview($this->stream, $this->number);
        if (FALSE === $result) {
            throw new Exception('FetchOverview failed: ' . imap_last_error());
        }
        list($result) = $result;
        foreach ($result as &$prop) {
            $prop = imap_utf8($prop);
        }
        return $result;
    }
    public function fetchStructure()
    {
        $structure = imap_fetchstructure($this->stream, $this->number);
        if (FALSE === $structure) {
            throw new Exception('FetchStructure failed: ' . imap_last_error());
        }
        return $structure;
    }
    /**
     * @return IMAPAttachments
     */
    public function getAttachments()
    {
        return new IMAPAttachments($this);
    }
    public function __toString()
    {
        return (string)$this->number;
    }
}