<?php

namespace SicrediAPI\Resources;

use SicrediAPI\Domain\Boleto as BoletoDomain;
use SicrediAPI\Domain\PaymentInformation;
use SicrediAPI\Mappers\Boleto as BoletoMapper;

class Boleto extends ResourceAbstract
{
    public function create(BoletoDomain $boleto): BoletoDomain
    {
        $payload = BoletoMapper::mapCreateBoleto($boleto);

        $response = $this->post('/cobranca/boleto/v1/boletos', [
            'json' => $payload,
            'headers' => [
                'cooperativa' => $this->apiClient->getCooperative(),
                'posto' => $this->apiClient->getPost(),
            ]
        ]);

        $paymentInformation = PaymentInformation::fromArray($response);

        $boleto->setPaymentInformation($paymentInformation);

        if ($boleto->getOurNumber() === null) {
            $boleto->setOurNumber($paymentInformation->getOurNumber());
        }

        return $boleto;
    }

    public function query(string $ourNumber): BoletoDomain
    {
        // boleto/v1/boletos?codigoBeneficiario=12345&nossoNumero=211001290
        $response = $this->get('/cobranca/boleto/v1/boletos/', [
            'query' => [
                'codigoBeneficiario' => $this->apiClient->getBeneficiaryCode(),
                'nossoNumero' => $ourNumber,
            ],
            'headers' => [
                'cooperativa' => $this->apiClient->getCooperative(),
                'posto' => $this->apiClient->getPost(),
            ]
        ]);

        $boleto = BoletoMapper::mapFromQuery($response);

        return $boleto;
    }

}
