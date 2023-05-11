<?php

namespace DoutorFinancas\MicrosoftGraphEmail\Services;

use DoutorFinancas\MicrosoftGraphEmail\ValueObject\MicrosoftConfig;
use DoutorFinancas\MicrosoftGraphEmail\ValueObject\MicrosoftFolder;
use Psr\Http\Client\ClientExceptionInterface;

class MicrosoftDriveService
{
    /**
     * @var RequestService
     */
    private $requestService;
    /**
     * @var MicrosoftConfig
     */
    private $config;

    public function __construct(MicrosoftConfig $config, RequestService $requestService)
    {
        $this->requestService = $requestService;
        $this->config = $config;
    }

    public function uploadFile(MicrosoftFolder $folder, string $filename, string $filePath)
    {
        try {
            // without base url.
            $uri = sprintf(
                '/drives/%s/items/root:/%s/%s:/content',
                $this->config->getDriverId(),
                $folder->getId(),
                $filename
            );

            $response = $this->requestService->upload($filePath, $uri);

            var_dump($response);


        } catch (ClientExceptionInterface $e) {
            echo json_decode($e->getMessage()) . PHP_EOL;
            return '';
        }
    }
}