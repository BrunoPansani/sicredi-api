<?php

namespace SicrediAPI\Mappers;

use SicrediAPI\Domain\Boleto\Payee as PayeeDomain;

class Payee
{
    private $payee;

    public function __construct(PayeeDomain $payee)
    {
        $this->payee = $payee;
    }

    private function getPersonKind()
    {
        return $this->payee->getPersonKind() == PayeeDomain::PERSON_KIND_NATURAL ? 'PESSOA_FISICA' : 'PESSOA_JURIDICA';
    }

    public function toArray()
    {
        $payee = [
            'nome' => $this->payee->getName(),
            'documento' => $this->payee->getDocument(),
            'tipoPessoa' => $this->getPersonKind(),
            'endereco' => $this->payee->getAddress(),
            'cidade' => $this->payee->getCity(),
            'uf' => $this->payee->getState(),
            'cep' => $this->payee->getZipCode(),
            'telefone' => $this->payee->getPhone(),
            'email' => $this->payee->getEmail()
        ];

        return array_filter($payee, function ($value) {
            return !empty($value);
        });
    }
}
