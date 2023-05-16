<?php

declare(strict_types=1);

namespace DoutorFinancas\MicrosoftGraphEmail\ValueObject;

class MicrosoftDriverItem
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

    public function __construct(
        string $id,
        string $eTag,
        string $name,
        string $webUrl,
        string $userSpace,
        string $baseUrl
    ) {
        $this->id = $id;
        $this->eTag = $eTag;
        $this->name = $name;
        $this->webUrl = $webUrl;
        $this->userSpace = $userSpace;
        $this->baseUrl = $baseUrl;

        $this->configure();
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
