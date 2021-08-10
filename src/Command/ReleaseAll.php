<?php declare(strict_types=1);

namespace Yireo\ReplaceTools\Command;

use Github\Exception\MissingArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yireo\ReplaceTools\FilesystemResolver;
use Yireo\ReplaceTools\MagentoVersions;
use Yireo\ReplaceTools\RepositoryList;

/**
 * Class ReleaseAll
 * @package Yireo\ReplaceTools\Command
 */
class ReleaseAll extends Command
{
    protected static $defaultName = 'release:all';

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws MissingArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $magentoVersions = MagentoVersions::getVersions();
        $repositories = RepositoryList::getRepositories(true);

        foreach ($repositories as $repository) {
            $repositoryName = $repository->getName();
            foreach ($magentoVersions as $magentoVersion) {
                $branch = 'magento-' . $magentoVersion;
                $newVersion = $repository->getNewVersionByBranchName($branch);
                $repository->release($branch, $newVersion);
            }
        }

        return Command::SUCCESS;
    }
}
