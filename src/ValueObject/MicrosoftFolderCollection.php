<?php

declare(strict_types=1);

namespace DoutorFinancas\MicrosoftGraphEmail\ValueObject;

class MicrosoftFolderCollection
{
    private $list;

    public function __construct()
    {
        $this->list = [];
    }

    /**
     * @param MicrosoftFolder $folder
     */
    public function add(MicrosoftFolder $folder): void
    {
        $this->list[$folder->getName()] = $folder;
    }

    /**
     * @param MicrosoftFolder $folder
     */
    public function remove(MicrosoftFolder $folder): void
    {
        unset($this->list[$folder->getName()]);
    }

    /**
     * @param string $name
     *
     * @return null|MicrosoftFolder
     */
    public function findByName(string $name): ?MicrosoftFolder
    {
        if (isset($this->list[$name])) {
            return $this->list[$name];
        }

        return null;
    }
}
