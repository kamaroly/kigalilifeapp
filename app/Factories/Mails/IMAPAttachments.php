<?php 
namespace App\Factories\Mails;

use ArrayObject;
use stdClass;

class IMAPAttachments extends ArrayObject
{
    private $message;
    public function __construct(IMAPMessage $message)
    {
        $array = $this->setMessage($message);
        parent::__construct($array);
    }
    private function setMessage(IMAPMessage $message)
    {
        $this->message = $message;
        return $this->parseStructure($message->fetchStructure());
    }
    private function parseStructure($structure)
    {
        $attachments = array();
        if (!isset($structure->parts)) {
            return $attachments;
        }
        foreach ($structure->parts as $index => $part)
        {
            if (!$part->ifdisposition) continue;
            $attachment = new stdClass;
            $attachment->isAttachment = FALSE;
            $attachment->number = $index + 1;
            $attachment->bytes = $part->bytes;
            $attachment->encoding = $part->encoding;
            $attachment->filename = NULL;
            $attachment->name = NULL;
            $part->ifdparameters
                && ($attachment->filename = $this->getAttribute($part->dparameters, 'filename'))
                && $attachment->isAttachment = TRUE;
            $part->ifparameters
                && ($attachment->name = $this->getAttribute($part->parameters, 'name'))
                && $attachment->isAttachment = TRUE;
            $attachment->isAttachment
                && $attachments[] = new IMAPAttachment($this->message, $attachment);
        }
        return $attachments;
    }
    private function getAttribute($params, $name)
    {
        foreach ($params as $object)
        {
            if ($object->attribute == $name) {
                return IMAP::decodeToUTF8($object->value);
            }
        }
        return NULL;
    }
}