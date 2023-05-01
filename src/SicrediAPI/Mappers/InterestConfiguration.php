<?php

namespace SicrediAPI\Mappers;

use SicrediAPI\Domain\Boleto\InterestConfiguration as InterestConfigurationDomain;

class InterestConfiguration
{
    private $interest;

    public function __construct(InterestConfigurationDomain $interest)
    {
        $this->interest = $interest;
    }

    private function getInterestType()
    {
        return $this->interest->getInterestType() == InterestConfigurationDomain::TYPE_PERCENTAGE ? 'PERCENTUAL' : 'VALOR';
    }

    public function toArray()
    {

        $configuration = [
            'tipoJuros' => $this->getInterestType()
        ];

        if (!empty($this->interest->getInterestAmount())) {
            $configuration['juros'] = $this->interest->getInterestAmount();
        }

        if (!empty($this->interest->getPenaltyAmount())) {
            $configuration['multa'] = $this->interest->getPenaltyAmount();
        }

        if (!empty($this->interest->getDaysAutoNegativateAfter())) {
            $configuration['diasNegativacaoAuto'] = $this->interest->getDaysAutoNegativateAfter();
        }

        if (!empty($this->interest->getDaysAutoProtestAfter())) {
            $configuration['diasProtestoAuto'] = $this->interest->getDaysAutoProtestAfter();
        }

        if (!empty($this->interest->getDaysValidAfterDueDate())) {
            $configuration['validadeAposVencimento'] = $this->interest->getDaysValidAfterDueDate();
        }

        return $configuration;
    }
}
