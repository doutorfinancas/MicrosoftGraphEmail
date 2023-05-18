<?php

declare(strict_types=1);

namespace DoutorFinancas\MicrosoftGraphEmail\ValueObject;

use DoutorFinancas\MicrosoftGraphEmail\Exceptions\MicrosoftValidationException;

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

    /**
     * @param string $tenantId
     * @param string $clientId
     * @param string $secret
     * @param string $driverId
     * @param null|string $sharePointBaseUrl
     * @param null|string $sharePointUserSpaceName
     * @param string $graphEndpoint
     * @param string $graphApiVersion
     * @param string $authTokenURL
     * @param string $scope
     * @param string $grantType
     * @param string $authenticationFlow
     *
     * @throws MicrosoftValidationException
     */
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

        $this->validate();
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

    /**
     * @throws MicrosoftValidationException
     */
    private function validate(): void
    {
        $gidRegex = '/^\{?[a-f\d]{8}-(?:[a-f\d]{4}-){3}[a-f\d]{12}\}?$/i';

        if (empty($this->clientId) || empty($this->secret) || empty($this->tenantId)) {
            throw new MicrosoftValidationException('TenantId, ClientId and Secret should not be empty.');
        }

        if (!preg_match($gidRegex, $this->tenantId)) {
            throw new MicrosoftValidationException('TenantId should be a GUID.');
        }

        if (!preg_match($gidRegex, $this->clientId)) {
            throw new MicrosoftValidationException('ClientId should be a GUID.');
        }

        if ($this->tenantId === $this->clientId) {
            throw new MicrosoftValidationException('TenantId and ClientID cannot be equals.');
        }
    }
}
