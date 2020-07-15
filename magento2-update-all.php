<?php
declare(strict_types=1);

use Yireo\ReplaceTools\FilesystemResolver;
use Yireo\ReplaceTools\MagentoVersions;
use Yireo\ReplaceTools\Repository;
use Yireo\ReplaceTools\RepositoryList;

require_once __DIR__ . '/vendor/autoload.php';
FilesystemResolver::getInstance()->setRootFolder(dirname(__DIR__));

$magentoVersions = MagentoVersions::getVersions();
$repositories = RepositoryList::getRepositories(true);

foreach ($repositories as $repository) {
    /** @var Repository $repository */
    $repositoryName = $repository->getName();
    $repository->update('master');
    foreach ($magentoVersions as $magentoVersion) {
        $branch = 'magento-' . $magentoVersion;
        echo "Pulling in $repositoryName:$branch\n";
        $repository->update($branch);
    }
}

