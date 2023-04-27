<?php

namespace SicrediAPI\Mappers;

class Messages {
    private $message;

    public function __construct($messages)
    {
        $this->message = $messages;
    }

    public function toArray() {
        return $this->message->getLines();
    }
}