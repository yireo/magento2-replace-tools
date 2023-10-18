<?php declare(strict_types=1);

namespace Yireo\ReplaceTools\Composer\Command;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yireo\ReplaceTools\Composer\Service\ReplaceBuilder;

class ReplaceBulkListCommand extends BaseCommand
{
    protected function configure(): void
    {
        $this->setName('replace:bulk:list');
        $this->setDescription('List current bulk replacements');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $table = new Table($output);
        $table->setHeaders(['Package']);

        $replaceBuilder = new ReplaceBuilder();
        foreach ($replaceBuilder->readBulks() as $bulkReplacement) {
            $table->addRow([$bulkReplacement->getComposerName()]);
        }

        $table->render();

        return Command::SUCCESS;
    }
}
