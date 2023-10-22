<?php declare(strict_types=1);

namespace Yireo\ReplaceTools\Test\Unit\Fixture;

class ComposerJsonFixture
{
    /**
     * @var string[]
     */
    private array $require = [];

    /**
     * @var array
     */
    private array $replace = [];

    /**
     * @var array
     */
    private array $bulkReplace = [];

    /**
     * @var array
     */
    private array $includeReplace = [];

    /**
     * @var array
     */
    private array $excludeReplace = [];

    /**
     * @param string $packageName
     * @param string $version
     * @return $this
     */
    public function addRequire(string $packageName, string $version = '@dev'): ComposerJsonFixture
    {
        $this->require[$packageName] = $version;
        return $this;
    }

    /**
     * @param string $packageName
     * @param string $version
     * @return $this
     */

    public function addReplace(string $packageName, string $version = '*'): ComposerJsonFixture
    {
        $this->replace[$packageName] = $version;
        return $this;
    }

    /**
     * @param string $packageName
     * @return $this
     */
    public function addBulkReplace(string $packageName): ComposerJsonFixture
    {
        $this->bulkReplace[] = $packageName;
        return $this;
    }

    /**
     * @param string $packageName
     * @param string $version
     * @return $this
     */
    public function addIncludeReplace(string $packageName, string $version = '*'): ComposerJsonFixture
    {
        $this->includeReplace[$packageName] = $version;
        return $this;
    }

    /**
     * @param string $packageName
     * @param string $version
     * @return $this
     */
    public function addExcludeReplace(string $packageName, string $version = '*'): ComposerJsonFixture
    {
        $this->excludeReplace[$packageName] = $version;
        return $this;
    }

    /**
     * @return string
     */
    public function output(): string
    {
        $data = [
            'require' => $this->require,
            'replace' => $this->replace,
            'extra' => [
                'replace' => [
                    'bulk' => $this->bulkReplace,
                    'include' => $this->includeReplace,
                    'exclude' => $this->excludeReplace,
                ]
            ]
        ];

        return json_encode($data, JSON_PRETTY_PRINT + JSON_UNESCAPED_SLASHES);
    }
}
