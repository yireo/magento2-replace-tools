<?php declare(strict_types=1);

namespace Yireo\ReplaceTools\Composer\Command;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yireo\ReplaceTools\Composer\Service\ReplaceBuilder;

class ReplaceBuildCommand extends BaseCommand
{
    protected function configure(): void
    {
        $this->setName('replace:build');
        $this->setDescription('Build composer replacements based on "extra.replace"');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $replaceBuilder = new ReplaceBuilder();
        $messages = $replaceBuilder->build();
        foreach ($messages as $message) {
            $output->writeln($message);
        }

        $output->writeln('Your "composer.json" file has been updated. Remove the "composer.lock" file and "vendor/" folder and run "composer install" to rebuild your composer dependencies');
        return Command::SUCCESS;
    }
}
