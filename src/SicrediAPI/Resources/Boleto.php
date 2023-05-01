<?php

namespace SicrediAPI\Resources;

use DateTime;
use GuzzleHttp\Exception\GuzzleException;
use SicrediAPI\Domain\Boleto\Boleto as BoletoDomain;
use SicrediAPI\Domain\Boleto\Liquidation;
use SicrediAPI\Domain\Boleto\PaymentInformation;
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

    /**
     * Returns the Boletos liquidated in a specific day
     * @param DateTime $day 
     * @return Liquidation[]
     * @throws GuzzleException 
     */
    public function queryDailyLiquidations(\DateTime $day, int $page = 1) {

        // /cobranca/boleto/v1/boletos/liquidados/dia?codigoBeneficiario=12345&dia=15/08/2022

        $response = $this->get('/cobranca/boleto/v1/boletos/liquidados/dia', [
            'query' => [
                'codigoBeneficiario' => $this->apiClient->getBeneficiaryCode(),
                'dia' => $day->format('d/m/Y'),
            ],
            'headers' => [
                'cooperativa' => $this->apiClient->getCooperative(),
                'posto' => $this->apiClient->getPost(),
            ]
        ]);

        $liquidations = BoletoMapper::mapFromQueryDailyLiquidations($response);

        return $liquidations;
    }

}
