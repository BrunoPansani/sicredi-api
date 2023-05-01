<?php

namespace SicrediAPI\Mappers;

use SicrediAPI\Domain\Boleto\DiscountConfiguration as DiscountConfigurationDomain;

class DiscountConfiguration
{
    private $discounts;

    public function __construct(DiscountConfigurationDomain $discounts)
    {
        $this->discounts = $discounts;
    }

    private function getDiscountType()
    {
        return $this->discounts->getDiscountType() == DiscountConfigurationDomain::TYPE_PERCENTAGE ? 'PERCENTUAL' : 'VALOR';
    }

    public function toArray()
    {
        $configuration = [
            'tipoDesconto' => $this->getDiscountType()
        ];

        if (!empty($this->discounts->getEarlyPaymentDiscount())) {
            $configuration['descontoAntecipado'] = $this->discounts->getEarlyPaymentDiscount();
            return $configuration;
        }

        if (!empty($this->discounts->getDiscounts())) {
            foreach ($this->discounts->getDiscounts() as $key => $item) {
                $discountIndex = $key + 1;
                $configuration['valorDesconto' . $discountIndex] = $item->getAmount();
                $configuration['dataDesconto' . $discountIndex] = $item->getDate()->format('Y-m-d');
            }
        }

        return $configuration;
    }
}
