<?php
declare(strict_types=1);

namespace Yireo\ReplaceTools\Command;

use Exception;
use Github\Exception\MissingArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yireo\ReplaceTools\Environment;
use Yireo\ReplaceTools\FilesystemResolver;
use Yireo\ReplaceTools\MagentoVersions;
use Yireo\ReplaceTools\Repository;
use Yireo\ReplaceTools\RepositoryList;

/**
 * Class GenerateComposerReplaceAll
 * @package Yireo\ReplaceTools\Command
 */
class GenerateComposerReplaceAll extends Command
{
    protected static $defaultName = 'generate:composer-replace-all';

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws MissingArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
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

        return Command::SUCCESS;
    }
}
