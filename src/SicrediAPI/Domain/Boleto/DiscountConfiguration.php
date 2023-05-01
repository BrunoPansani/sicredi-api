<?php

namespace SicrediAPI\Domain\Boleto;

use DateTime;
use SicrediAPI\Domain\Boleto\Discount;

class DiscountConfiguration
{
    public const TYPE_PERCENTAGE = 'percentage';
    public const TYPE_VALUE = 'value';

    private $discountType;
    private $earlyPaymentDiscount;
    private $discounts;
    private $sumOfDiscounts;

    public function __construct(
        array $discounts = [],
        string $discountType,
        float $earlyPaymentDiscount = null
    ) {

        if ($discountType !== self::TYPE_PERCENTAGE && $discountType !== self::TYPE_VALUE) {
            throw new \InvalidArgumentException("Discount type must be \"percentage\" or \"value\"");
        }

        $this->discountType = $discountType;

        if ($earlyPaymentDiscount) {
            $this->earlyPaymentDiscount = $earlyPaymentDiscount;

            if (empty($discounts)) {
                return;
            }

            throw new \InvalidArgumentException("You can't set both the Early Payment Discounts and Discounts");
        }

        if (empty($discounts)) {
            throw new \InvalidArgumentException("You must set at least one discount");
        }

        if (count($discounts) > 3) {
            throw new \InvalidArgumentException("Discounts count must be less than 3");
        }

        $sumOfDiscounts = 0;
        $previousDate = null;

        $this->discounts = array_map(function ($discount) use (&$sumOfDiscounts, &$previousDate) {
            // Every item must be an instance of Discount
            if (!$discount instanceof Discount) {
                throw new \InvalidArgumentException("Discounts must be an array of Discount objects");
            }

            // Discounts must be defined in a crescent order
            if (isset($previousDate) && $discount->getDate() <= $previousDate) {
                throw new \InvalidArgumentException("Discount dates must be in ascending order");
            }

            // If Percentual, amount must be less than 100
            if ($this->discountType == self::TYPE_PERCENTAGE && $discount->getAmount() >= 100) {
                throw new \InvalidArgumentException("Discount amount must be less than 100%");
            }

            $previousDate = $discount->getDate();
            $sumOfDiscounts += $discount->getAmount();

            return $discount;
        }, $discounts);

        $this->sumOfDiscounts = $sumOfDiscounts;

    }

    public function validateAgainstDueDate(DateTime $dueDate)
    {
        foreach ($this->getDiscounts() as $key => $item) {
            if ($item->getDate() >= $dueDate) {
                throw new \InvalidArgumentException("Discount date must be less than due date on Discount " . $key + 1 . "}");
            }
        }

        return true;
    }

    public function validateAgainstAmount(float $amount)
    {
        // If discount type is amount, then $discountConfiguration->getSumOfDiscounts() must be less than $this->amount
        if ($this->getDiscountType() == DiscountConfiguration::TYPE_VALUE) {

            if ($this->getSumOfDiscounts() >= $amount) {
                throw new \InvalidArgumentException("Sum of discounts must be less than the amount of the boleto");
            }

            if ($this->getEarlyPaymentDiscount() >= $amount) {
                throw new \InvalidArgumentException("Early discount value must be less than the amount of the boleto");
            }
        }

        return true;
    }

    public function getDiscountType()
    {
        return $this->discountType;
    }

    public function getEarlyPaymentDiscount()
    {
        return $this->earlyPaymentDiscount;
    }

    public function getDiscounts()
    {
        return $this->discounts;
    }

    public function getSumOfDiscounts()
    {
        return $this->sumOfDiscounts;
    }
}
