<?php
declare(strict_types=1);

use Yireo\ReplaceTools\ClientFactory;
use Yireo\ReplaceTools\FilesystemResolver;
use Yireo\ReplaceTools\MagentoVersions;
use Yireo\ReplaceTools\RepositoryList;

require_once __DIR__ . '/vendor/autoload.php';
FilesystemResolver::getInstance()->setRootFolder(dirname(__DIR__));

$magentoVersions = MagentoVersions::getVersions();
$repositories = RepositoryList::getRepositories(true);

foreach ($repositories as $repository) {
    $repositoryName = $repository->getName();
    foreach ($magentoVersions as $magentoVersion) {
        $newVersion = $repository->getNewVersionByPrefix($magentoVersion);
        $branch = 'magento-' . $magentoVersion;
        $repository->release($branch, $newVersion);
    }
}

