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
$client = ClientFactory::getClient();

foreach ($repositories as $repository) {
    $repositoryName = $repository->getName();

    // Remove all releases
    $releases = $client->api('repo')->releases()->all('yireo', $repositoryName);
    if (!empty($releases)) {
        foreach ($releases as $release) {
            $releaseId = $release['id'];
            $response = $client->api('repo')->releases()->remove('yireo', $repositoryName, $releaseId);
        }
    }

    // Remove all tags
    $tags = $client->api('repo')->tags('yireo', $repositoryName);
    foreach ($tags as $tag) {
        $tagName = $tag['name'];
        $response = $client->getHttpClient()->delete('repos/yireo/' . $repositoryName . '/git/refs/tags/' . $tagName);
    }

    // Recreate tags & releases from branches
    foreach ($magentoVersions as $magentoVersion) {
        $branch = 'magento-' . $magentoVersion;
        $release = $client->api('repo')->releases()->create(
            'yireo',
            $repositoryName,
            array(
                'tag_name' => $magentoVersion,
                'target_commitish' => $branch,
                'name' => $magentoVersion
            )
        );
    }
}

