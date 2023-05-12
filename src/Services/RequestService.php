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
     * @var string
     */
    private $url;
    /**
     * @var string
     */
    private $requestMethod;
    /**
     * @var mixed|null
     */
    private $requestBody;

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
     * @param $requestBody
     * @return $this
     */
    public function createRequest(string $uri, string $requestMethod = 'GET', $requestBody = null): self
    {
        $this->url = sprintf('%s%s', $this->config->getGraphBaseURL(), $uri);
        $this->requestMethod = $requestMethod;
        $this->requestBody = $requestBody;
        return $this;
    }

    /**
     * @param string $filePath
     * @return mixed
     * @throws ClientExceptionInterface
     */
    public function upload(string $filePath)
    {
        $file = fopen($filePath, 'r');
        $this->requestBody = Utils::streamFor($file);
        return $this->execute();
    }

    /**
     * @return mixed
     * @throws ClientExceptionInterface
     */
    public function execute()
    {
        $request = new Request($this->requestMethod, $this->url, $this->defaultHeaders(), $this->requestBody);
        $response = $this->httpClient->sendRequest($request);
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