<?php declare(strict_types=1);

namespace Yireo\ReplaceTools\Composer\Command;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yireo\ReplaceTools\Composer\Model\BulkReplacement;
use Yireo\ReplaceTools\Composer\Service\ReplaceBuilder;

class ReplaceBulkAddCommand extends BaseCommand
{
    protected function configure(): void
    {
        $this->setName('replace:bulk:add');
        $this->setDescription('Add multiple replacements via a bulk package');
        $this->addArgument('package', InputArgument::REQUIRED, 'Bulk package to use');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $composerName = (string)$input->getArgument('package');
        $bulkReplacement = new BulkReplacement($composerName);
        $replacementsFromBulk = $bulkReplacement->fetch();
        if ($replacementsFromBulk->empty()) {
            $output->writeln('<error>No replacements loaded from bulk</error>');

            return Command::FAILURE;
        }

        $replaceBuilder = new ReplaceBuilder();
        $replaceBuilder->addBulk($bulkReplacement);

        foreach ($replaceBuilder->getErrors() as $error) {
            $output->writeln('<error>'.$error.'</error>');
        }
        $output->writeln('Do not forget to run "composer replace:build" afterwards');

        return Command::SUCCESS;
    }
}
