<?php

namespace SicrediAPI;

use GuzzleHttp\Client as HttpClient;
use SicrediAPI\Domain\Token;

class Client
{
    public const BASE_URL = 'https://api-parceiro.sicredi.com.br';
    public const SANDBOX_BASE_URL = 'https://api-parceiro.sicredi.com.br/sb';

    private $apiKey;
    private $cooperative;
    private $post;
    private $beneficiaryCode;

    private $baseUrl;
    private $httpClient;
    private $environment;
    private $token;

    public function __construct(string $apiKey, string $cooperative, string $post, string $beneficiaryCode, HttpClient $httpClient, bool $useSandbox = false)
    {
        $this->apiKey = $apiKey;
        $this->cooperative = $cooperative;
        $this->post = $post;
        $this->beneficiaryCode = $beneficiaryCode;

        $this->httpClient = $httpClient;
        $this->environment = $useSandbox ? 'sandbox' : 'production';
        $this->baseUrl = $useSandbox ? self::SANDBOX_BASE_URL : self::BASE_URL;
    }

    public function authenticate($username, $password)
    {
        $response = $this->httpClient->post($this->baseUrl . '/auth/openapi/token', [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'context' => 'COBRANCA',
                'x-api-key' => $this->apiKey,
            ],
            'form_params' => [
                'username' => $username,
                'password' => $password,
                'scope' => 'cobranca',
                'grant_type' => 'password',
            ],
        ]);

        $data = json_decode($response->getBody(), true);

        $this->token = Token::fromArray($data);

        return true;
    }

    public function refreshToken()
    {
        $response = $this->httpClient->post($this->baseUrl . '/auth/openapi/token', [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'context' => 'COBRANCA',
                'x-api-key' => $this->apiKey,
            ],
            'form_params' => [
                'scope' => 'cobranca',
                'grant_type' => 'refresh_token',
                'refresh_token' => $this->token->getRefreshToken(),
            ],
        ]);

        $data = json_decode($response->getBody(), true);

        $this->token = Token::fromArray($data);

        return true;
    }

    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    public function getApiKey()
    {
        return $this->apiKey;
    }

    public function getToken(): Token
    {
        return $this->token;
    }

    public function getHttpClient()
    {
        return $this->httpClient;
    }

    public function getEnvironment()
    {
        return $this->environment;
    }

    public function getCooperative()
    {
        return $this->cooperative;
    }

    public function getPost()
    {
        return $this->post;
    }

    public function getBeneficiaryCode()
    {
        return $this->beneficiaryCode;
    }

    public function boleto()
    {
        return new Resources\Boleto($this);
    }
}
