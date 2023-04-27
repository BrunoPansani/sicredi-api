<?php

namespace SicrediAPI\Mappers;
use SicrediAPI\Domain\Information as InformationDomain;

class Information {
    private $information;

    public function __construct(InformationDomain $information)
    {
        $this->information = $information;
    }

    public function toArray() {
        return $this->information->getLines();
    }
}