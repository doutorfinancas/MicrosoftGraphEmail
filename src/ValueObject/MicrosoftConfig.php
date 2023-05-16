<?php

declare(strict_types=1);

namespace DoutorFinancas\MicrosoftGraphEmail\ValueObject;

final class MicrosoftConfig
{
    /**
     * @var string
     */
    private $tenantId;

    /**
     * @var string
     */
    private $clientId;

    /**
     * @var string
     */
    private $secret;

    /**
     * @var string
     */
    private $driverId;

    /**
     * @var string
     */
    private $graphEndpoint;

    /**
     * @var string
     */
    private $authTokenURL;

    /**
     * @var string
     */
    private $scope;

    /**
     * @var string
     */
    private $grantType;

    /**
     * @var string
     */
    private $authenticationFlow;

    /**
     * @var string
     */
    private $graphApiVersion;

    /**
     * @var null|string
     */
    private $sharePointBaseUrl;

    /**
     * @var null|string
     */
    private $sharePointUserSpaceName;

    public function __construct(
        string $tenantId,
        string $clientId,
        string $secret,
        string $driverId,
        string $sharePointBaseUrl = null,
        string $sharePointUserSpaceName = null,
        string $graphEndpoint = 'https://graph.microsoft.com',
        string $graphApiVersion = 'v1.0',
        string $authTokenURL = 'https://login.microsoftonline.com/%s/oauth2/v2.0/token',
        string $scope = 'https://graph.microsoft.com/.default',
        string $grantType = 'client_credentials',
        string $authenticationFlow = 'application' // just to know, not used in any place yet.
    ) {
        $this->tenantId = $tenantId;
        $this->clientId = $clientId;
        $this->secret = $secret;
        $this->driverId = $driverId;
        $this->graphEndpoint = $graphEndpoint;
        $this->graphApiVersion = $graphApiVersion;
        $this->authTokenURL = $authTokenURL;
        $this->scope = $scope;
        $this->grantType = $grantType;
        $this->authenticationFlow = $authenticationFlow;
        $this->sharePointBaseUrl = $sharePointBaseUrl;
        $this->sharePointUserSpaceName = $sharePointUserSpaceName;
    }

    /**
     * @return string
     */
    public function getTenantId(): string
    {
        return $this->tenantId;
    }

    /**
     * @return string
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * @return string
     */
    public function getSecret(): string
    {
        return $this->secret;
    }

    /**
     * @return string
     */
    public function getDriverId(): string
    {
        return $this->driverId;
    }

    /**
     * @return string
     */
    public function getGraphBaseURL(): string
    {
        return sprintf('%s/%s', $this->graphEndpoint, $this->graphApiVersion);
    }

    /**
     * @return string
     */
    public function getAuthTokenURL(): string
    {
        return $this->authTokenURL;
    }

    /**
     * @return string
     */
    public function getScope(): string
    {
        return $this->scope;
    }

    /**
     * @return string
     */
    public function getGrantType(): string
    {
        return $this->grantType;
    }

    /**
     * @return string
     */
    public function getAuthenticationFlow(): string
    {
        return $this->authenticationFlow;
    }

    /**
     * @return null|string
     */
    public function getSharePointBaseUrl(): ?string
    {
        return $this->sharePointBaseUrl;
    }

    /**
     * @return null|string
     */
    public function getSharePointUserSpaceName(): ?string
    {
        return $this->sharePointUserSpaceName;
    }
}
