<?php declare(strict_types=1);

namespace Yireo\ReplaceTools\Composer\Command;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yireo\ReplaceTools\Composer\Model\BulkReplacement;
use Yireo\ReplaceTools\Composer\Service\ReplaceBuilder;

class ReplaceBulkRemoveCommand extends BaseCommand
{
    protected function configure(): void
    {
        $this->setName('replace:bulk:remove');
        $this->setDescription('Remove bulk replacement package');
        $this->addArgument('package', InputArgument::REQUIRED, 'Bulk package to remove');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $bulkPackageName = (string) $input->getArgument('package');
        $bulkReplacement = new BulkReplacement($bulkPackageName);

        $replaceBuilder = new ReplaceBuilder();
        $replaceBuilder->removeBulk($bulkReplacement);

        return Command::SUCCESS;
    }
}
