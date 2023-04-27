<?php

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$client = new \SicrediAPI\Client($_ENV['SICREDI_API_KEY'], new \GuzzleHttp\Client(), true);

if ($client->authenticate($_ENV['SICREDI_USERNAME'], $_ENV['SICREDI_PASSWORD'])) {
    echo "Authenticated successfully\n";
} else {
    echo "Authentication failed\n";
};
