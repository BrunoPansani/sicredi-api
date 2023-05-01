<?php

namespace SicrediAPI\Domain\Boleto;

class Liquidation
{
    private $liquidationDate;
    private $amount;
    private $penalty;
    private $reduction;
    private $interest;
    private $discount;

    private $ourNumber;
    private $yourNumber;
    private $liquidationType;
    private $originalAmount;

    public function __construct(
        \DateTime $liquidationDate,
        float $amount,
        float $penalty = null,
        float $reduction = null,
        float $interest = null,
        float $discount = null,
        string $ourNumber = null,
        string $yourNumber = null,
        float $originalAmount = null,
        string $liquidationType = null
    ) {
        $this->liquidationDate = $liquidationDate;
        $this->amount = $amount;
        $this->penalty = $penalty;
        $this->reduction = $reduction;
        $this->interest = $interest;
        $this->discount = $discount;
        $this->ourNumber = $ourNumber;
        $this->yourNumber = $yourNumber;
        $this->originalAmount = $originalAmount;
        $this->liquidationType = $liquidationType;
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

    public function getOurNumber(): ?string
    {
        return $this->ourNumber;
    }

    public function getYourNumber(): ?string
    {
        return $this->yourNumber;
    }

    public function getLiquidationType(): ?string
    {
        return $this->liquidationType;
    }

    public function getOriginalAmount(): ?float
    {
        return $this->originalAmount;
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
