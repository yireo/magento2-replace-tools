<?php
declare(strict_types=1);

use Yireo\ReplaceTools\FilesystemResolver;
use Yireo\ReplaceTools\MagentoVersions;
use Yireo\ReplaceTools\RepositoryList;

require_once __DIR__ . '/vendor/autoload.php';
FilesystemResolver::getInstance()->setRootFolder(dirname(__DIR__));

$repositoryName = (string)$argv[1];
if (!in_array($repositoryName,RepositoryList::getRepositories(true))) {
    throw new RuntimeException('Invalid repository name');
}

$repository = RepositoryList::getRepositoryByName($repositoryName);
foreach (MagentoVersions::getVersions() as $magentoVersion) {
    $newVersion = $repository->getNewVersionByPrefix($magentoVersion);
    $branch = 'magento-' . $magentoVersion;
    $repository->release($branch, $newVersion);
}

