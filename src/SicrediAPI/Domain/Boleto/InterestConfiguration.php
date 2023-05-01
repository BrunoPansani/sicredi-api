<?php

namespace SicrediAPI\Domain\Boleto;

use DateTime;

class InterestConfiguration
{
    public const TYPE_PERCENTAGE = 'percentage';
    public const TYPE_VALUE = 'value';

    /**
     * Days to auto protest after due date
     * @var mixed
     */
    private $daysAutoProtestAfter;

    /**
     * Days to auto negativate after due date
     * @var mixed
     */
    private $daysAutoNegativateAfter;

    /**
     * Days to consider the boleto valid after due date
     * If no value is set, will assume the value of auto liquidation (baixa automatica) defined in the contract
     * @var mixed
     */
    private $daysValidAfterDueDate;

    /**
     * Interest Type can be percentage or value
     * @var string
     */
    private $interestType;

    /**
     * Interest amount to be charged each day, in percentage or value according to Interest Type
     * @var float
     */
    private $interestAmount;

    /**
     * Penalty amount in percentage to be charged per day after due date
     * @var float
     */
    private $penaltyAmount;

    public function __construct(
        string $interestType = null,
        float $interestAmount = null,
        float $penaltyAmount = null,
        int $daysAutoProtestAfter = null,
        int $daysAutoNegativateAfter = null,
        int $daysValidAfterDueDate = null
    ) {
        // Cannot use daysAutoProtestAfter with daysAutoNegativateAfter
        if (!empty($daysAutoProtestAfter) && !empty($daysAutoNegativateAfter)) {
            throw new \InvalidArgumentException("Auto Protest and Auto Negativate cannot be defined at the same time");
        }

        // $interestType must be percentage or value
        if (!empty($interestType) && !in_array($interestType, [self::TYPE_PERCENTAGE, self::TYPE_VALUE])) {
            throw new \InvalidArgumentException("Interest type must be percentage or value");
        }

        // If interestType is set, interestAmount must be set
        if (!empty($interestType) && empty($interestAmount)) {
            throw new \InvalidArgumentException("Interest amount must be set when interest type is set");
        }

        // If interestAmount is set, interestType must be set
        if (empty($interestType) && !empty($interestAmount)) {
            throw new \InvalidArgumentException("Interest type must be set when interest amount is set");
        }

        // If penaltyAmount is set, it must be between 0 and 100
        if (!empty($penaltyAmount) && ($penaltyAmount < 0 || $penaltyAmount > 100)) {
            throw new \InvalidArgumentException("Penalty amount must be between 0 and 100");
        }

        $this->assertPositive($daysAutoProtestAfter, 'Auto Protest Days', true);
        $this->assertPositive($daysAutoNegativateAfter, 'Auto Negativate Days', true);
        $this->assertPositive($daysValidAfterDueDate, 'Days Valid After Due Date', true);

        $this->assertPositive($interestAmount, 'Interest Amount');
        $this->assertPositive($penaltyAmount, 'Penalty Amount');

        $this->daysAutoProtestAfter = $daysAutoProtestAfter;
        $this->daysAutoNegativateAfter = $daysAutoNegativateAfter;
        $this->daysValidAfterDueDate = $daysValidAfterDueDate;
        $this->interestType = $interestType;
        $this->interestAmount = $interestAmount;
        $this->penaltyAmount = $penaltyAmount;
    }

    private function assertPositive($field, $fieldName, $forceInteger = false)
    {
        if (empty($field)) {
            return;
        }

        if ($field <= 0 || $field > 99) {
            throw new \InvalidArgumentException("{$fieldName} must be between 1 and 99");
        }

        if ($forceInteger && !is_int($field)) {
            throw new \InvalidArgumentException("{$fieldName} must be an integer");
        }
    }

    public function validateAgainstAmount(float $amount)
    {

        // If interestType is value, interestAmount must be less than amount
        if ($this->interestType == self::TYPE_VALUE && $this->interestAmount >= $amount) {
            throw new \InvalidArgumentException("Interest amount must be less than amount");
        }

        return true;
    }

    public function getDaysAutoProtestAfter()
    {
        return $this->daysAutoProtestAfter;
    }

    public function getDaysAutoNegativateAfter()
    {
        return $this->daysAutoNegativateAfter;
    }

    public function getDaysValidAfterDueDate()
    {
        return $this->daysValidAfterDueDate;
    }

    public function getInterestType()
    {
        return $this->interestType;
    }

    public function getInterestAmount()
    {
        return $this->interestAmount;
    }

    public function getPenaltyAmount()
    {
        return $this->penaltyAmount;
    }
}
