<?php declare(strict_types=1);

namespace Yireo\ReplaceTools\Composer\Model;


class Replacement
{
    private string $composerName;
    private string $version;

    /**
     * @param string $composerName
     * @param string $version
     */
    public function __construct(
        string $composerName,
        string $version = '*'
    ) {
        $this->composerName = $composerName;
        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getComposerName(): string
    {
        return $this->composerName;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }
}
