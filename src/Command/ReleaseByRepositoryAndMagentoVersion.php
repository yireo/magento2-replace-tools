<?php declare(strict_types=1);

namespace Yireo\ReplaceTools\Command;

use Github\Exception\MissingArgumentException;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Yireo\ReplaceTools\FilesystemResolver;
use Yireo\ReplaceTools\MagentoVersions;
use Yireo\ReplaceTools\RepositoryList;

/**
 * Class ReleaseAll
 * @package Yireo\ReplaceTools\Command
 */
class ReleaseByRepositoryAndMagentoVersion extends Command
{
    protected static $defaultName = 'release:repository-and-version';

    protected function configure()
    {
        $this->addArgument('repository', InputArgument::OPTIONAL, 'The repository of replacement packages.');
        $this->addArgument('magento_version', InputArgument::OPTIONAL, 'The Magento version');
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
        if (empty($repositoryName)) {
            $question = new ChoiceQuestion(
                'Please select a repository',
                RepositoryList::getRepositoryNames()
            );
            $question->setErrorMessage('Repository %s is invalid.');
            $helper = $this->getHelper('question');
            $repositoryName = $helper->ask($input, $output, $question);
        }

        $output->writeln('Repository: '.$repositoryName);
        $repository = RepositoryList::getRepositoryByName($repositoryName);

        $magentoVersion = (string)$input->getArgument('magento_version');
        if (empty($magentoVersion)) {
            $question = new ChoiceQuestion(
                'Please select a Magento version',
                MagentoVersions::getVersions()
            );
            $question->setErrorMessage('Magento version %s is invalid.');
            $helper = $this->getHelper('question');
            $magentoVersion = $helper->ask($input, $output, $question);
        }

        if (!in_array($magentoVersion, MagentoVersions::getVersions())) {
            throw new RuntimeException('Invalid Magento version');
        }

        $newVersion = $repository->getNewVersionByBranchName('magento-'.$magentoVersion);
        echo "Releasing new version $newVersion\n";
        $branch = 'magento-' . $magentoVersion;
        $repository->release($branch, $newVersion);

        return Command::SUCCESS;
    }
}
