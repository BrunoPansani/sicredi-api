<?php

namespace SicrediAPI\Resources;

use SicrediAPI\Domain\Boleto as BoletoDomain;
use SicrediAPI\Mappers\CreateBoleto as CreateBoletoMapper;

class Boleto extends ResourceAbstract
{
    public function create(BoletoDomain $boleto)
    {
        $payload = (new CreateBoletoMapper($boleto))->toArray();

        var_dump($payload);
        $response = $this->post('/cobranca/boleto/v1/boletos', [
            'json' => $payload,
            'headers' => [
                'cooperativa' => $this->apiClient->getCooperative(),
                'posto' => $this->apiClient->getPost(),
            ]
        ]);

        var_dump($response->getBody()->getContents());
        die();

        // return $response->getBody()->getContents();
    }

}
