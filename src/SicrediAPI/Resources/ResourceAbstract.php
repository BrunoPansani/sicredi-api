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

    private function response(\Psr\Http\Message\ResponseInterface $response)
    {
        if ($response->getHeader('Content-Type')[0] != 'application/json') {
            return $response->getBody()->getContents();
        }

        $response = json_decode($response->getBody()->getContents(), true);

        return $this->sandboxResponseFixes($response);
    }

    /**
     * Fix some issues with sandbox responses because it doesn't conform to the API documentation
     * This needs a better solution in the future but for now it's ok
     *
     * @param array $response
     * @return array
     */
    private function sandboxResponseFixes($response)
    {
        if ($this->apiClient->getEnvironment() !== 'sandbox') {
            return $response;
        }

        /**
         * Payees must not have the same document key as the beneficiary
         */
        if (isset($response['pagador']['documento'])) {
            $response['pagador']['documento'] = '98765432100';
        }

        /**
         * Discount dates must be between issuance date and due date, and must be in crescent order
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
