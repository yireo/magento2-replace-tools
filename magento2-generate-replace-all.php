<?php

declare(strict_types=1);

use Yireo\ReplaceTools\Environment;
use Yireo\ReplaceTools\FilesystemResolver;
use Yireo\ReplaceTools\MagentoVersions;
use Yireo\ReplaceTools\Repository;
use Yireo\ReplaceTools\RepositoryList;

require_once __DIR__ . '/vendor/autoload.php';
FilesystemResolver::getInstance()->setRootFolder(dirname(__DIR__));

$magentoVersions = MagentoVersions::getVersions();
$repositories = RepositoryList::getRepositories();
$parentRepository = new Repository('magento2-replace-all', Environment::getAccountName());

foreach ($magentoVersions as $magentoVersion) {
    $branch = 'magento-' . $magentoVersion;
    $replacements = [];

    foreach ($repositories as $repository) {
        echo 'Reading "'.$repository->getName().'" with branch "'.$branch.'"'."\n";
        $composerFile = $repository->getRemoteComposerFile($branch);
        $repositoryReplacements = $composerFile->getReplace();
        if (empty($repositoryReplacements)) {
            throw new Exception('No replacements found');
        }

        $replacements = array_merge($replacements, $repositoryReplacements);
    }

    ksort($replacements);

    $composerFile = $parentRepository->getLocalComposerFile($branch);
    $composerFile->setReplace($replacements);
    $parentRepository->saveComposerFile($composerFile, $branch);
}
