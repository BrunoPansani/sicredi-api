<?php

namespace SicrediAPI\Resources;

use SicrediAPI\Client;

abstract class ResourceAbstract
{
    protected $apiClient;

    protected $additionalDefaultHeaders = [];

    public function __construct(Client $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    private function buildRequestOptions($options)
    {
        $headers = [
            'x-api-key' => $this->apiClient->getApiKey(),
            'Authorization' => 'Bearer ' . $this->apiClient->getToken()->getAccessToken(),
            'content-type' => 'application/json',
        ];

        if (isset($options['headers'])) {
            $headers = array_merge($headers, $options['headers'], $this->additionalDefaultHeaders);
            unset($options['headers']);
        }

        return array_merge($options, ['headers' => $headers]);
    }

    protected function checkToken()
    {
        if ($this->apiClient->getToken()->isAccessTokenExpired()) {
            $this->apiClient->refreshToken();
        }
    }

    public function get($url, $options = [])
    {
        $this->checkToken();

        return $this->apiClient->getHttpClient()->get($this->apiClient->getBaseUrl() . $url, $this->buildRequestOptions($options));
    }

    public function post($url, $options = [])
    {
        $this->checkToken();

        try {
            return $this->apiClient->getHttpClient()->post($this->apiClient->getBaseUrl() . $url, $this->buildRequestOptions($options));
        } catch (\GuzzleHttp\Exception\ClientException $th) {
            $response = $th->getResponse();
            return $response;
        }
    }

    public function put($url, $options = [])
    {
        $this->checkToken();

        return $this->apiClient->getHttpClient()->put($this->apiClient->getBaseUrl() . $url, $this->buildRequestOptions($options));
    }

    public function delete($url, $options = [])
    {
        $this->checkToken();

        return $this->apiClient->getHttpClient()->delete($this->apiClient->getBaseUrl() . $url, $this->buildRequestOptions($options));
    }

}