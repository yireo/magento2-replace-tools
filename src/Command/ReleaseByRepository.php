<?php
declare(strict_types=1);

namespace Yireo\ReplaceTools\Command;

use Github\Exception\MissingArgumentException;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yireo\ReplaceTools\FilesystemResolver;
use Yireo\ReplaceTools\MagentoVersions;
use Yireo\ReplaceTools\RepositoryList;

/**
 * Class ReleaseByRepository
 * @package Yireo\ReplaceTools\Command
 */
class ReleaseByRepository extends Command
{
    protected static $defaultName = 'release:repository';

    protected function configure()
    {
        $this->addArgument('repository');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws MissingArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repositoryName = (string)$input->getArgument('repository');
        $allowedRepositories = RepositoryList::getRepositories(true);
        if (!in_array($repositoryName, RepositoryList::getRepositories(true))) {
            throw new RuntimeException(
                'Invalid repository name "' . $repositoryName . '". Repository should be one of the following: ' . implode(
                    ' ',
                    $allowedRepositories
                )
            );
        }

        $repository = RepositoryList::getRepositoryByName($repositoryName);
        foreach (MagentoVersions::getVersions() as $magentoVersion) {
            $branchName = 'magento-' . $magentoVersion;
            $newVersion = $repository->getNewVersionByBranchName($branchName);
            $repository->release($branchName, $newVersion);
        }

        return Command::SUCCESS;
    }
}
