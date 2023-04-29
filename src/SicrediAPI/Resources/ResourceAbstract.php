<?php

namespace SicrediAPI\Resources;

use DateTime;
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

    protected function get($url, $options = [])
    {
        $this->checkToken();

        $response = $this->apiClient->getHttpClient()->get($this->apiClient->getBaseUrl() . $url, $this->buildRequestOptions($options));

        return $this->response($response);
    }

    protected function post($url, $options = [])
    {
        $this->checkToken();

        try {
            $response = $this->apiClient->getHttpClient()->post($this->apiClient->getBaseUrl() . $url, $this->buildRequestOptions($options));
        } catch (\GuzzleHttp\Exception\ClientException $th) {
            $response = $th->getResponse();
            return $response;
        }

        return $this->response($response);
    }

    protected function put($url, $options = [])
    {
        $this->checkToken();

        $response = $this->apiClient->getHttpClient()->put($this->apiClient->getBaseUrl() . $url, $this->buildRequestOptions($options));

        return $this->response($response);
    }

    protected function delete($url, $options = [])
    {
        $this->checkToken();

        $response = $this->apiClient->getHttpClient()->delete($this->apiClient->getBaseUrl() . $url, $this->buildRequestOptions($options));

        return $this->response($response);
    }

    private function response($response)
    {
        $response = json_decode($response->getBody()->getContents(), true);

        return $this->sandboxResponseFixes($response);
    }

    private function sandboxResponseFixes($response)
    {
        if ($this->apiClient->getEnvironment() !== 'sandbox') {
            return $response;
        }

        /**
         * Payees must not have the same document key as the beneficiary
         * Sadly, the Sandbox API's Query response doesn't obey by the same rules as the Create response
         */
        if (isset($response['pagador']['documento'])) {
            $response['pagador']['documento'] = '98765432100';
        }

        /**
         * For each object inside the 'descontos' array, the 'dataLimite' should start with 2021-08-26 and add 2 days for each object
         */
        if (isset($response['descontos'])) {
            $date = DateTime::createFromFormat('Y-m-d', $response['dataEmissao']);
            foreach ($response['descontos'] as $key => $desconto) {
                $date->add(new \DateInterval('P2D'));
                $response['descontos'][$key]['dataLimite'] = $date->format('Y-m-d');
            }
        }

        return $response;
    }

}
