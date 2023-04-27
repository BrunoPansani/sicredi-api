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
    private Client $client;

    protected function setUp(): void
    {
        $this->faker = Factory::create();

        // Create a mock HTTP client
        $this->httpMockHandler = new MockHandler([]);

        $handlerStack = HandlerStack::create($this->httpMockHandler);
        $client = new \GuzzleHttp\Client(['handler' => $handlerStack]);

        // Create a new instance of the Client class
        $this->client = new Client(
            'API-KEY',
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
        $this->assertSame('https://api-parceiro.sicredi.com.br', $baseUrl);
    }

    public function testGetApiKey()
    {
        $apiKey = $this->client->getApiKey();
        $this->assertSame('API-KEY', $apiKey);
    }

    /**
     * @covers \SicrediAPI\Client::__construct
     * @covers \SicrediAPI\Client::authenticate
     * @covers \SicrediAPI\Client::getToken
     * @covers \SicrediAPI\Domain\Token::__construct
     * @covers \SicrediAPI\Domain\Token::fromArray
     * @covers \SicrediAPI\Domain\Token::getAccessToken
     * @covers \SicrediAPI\Domain\Token::getExpiresIn
     * @covers \SicrediAPI\Domain\Token::getRefreshExpiresIn
     * @covers \SicrediAPI\Domain\Token::getRefreshToken
     *
     * @return void
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function testAuthenticate()
    {
        // Generate a random username and password
        $username = $this->faker->username();
        $password = $this->faker->password();

        // Generate a random access token and refresh token
        $accessToken = $this->faker->md5;
        $refreshToken = $this->faker->md5;
        $expiresIn = 3600;
        $refreshExpiresIn = 86400;

        // Mock the HTTP response
        $response = new Response(200, [], json_encode([
            'scope' => 'cobranca',
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_in' => $expiresIn,
            'refresh_expires_in' => $refreshExpiresIn,
            'token_type' => 'Bearer',
            'id_token' => $this->faker->md5,
            'not-before-policy' => 0,
            'session_state' => $this->faker->md5,
        ]));

        $this->httpMockHandler->append($response);

        // Call the authenticate method
        $this->client->authenticate($username, $password);

        $token = $this->client->getToken();

        // Check that the access token and refresh token are set correctly
        $this->assertSame($accessToken, $token->getAccessToken());
        $this->assertSame($refreshToken, $token->getRefreshToken());
        $this->assertSame($expiresIn, $token->getExpiresIn());
        $this->assertSame($refreshExpiresIn, $token->getRefreshExpiresIn());
    }

    /**
     * @covers \SicrediAPI\Client::__construct
     * @covers \SicrediAPI\Client::authenticate
     * @covers \SicrediAPI\Client::refreshToken
     * @covers \SicrediAPI\Client::getToken
     * @covers \SicrediAPI\Domain\Token::__construct
     * @covers \SicrediAPI\Domain\Token::fromArray
     * @covers \SicrediAPI\Domain\Token::getAccessToken
     * @covers \SicrediAPI\Domain\Token::getExpiresIn
     * @covers \SicrediAPI\Domain\Token::getIdToken
     * @covers \SicrediAPI\Domain\Token::getNotBeforePolicy
     * @covers \SicrediAPI\Domain\Token::getRefreshExpiresIn
     * @covers \SicrediAPI\Domain\Token::getRefreshToken
     * @covers \SicrediAPI\Domain\Token::getScope
     * @covers \SicrediAPI\Domain\Token::getSessionState
     * @covers \SicrediAPI\Domain\Token::getTokenType
     * @covers \SicrediAPI\Domain\Token::isAccessTokenExpired
     * @covers \SicrediAPI\Domain\Token::isRefreshTokenExpired
     *
     * @return void
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function testRefreshToken()
    {
        $expectedToken = [
            'scope' => 'cobranca',
            'access_token' => $this->faker->md5,
            'refresh_token' => $this->faker->md5,
            'expires_in' => 3600,
            'refresh_expires_in' => 86400,
            'token_type' => 'Bearer',
            'id_token' => $this->faker->md5,
            'not-before-policy' => 0,
            'session_state' => $this->faker->md5,
        ];

        // Mock the HTTP response
        $response = new Response(200, [], json_encode($expectedToken));

        $this->httpMockHandler->append($response);

        $this->client->authenticate($this->faker->username(), $this->faker->password());

        $newExpectedToken = array_merge($expectedToken, [
            'refresh_token' => $this->faker->md5,
        ]);

        $response = new Response(200, [], json_encode($newExpectedToken));

        $this->httpMockHandler->append($response);

        // Call the refreshToken method
        $this->client->refreshToken();

        $token = $this->client->getToken();

        $this->assertSame($newExpectedToken['access_token'], $token->getAccessToken());
        $this->assertSame($newExpectedToken['refresh_token'], $token->getRefreshToken());
        $this->assertSame($newExpectedToken['expires_in'], $token->getExpiresIn());
        $this->assertSame($newExpectedToken['refresh_expires_in'], $token->getRefreshExpiresIn());
        $this->assertSame($newExpectedToken['token_type'], $token->getTokenType());
        $this->assertSame($newExpectedToken['id_token'], $token->getIdToken());
        $this->assertSame($newExpectedToken['not-before-policy'], $token->getNotBeforePolicy());
        $this->assertSame($newExpectedToken['session_state'], $token->getSessionState());
        $this->assertSame($newExpectedToken['scope'], $token->getScope());
        $this->assertSame(false, $token->isAccessTokenExpired());
        $this->assertSame(false, $token->isRefreshTokenExpired());
    }
}
