<?php
$magentoVersions = [
//    '2.3.0',
//    '2.3.1',
//    '2.3.2',
//    '2.3.3',
//    '2.3.4',
    '2.3.5',
];

$account = 'yireo';
$subRepos = [
    'magento2-replace-core',
    'magento2-replace-bundled',
    'magento2-replace-graphql',
    'magento2-replace-inventory',
    'magento2-replace-content-staging',
];

$parentRepo = 'magento2-replace-all';

$replacements = [];
foreach ($magentoVersions as $magentoVersion) {
    foreach ($subRepos as $repo) {
        $repoFile = 'https://raw.githubusercontent.com/'.$account.'/'.$repo.'/magento-'.$magentoVersion.'/composer.json';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $repoFile);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        $body = curl_exec($ch);
        curl_close($ch);

        if (empty($body)) {
            throw new \Exception('Empty body for URL: '.$repoFile);
        }

        $data = json_decode($body, true);
        if (empty($data)) {
            throw new \Exception('Empty data for URL: '.$repoFile);
        }

        $replacements = array_merge($replacements, $data['replace']);
    }

    ksort($replacements);

    $composerFile = [
        'name' => 'yireo/magento2-replace-all',
        'description' => 'Bundling of other yireo/magento2-replace repositories',
        'version' => $magentoVersion,
        'replace' => $replacements
    ];

    $composerContents = json_encode($composerFile, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

    $parentRepoDirectory = __DIR__.'/../'.$parentRepo;
    if (!is_dir($parentRepoDirectory)) {
        throw new \Exception('Directory not found: '.$parentRepoDirectory);
    }

    chdir($parentRepoDirectory);
    exec('git checkout magento-'.$magentoVersion);
    file_put_contents('composer.json', $composerContents);
    exec('git commit -m "Merging packages together" composer.json');
    exec('git push origin magento-'.$magentoVersion);
}

