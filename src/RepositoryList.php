<?php declare(strict_types=1);

namespace Yireo\ReplaceTools;

use Exception;
use RuntimeException;

/**
 * Class RepositoryList
 * @package Yireo\ReplaceTools
 */
class RepositoryList
{
    /**
     * @return string[]
     */
    static public function getRepositoryNames(): array
    {
        $repositoryNames = [
            'magento2-replace-all',
            'magento2-replace-content-staging',
            'magento2-replace-core',
            'magento2-replace-bundled',
            'magento2-replace-graphql',
            'magento2-replace-inventory',
            'magento2-replace-sample-data',
        ];

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
        foreach (self::getRepositoryNames($includeAll) as $repositoryName) {
            $repositories[] = new Repository($repositoryName, Environment::getAccountName());
        }
        return $repositories;
    }

    /**
     * @param string $repositoryName
     * @return Repository
     * @throws Exception
     */
    static public function getRepositoryByName(string $repositoryName): Repository
    {
        foreach(self::getRepositories(true) as $repository) {
            if ($repository->getName() === $repositoryName) {
                return $repository;
            }
        }

        throw new RuntimeException('Invalid repository name');
    }
}
