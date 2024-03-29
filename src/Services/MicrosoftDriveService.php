<?php

declare(strict_types=1);

namespace DoutorFinancas\MicrosoftGraphEmail\Services;

use DoutorFinancas\MicrosoftGraphEmail\Exceptions\MicrosoftDriverException;
use DoutorFinancas\MicrosoftGraphEmail\Exceptions\MicrosoftRequestException;
use DoutorFinancas\MicrosoftGraphEmail\ValueObject\MicrosoftConfig;
use DoutorFinancas\MicrosoftGraphEmail\ValueObject\MicrosoftDriverItem;
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

    /**
     * @param MicrosoftFolder $folder
     * @param string $filename
     * @param string $filePath
     *
     * @throws MicrosoftRequestException
     * @throws MicrosoftDriverException
     *
     * @return null|MicrosoftDriverItem
     */
    public function uploadFile(MicrosoftFolder $folder, string $filename, string $filePath): ?MicrosoftDriverItem
    {
        try {
            // without base url.
            $uri = sprintf(
                '/drives/%s/items/root:/%s/%s:/content',
                $this->config->getDriverId(),
                $folder->getId(),
                $filename
            );

            $response = $this
                ->requestService
                ->createRequest($uri, 'PUT')
                ->upload($filePath)
            ;

            if (
                !property_exists($response, '@odata.context')
                && property_exists($response, 'error')
            ) {
                throw new MicrosoftDriverException(
                    sprintf('[Upload Error]: (%s) %s', $response->error->code, $response->error->message)
                );
            }

            return new MicrosoftDriverItem(
                $response->id,
                $response->eTag,
                $response->name,
                $response->webUrl,
                $this->config->getSharePointUserSpaceName(),
                $this->config->getSharePointBaseUrl(),
                $response->{'@microsoft.graph.downloadUrl'}
            );
        } catch (ClientExceptionInterface $e) {
            throw new MicrosoftRequestException($e->getMessage(), $e->getCode());
        }
    }
}
