<?php

namespace SicrediAPI\Tests;

use PHPUnit\Framework\TestCase;
use SicrediAPI\Client;
use SicrediAPI\Resources\Boleto;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use Faker\Factory;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;

class ClientTest extends TestCase
{
    private $faker;
    private $httpClient;
    private $httpMockHandler;
    private $client;

    protected function setUp(): void
    {
        $this->faker = Factory::create();

        // Create a mock HTTP client
        $this->httpMockHandler = new MockHandler([]);
        
        $handlerStack = HandlerStack::create($this->httpMockHandler);
        $client = new \GuzzleHttp\Client(['handler' => $handlerStack]);

        // Create a new instance of the Client class
        $this->client = new Client(
            'api-key',
            '9999',
            '99',
            '99999',
            $client
        );
    }

    public function testGetHttpClient()
    {
        $httpClient = $this->client->getHttpClient();
        $this->assertInstanceOf(ClientInterface::class, $httpClient);
    }

    public function testGetBaseUrl()
    {
        $baseUrl = $this->client->getBaseUrl();
        $this->assertSame('https://example.com/api', $baseUrl);
    }

    public function testGetApiKey()
    {
        $apiKey = $this->client->getApiKey();
        $this->assertSame('API_KEY', $apiKey);
    }

    /**
     * @covers \SicrediAPI\Client::getAccessToken
     * @covers \SicrediAPI\Client::getRefreshToken
     * @covers \SicrediAPI\Client::getExpiresIn
     * @covers \SicrediAPI\Client::getRefreshExpiresIn
     * @covers \SicrediAPI\Client::getAuthenticatedAt
     * 
     * @return void 
     * @throws InvalidArgumentException 
     * @throws ExpectationFailedException 
     */
    public function testAuthenticate()
    {
        // Generate a random username and password
        $username = $this->faker->userName;
        $password = $this->faker->password;

        // Generate a random access token and refresh token
        $accessToken = $this->faker->md5;
        $refreshToken = $this->faker->md5;
        $expiresIn = 3600;
        $refreshExpiresIn = 86400;

        // Mock the HTTP response
        $response = new Response(200, [], json_encode([
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_in' => $expiresIn,
            'refresh_expires_in' => $refreshExpiresIn,
        ]));

        $this->httpMockHandler->append($response);

        // Call the authenticate method
        $this->client->authenticate($username, $password);

        // Check that the access token and refresh token are set correctly
        $this->assertSame($accessToken, $this->client->getAccessToken());
        $this->assertSame($refreshToken, $this->client->getRefreshToken());
        $this->assertSame($expiresIn, $this->client->getExpiresIn());
        $this->assertSame($refreshExpiresIn, $this->client->getRefreshExpiresIn());
        
    }

    public function testRefreshToken()
    {

        // Generate a random access token and refresh token
        $accessToken = $this->faker->md5;
        $newRefreshToken = $this->faker->md5;
        $expiresIn = 3600;
        $refreshExpiresIn = 86400;

        // Mock the HTTP response
        $response = new Response(200, [], json_encode([
            'access_token' => $accessToken,
            'refresh_token' => $newRefreshToken,
            'expires_in' => $expiresIn,
            'refresh_expires_in' => $refreshExpiresIn,
        ]));

        $this->httpMockHandler->append($response);

        // Call the refreshToken method
        $this->client->refreshToken();

        $this->assertSame($accessToken, $this->client->getAccessToken());
        $this->assertSame($newRefreshToken, $this->client->getRefreshToken());
        $this->assertSame($expiresIn, $this->client->getExpiresIn());
        $this->assertSame($refreshExpiresIn, $this->client->getRefreshExpiresIn());
    }
}

