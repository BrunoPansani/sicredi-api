<?php

namespace SicrediAPI\Domain\Boleto;

class PrintFile
{
    private $filename;
    private $content;

    public function __construct(string $filename, string $content)
    {
        $this->filename = $filename;
        $this->content = $content;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
