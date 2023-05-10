<?php

namespace SicrediAPI\Domain;

class Token
{
    private $scope;
    private $accessToken;
    private $refreshToken;
    private $expiresIn;
    private $refreshExpiresIn;
    private $tokenType;
    private $idToken;
    private $notBeforePolicy;
    private $sessionState;
    private $issuedAt;

    public function __construct(
        string $scope,
        string $accessToken,
        string $refreshToken,
        int $expiresIn,
        int $refreshExpiresIn,
        string $tokenType,
        string $idToken = null,
        int $notBeforePolicy = null,
        string $sessionState = null
    ) {
        $this->scope = $scope;
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
        $this->expiresIn = $expiresIn;
        $this->refreshExpiresIn = $refreshExpiresIn;
        $this->tokenType = $tokenType;
        $this->idToken = $idToken;
        $this->notBeforePolicy = $notBeforePolicy;
        $this->sessionState = $sessionState;
        $this->issuedAt = time();
    }

    public function getScope(): string
    {
        return $this->scope;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getExpiresIn(): int
    {
        return $this->expiresIn;
    }

    public function getRefreshExpiresIn(): int
    {
        return $this->refreshExpiresIn;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function getIdToken(): string
    {
        return $this->idToken;
    }

    public function getTokenType(): string
    {
        return $this->tokenType;
    }

    public function getNotBeforePolicy(): int
    {
        return $this->notBeforePolicy;
    }

    public function getSessionState(): string
    {
        return $this->sessionState;
    }

    public function isAccessTokenExpired(): bool
    {
        return time() > $this->issuedAt + $this->expiresIn;
    }

    public function isRefreshTokenExpired(): bool
    {
        return time() > $this->issuedAt + $this->refreshExpiresIn;
    }

    public static function fromArray(array $data): Token
    {
        return new Token(
            $data['scope'],
            $data['access_token'],
            $data['refresh_token'],
            $data['expires_in'],
            $data['refresh_expires_in'],
            $data['token_type'],
            $data['id_token'],
            $data['not-before-policy'],
            $data['session_state']
        );
    }
}
