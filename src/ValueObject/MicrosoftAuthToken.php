<?php

namespace DoutorFinancas\Utilities\ValueObject;

use DoutorFinancas\Utilities\Services\MicrosoftAuthService;

class MicrosoftAuthToken
{
    protected $service;
    protected $token;
    protected $expiration;

    public function __construct(MicrosoftAuthService $service, string $token, int $expiresIn)
    {
        $this->service = $service;
        $this->token = $token;
        $this->expiration = time() + $expiresIn;
    }

    public function getTokenString()
    {
        if (time() > $this->expiration) {
            $that = $this->service->getToken();

            $this->token = $that->token;
            $this->expiration = $that->expiration;
        }

        return $this->token;
    }
}
