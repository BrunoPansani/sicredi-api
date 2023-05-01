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
     * Returns the Boletos liquidated in a specific day.
     * This method returns an instance of Meta\Paginator, which is an iterable object.
     * Upon reaching the end of the page, the next page is automatically fetched.
     * Beware of the performance implications of this method. Memory usage will increase as more pages are fetched.
     *
     * @param DateTime $day
     * @return Meta\Paginator
     * @throws GuzzleException
     */
    public function queryDailyLiquidations(\DateTime $day)
    {
        $liquidations = new Meta\Paginator($this, function ($page) use ($day) {
            return $this->getDailyLiquidationsByPage($page, $day);
        }, function ($items) {
            return BoletoMapper::mapFromQueryDailyLiquidations($items);
        });

        return $liquidations;
    }

    /**
     * Returns the Boletos liquidated in a specific day
     * @param DateTime $day
     * @return Liquidation[]
     * @throws GuzzleException
     */
    private function getDailyLiquidationsByPage(int $page = 1, \DateTime $day)
    {
        $response = $this->get('/cobranca/boleto/v1/boletos/liquidados/dia', [
            'query' => [
                'codigoBeneficiario' => $this->apiClient->getBeneficiaryCode(),
                'dia' => $day->format('d/m/Y'),
                'pagina' => $page,
            ],
            'headers' => [
                'cooperativa' => $this->apiClient->getCooperative(),
                'posto' => $this->apiClient->getPost(),
            ]
        ]);

        return $response;
    }

}
