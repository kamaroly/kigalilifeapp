<?php 
namespace App\Factories\Mails;

class IMAPAttachment
{
    private $attachment;
    private $message;
    public function __construct(IMAPMessage $message, $attachment)
    {
        $this->message = $message;
        $this->attachment = $attachment;
    }
    /**
     * @return string;
     */
    public function getBody()
    {
        return $this->message->fetchBody($this->attachment->number);
    }
    /**
     * @return int
     */
    public function getSize()
    {
        return (int)$this->attachment->bytes;
    }
    /**
     * @return string
     */
    public function getExtension()
    {
        return pathinfo($this->getFilename(), PATHINFO_EXTENSION);
    }
    public function getFilename()
    {
        $filename = $this->attachment->filename;
        NULL === $filename && $filename = $this->attachment->name;
        return $filename;
    }
    public function __toString()
    {
        $encoding = $this->attachment->encoding;
        switch ($encoding) {
            case 0: // 7BIT
            case 1: // 8BIT
            case 2: // BINARY
                return $this->getBody();
            case 3: // BASE-64
                return base64_decode($this->getBody());
            case 4: // QUOTED-PRINTABLE
                return imap_qprint($this->getBody());
        }
        throw new Exception(sprintf('Encoding failed: Unknown encoding %s (5: OTHER).', $encoding));
    }
}