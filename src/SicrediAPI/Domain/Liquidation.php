<?php

namespace SicrediAPI\Domain;

class Liquidation
{
    private $liquidationDate;
    private $amount;
    private $penalty;
    private $reduction;
    private $interest;
    private $discount;

    public function __construct(
        \DateTime $liquidationDate,
        float $amount,
        float $penalty = null,
        float $reduction = null,
        float $interest = null,
        float $discount = null
    ) {
        $this->liquidationDate = $liquidationDate;
        $this->amount = $amount;
        $this->penalty = $penalty;
        $this->reduction = $reduction;
        $this->interest = $interest;
        $this->discount = $discount;
    }

    public function getLiquidationDate(): \DateTime
    {
        return $this->liquidationDate;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getPenalty(): ?float
    {
        return $this->penalty;
    }

    public function getReduction(): ?float
    {
        return $this->reduction;
    }

    public function getInterest(): ?float
    {
        return $this->interest;
    }

    public function getDiscount(): ?float
    {
        return $this->discount;
    }

    public static function fromArray(array $data)
    {
        return new self(
            \DateTime::createFromFormat('Y-m-d\TH:i:s.uP', $data['data']),
            $data['valor'],
            $data['multa'],
            $data['abatimento'],
            $data['juros'],
            $data['desconto']
        );

    }
}
