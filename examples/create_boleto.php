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
    new \GuzzleHttp\Client(),
    true
);

$client->authenticate($_ENV['SICREDI_USERNAME'], $_ENV['SICREDI_PASSWORD']);

$boletoClient = $client->boleto();

$boleto = new \SicrediAPI\Domain\Boleto\Boleto(
    (new Beneficiary(
        'Jose da Silva',
        '86049253099',
        'person'
    )),
    (new Payee(
        'Maria de Lurdes',
        '50581718054',
        'person'
    )),
    100.00,
    'DM',
    12345,
    'RECIBO',
    '999999',
    new DateTime('2023-12-31')
);

$boletoClient->create($boleto);
