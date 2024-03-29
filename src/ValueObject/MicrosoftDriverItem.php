<?php

declare(strict_types=1);

namespace DoutorFinancas\MicrosoftGraphEmail\ValueObject;

final class MicrosoftDriverItem
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $webUrl;

    /**
     * @var string
     */
    private $eTag;

    /**
     * @var string
     */
    private $embedUrl;

    /**
     * @var string
     */
    private $userSpace;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var null|string
     */
    private $downloadUrl;

    public function __construct(
        string $id,
        string $eTag,
        string $name,
        string $webUrl,
        string $userSpace,
        string $baseUrl,
        string $downloadUrl = null
    ) {
        $this->id = $id;
        $this->eTag = $eTag;
        $this->name = $name;
        $this->webUrl = $webUrl;
        $this->userSpace = $userSpace;
        $this->baseUrl = $baseUrl;

        $this->configure();
        $this->downloadUrl = $downloadUrl;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getWebUrl(): string
    {
        return $this->webUrl;
    }

    public function getEmbedUrl(): string
    {
        return $this->embedUrl;
    }

    /**
     * @return null|string
     */
    public function getDownloadUrl(): ?string
    {
        return preg_replace('/&tempauth(=[^&]*)?|^foo(=[^&]*)?&?/', '', $this->downloadUrl);
    }

    public function getDownloadUrlWithTmpAuthParameter(): ?string
    {
        return $this->downloadUrl;
    }

    private function configure(): void
    {
        $this->createEmbedUrl();
    }

    private function createEmbedUrl(): void
    {
        $url = <<<'URL'
            %s/personal/%s/_layouts/15/Doc.aspx
            ?sourcedoc={%s}
            &action=embedview
            &AllowTyping=True
            &wdInConfigurator=True
            &wdInConfigurator=True
            &edesNext=false
            &resen=true
            &ed1JS=false
URL;

        preg_match('/[A-Z-0-9]{36}/', $this->eTag, $matches);
        $this->embedUrl = sprintf(
            preg_replace('/\s+/', '', $url),
            $this->baseUrl,
            $this->userSpace,
            $matches[0]
        );
    }
}
