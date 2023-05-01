<?php

namespace SicrediAPI\Mappers;

use SicrediAPI\Domain\Boleto\Beneficiary as BeneficiaryDomain;

class Beneficiary
{
    private $beneficiary;

    public function __construct(BeneficiaryDomain $beneficiary)
    {
        $this->beneficiary = $beneficiary;
    }

    private function getPersonKind()
    {
        return $this->beneficiary->getPersonKind() == BeneficiaryDomain::PERSON_KIND_NATURAL ? 'PESSOA_FISICA' : 'PESSOA_JURIDICA';
    }

    public function toArray()
    {
        $beneficiary = [
            'documento' => $this->beneficiary->getDocument(),
            'tipoPessoa' => $this->getPersonKind(),
            'nome' => $this->beneficiary->getName(),
            'endereco' => $this->beneficiary->getAddress(),
            'cidade' => $this->beneficiary->getCity(),
            'complemento' => $this->beneficiary->getComplement(),
            'numero' => $this->beneficiary->getAddressNumber(),
            'uf' => $this->beneficiary->getState(),
            'cep' => $this->beneficiary->getZipCode(),
            'telefone' => $this->beneficiary->getPhone(),
            'email' => $this->beneficiary->getEmail()
        ];

        return array_filter($beneficiary, function ($value) {
            return !empty($value);
        });
    }
}
