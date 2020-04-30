<?php

declare(strict_types=1);

namespace Yireo\ReplaceTools;

use Exception;

/**
 * Class FilesystemResolver
 * @package Yireo\ReplaceTools
 */
class FilesystemResolver
{
    /**
     * @var FilesystemResolver
     */
    private static $instance;

    /**
     * @var string
     */
    private $rootFolder = '';

    /**
     * @return FilesystemResolver
     */
    public static function getInstance(): FilesystemResolver
    {
        if (self::$instance === null) {
            self::$instance = new FilesystemResolver();
        }

        return self::$instance;
    }

    /**
     * @return string
     */
    public function getRootFolder(): string
    {
        return $this->rootFolder;
    }

    /**
     * @param string $rootFolder
     * @throws Exception
     */
    public function setRootFolder(string $rootFolder): void
    {
        if (!is_dir($rootFolder)) {
            throw new Exception('Folder "' . $rootFolder . '" does not exist');
        }

        $this->rootFolder = $rootFolder;
    }
}
