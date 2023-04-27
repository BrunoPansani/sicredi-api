<?php

namespace SicrediAPI\Mappers;

class Messages
{
    private $message;

    public function __construct($messages)
    {
        $this->message = $messages;
    }

    public function toArray()
    {
        if (empty($this->message)) {
            return [];
        }

        return $this->message;
    }
}
