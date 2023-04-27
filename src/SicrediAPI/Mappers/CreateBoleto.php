<?php

namespace SicrediAPI\Mappers;

use SicrediAPI\Domain\Boleto;

class CreateBoleto
{
    private $boleto;

    public function __construct(Boleto $boleto)
    {
        $this->boleto = $boleto;
    }

    public function toArray()
    {
        $base = [
            'beneficiarioFinal' => (new Beneficiary($this->boleto->getBeneficiary()))->toArray(),
            'pagador' => (new Payee($this->boleto->getPayee()))->toArray(),
            'nossoNumero' => $this->boleto->getOurNumber(),
            'seuNumero' => $this->boleto->getYourNumber(),
            'dataVencimento' => $this->boleto->getDueDate()->format('Y-m-d'),
            'valor' => $this->boleto->getAmount(),
            'codigoBeneficiario' => $this->boleto->getBeneficiaryCode(),
            'especieDocumento' => $this->boleto->getDocumentType(),
            'informativo' => (new Messages($this->boleto->getInformation()))->toArray(),
            'mensagem' => (new Messages($this->boleto->getMessages()))->toArray(),
        ];

        if (!empty($this->boleto->getDiscounts())) {
            array_merge($base, (new DiscountConfiguration($this->boleto->getDiscounts()))->toArray());
        }

        // Merge getInterest
        if (!empty($this->boleto->getInterests())) {
            array_merge($base, (new InterestConfiguration($this->boleto->getInterests()))->toArray());
        }

        // Merge messages and information
        if (!empty($this->boleto->getMessages())) {
            array_merge($base, (new Messages($this->boleto->getMessages()))->toArray());
        }

        if (!empty($this->boleto->getInformation())) {
            array_merge($base, (new Information($this->boleto->getInformation()))->toArray());
        }

        return array_filter($base, function ($value) {
            return !empty($value);
        });
    }
}
