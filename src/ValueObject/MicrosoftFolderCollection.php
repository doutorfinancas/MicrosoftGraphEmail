<?php

namespace DoutorFinancas\Utilities\ValueObject;

class MicrosoftFolderCollection
{
    private $list;

    public function __construct()
    {
        $this->list = [];
    }

    /**
     * @param MicrosoftFolder $folder
     * @return void
     */
    public function add(MicrosoftFolder $folder): void
    {
        $this->list[$folder->getName()] = $folder;
    }

    /**
     * @param MicrosoftFolder $folder
     * @return void
     */
    public function remove(MicrosoftFolder $folder): void
    {
        unset($this->list[$folder->getName()]);
    }

    /**
     * @param string $name
     * @return MicrosoftFolder | null
     */
    public function findByName(string $name): mixed
    {
        if (isset($this->list[$name])) {
            var_dump($this->list[$name]);
            return $this->list[$name];
        }

        return null;
    }
}
