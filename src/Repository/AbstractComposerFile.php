<?php

declare(strict_types=1);

namespace Yireo\ReplaceTools\Repository;

use Yireo\ReplaceTools\Repository;

/**
 * Class AbstractComposerFile
 * @package Yireo\ReplaceTools\Repository
 */
abstract class AbstractComposerFile
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @var string
     */
    protected $branch;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * ComposerFile constructor.
     * @param Repository $repository
     * @param string $branch
     */
    public function __construct(Repository $repository, string $branch)
    {
        $this->repository = $repository;
        $this->branch = $branch;
        $this->load();
    }

    /**
     * @return array
     */
    abstract protected function load(): array;

    /**
     * @return array
     */
    protected function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    protected function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getRequire(): array
    {
        $data = $this->getData();
        if (isset($data['require'])) {
            return $data['require'];
        }

        return [];
    }

    /**
     * @return array
     */
    public function getReplace(): array
    {
        $data = $this->getData();
        if (isset($data['replace'])) {
            return $data['replace'];
        }

        return [];
    }

    /**
     * @param array $replace
     */
    public function setReplace(array $replace): void
    {
        $this->data['replace'] = $replace;
    }

    /**
     * @return string
     */
    public function getContents(): string
    {
        return json_encode($this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
