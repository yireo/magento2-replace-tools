#!/usr/bin/env php
<?php
require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Application;
use Yireo\ReplaceTools\Command\GenerateComposerReplaceAll;
use Yireo\ReplaceTools\Command\ReleaseAll;
use Yireo\ReplaceTools\Command\ReleaseByRepository;use Yireo\ReplaceTools\Command\ReleaseByRepositoryAndMagentoVersion;use Yireo\ReplaceTools\FilesystemResolver;

FilesystemResolver::getInstance()->setRootFolder(dirname(__DIR__));

$application = new Application();
$application->addCommands(
    [
        new ReleaseAll,
        new ReleaseByRepository(),
        new ReleaseByRepositoryAndMagentoVersion(),
        new GenerateComposerReplaceAll,
    ]
);
$application->run();
