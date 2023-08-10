<?php declare(strict_types=1);

namespace Yireo\ReplaceTools\Composer\Command;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yireo\ReplaceTools\Composer\Util\Replacer;

class ReplaceListCommand extends BaseCommand
{
    protected function configure(): void
    {
        $this->setName('replace:list');
        $this->setDescription('List current composer replacements');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $table = new Table($output);
        $table->setHeaders(['Package', 'Version']);

        $replacer = new Replacer();
        foreach ($replacer->getReplacements() as $package => $version) {
            $table->addRow([$package, $version]);
        }

        $table->render();

        return Command::SUCCESS;
    }
}
