<?php

declare(strict_types=1);

namespace DoutorFinancas\MicrosoftGraphEmail\Services;

use DoutorFinancas\MicrosoftGraphEmail\ValueObject\MicrosoftAuthToken;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;

class MicrosoftAuthService
{
    protected $url;

    protected $tenantId;

    protected $clientId;

    protected $secret;

    protected $httpClient;

    /**
     * @param ClientInterface $httpClient
     * @param string $tenantId
     * @param string $clientId
     * @param string $secret
     * @param string $url default https://login.microsoftonline.com/{tenantId}/oauth2/v2.0/token
     */
    public function __construct(
        ClientInterface $httpClient,
        string $tenantId,
        string $clientId,
        string $secret,
        string $url = 'https://login.microsoftonline.com/%s/oauth2/v2.0/token'
    ) {
        $this->httpClient = $httpClient;
        $this->tenantId = $tenantId;
        $this->clientId = $clientId;
        $this->secret = $secret;
        $this->url = $url;
        if (str_contains($url, '%s')) {
            $this->url = sprintf(
                'https://login.microsoftonline.com/%s/oauth2/v2.0/token',
                $this->tenantId
            );
        }
    }

    /**
     * @param string $scope
     * @param string $grantType
     *
     * @return bool|MicrosoftAuthToken
     */
    public function getToken(
        string $scope = 'https://graph.microsoft.com/.default',
        string $grantType = 'client_credentials'
    ) {
        try {
            $request = new Request(
                'POST',
                $this->url,
                [],
                http_build_query([
                    'client_id' => $this->clientId,
                    'client_secret' => $this->secret,
                    'scope' => $scope,
                    'grant_type' => $grantType,
                ])
            );

            $response = $this->httpClient->sendRequest($request);
            $token = json_decode($response->getBody()->getContents());

            return new MicrosoftAuthToken($this, $token->access_token, $token->expires_in);
        } catch (ClientExceptionInterface $e) {
            echo $e->getTraceAsString();

            return false;
        }
    }
}
