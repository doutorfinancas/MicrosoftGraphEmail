<?php

namespace DoutorFinancas\MicrosoftGraphEmail\Services;

use DoutorFinancas\MicrosoftGraphEmail\ValueObject\MicrosoftAuthToken;
use DoutorFinancas\MicrosoftGraphEmail\ValueObject\MicrosoftConfig;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;

class RequestService
{
    /**
     * @var Client|ClientInterface|null
     */
    private $httpClient;
    /**
     * @var MicrosoftConfig
     */
    private $config;
    /**
     * @var MicrosoftAuthToken
     */
    private $token;
    /**
     * @var Request
     */
    private $request;

    public function __construct(
        MicrosoftConfig $config,
        MicrosoftAuthToken $token,
        ClientInterface $client = null
    )
    {
        if (is_null($client)) {
            $client = new Client();
        }

        $this->httpClient = $client;
        $this->config = $config;
        $this->token = $token;
    }

    /**
     * @param string $uri
     * @param string $requestMethod
     * @param $body
     * @return $this
     */
    public function createRequest(string $uri, string $requestMethod = 'GET', $body = null): self
    {
        $url = sprintf('%s%s', $this->config->getGraphBaseURL(), $uri);
        $this->request = new Request($requestMethod, $url, $this->defaultHeaders(), $body);
        return $this;
    }

    /**
     * @param string $filePath
     * @param string $uri
     * @param string $requestMethod
     * @return mixed
     * @throws ClientExceptionInterface
     */
    public function upload(string $filePath, string $uri, string $requestMethod = 'PUT')
    {
        $file = fopen($filePath, 'r');
        $fileContent = Utils::streamFor($file);

        return $this
            ->createRequest($uri, $requestMethod, $fileContent)
            ->execute()
        ;
    }

    /**
     * @return mixed
     * @throws ClientExceptionInterface
     */
    public function execute()
    {
        $response = $this->httpClient->sendRequest($this->request);
        return json_decode($response->getBody()->getContents());
    }

    private function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token->getTokenString()
        ];
    }
}