<?php

namespace SicrediAPI\Domain\Boleto;

use DateTime;

class Discount
{
    private $amount;
    private $date;

    public function __construct(
        float $amount,
        DateTime $date
    ) {
        if ($amount <= 0) {
            throw new \InvalidArgumentException("Discount amount must be greater than 0");
        }

        if (empty($date)) {
            throw new \InvalidArgumentException("Discount date must be set");
        }

        $this->amount = $amount;
        $this->date = $date;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getDate()
    {
        return $this->date;
    }

    public static function create(float $amount, DateTime $date)
    {
        return new Discount($amount, $date);
    }
}
