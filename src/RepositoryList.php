<?php

declare(strict_types=1);

namespace Yireo\ReplaceTools;

use Exception;

/**
 * Class RepositoryList
 * @package Yireo\ReplaceTools
 */
class RepositoryList
{
    /**
     * @return string[]
     */
    static public function getRepositoryNames(bool $includeAll = false): array
    {
        $repositoryNames = [
            'magento2-replace-core',
            'magento2-replace-bundled',
            'magento2-replace-graphql',
            'magento2-replace-inventory',
            'magento2-replace-content-staging',
        ];

        if ($includeAll) {
            $repositoryNames[] = 'magento2-replace-all';
        }

        return $repositoryNames;
    }

    /**
     * @param bool $includeAll
     * @return Repository[]
     * @throws Exception
     */
    static public function getRepositories(bool $includeAll = false): array
    {
        $repositories = [];
        foreach (self::getRepositoryNames() as $repositoryName) {
            $repositories[] = new Repository($repositoryName);
        }
        return $repositories;
    }
}
