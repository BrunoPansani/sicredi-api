<?php

require_once __DIR__ . '/../vendor/autoload.php';

use SicrediAPI\Domain\Boleto\Beneficiary;
use SicrediAPI\Domain\Boleto\Payee;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$client = new \SicrediAPI\Client(
    $_ENV['SICREDI_API_KEY'],
    $_ENV['SICREDI_COOPERATIVE'],
    $_ENV['SICREDI_POST'],
    $_ENV['SICREDI_BENEFICIARY'],
    new \GuzzleHttp\Client()
);

$client->authenticate($_ENV['SICREDI_USERNAME'], $_ENV['SICREDI_PASSWORD']);

$boletoClient = $client->boleto();

$boleto = new \SicrediAPI\Domain\Boleto\Boleto(
    null,
    (new Payee(
        'Bruno Henrique Pansani',
        '39423415814',
        'person',
        null,
        'Rua dos Bobos, 0',
        'Campinas',
        'SP',
        '13091410'
    )),
    10.00,
    'DM',
    97963,
    'RECIBO',
    '999999',
    new DateTime('2023-05-15')
);

$boletoClient->create($boleto);