<?php

declare(strict_types=1);

use Yireo\ReplaceTools\FilesystemResolver;
use Yireo\ReplaceTools\MagentoVersions;
use Yireo\ReplaceTools\RepositoryList;

require_once __DIR__ . '/vendor/autoload.php';
FilesystemResolver::getInstance()->setRootFolder(dirname(__DIR__));

if (!isset($argv[1])) {
    die("Argument 1 should be a repository name\n");
}

if (!isset($argv[2])) {
    die("Argument 2 should be a Magento version\n");
}

$repositoryName = (string)$argv[1];
$repository = RepositoryList::getRepositoryByName($repositoryName);

$magentoVersion = (string)$argv[2];
if (!in_array($magentoVersion, MagentoVersions::getVersions())) {
    throw new RuntimeException('Invalid Magento version');
}

$newVersion = $repository->getNewVersionByPrefix($magentoVersion);
echo "Releasing new version $newVersion\n";
$branch = 'magento-' . $magentoVersion;
$repository->release($branch, $newVersion);
