<?php

declare(strict_types=1);

namespace Yireo\ReplaceTools;

use Exception;

/**
 * Class Environment
 * @package Yireo\ReplaceTools
 */
class Environment
{
    /**
     * @return string
     * @throws Exception
     */
    static public function getAccountName(): string
    {
        $environment = self::getEnvironment();
        return $environment['GITHUB_ACCOUNT'];
    }

    /**
     * @return array
     * @throws Exception
     */
    static public function getEnvironment(): array
    {
        $environment = require(__DIR__ . '/../.env.php');

        if (empty($environment['GITHUB_ACCOUNT']) && empty($environment['GITHUB_ACCOUNT'])) {
            throw new Exception('GitHub account is missing.');
        }

        if (empty($environment['GITHUB_TOKEN']) && empty($environment['GITHUB_PASSWORD'])) {
            throw new Exception('GitHub token and password are missing. One is needed.');
        }

        return $environment;
    }
}
