<?php

namespace SicrediAPI\Mappers;

use SicrediAPI\Domain\Boleto\Information as InformationDomain;

class Information
{
    private $information;

    public function __construct(InformationDomain $information)
    {
        $this->information = $information;
    }

    public function toArray()
    {
        if (empty($this->information)) {
            return [];
        }

        return $this->information->getLines();
    }
}
