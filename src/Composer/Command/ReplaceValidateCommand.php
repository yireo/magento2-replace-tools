<?php declare(strict_types=1);

namespace Yireo\ReplaceTools\Composer\Command;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yireo\ReplaceTools\Composer\Service\ReplaceBuilder;

class ReplaceValidateCommand extends BaseCommand
{
    protected function configure(): void
    {
        $this->setName('replace:validate');
        $this->setDescription('Validate current composer replacements');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $replaceBuilder = new ReplaceBuilder();
        $errors = $replaceBuilder->getErrors();
        if (empty($errors)) {
            return Command::SUCCESS;
        }

        $table = new Table($output);
        $table->setHeaders(['Error']);

        foreach ($errors as $error) {
            $table->addRow([$error]);
        }

        $table->render();

        return Command::FAILURE;
    }
}
