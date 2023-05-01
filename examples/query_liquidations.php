<?php

require_once __DIR__ . '/../vendor/autoload.php';

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

$liquidations = $boletoClient->queryDailyLiquidations(DateTime::createFromFormat('Y-m-d', '2021-09-01'));

foreach ($liquidations as $key => $value) {
    var_dump($key);
}
