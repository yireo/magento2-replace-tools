<?php declare(strict_types=1);

namespace Yireo\ReplaceTools\Composer\Command;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yireo\ReplaceTools\Composer\Service\ReplaceBuilder;

class ReplaceAddCommand extends BaseCommand
{
    protected function configure(): void
    {
        $this->setName('replace:add');
        $this->setDescription('Replace a composer package with a specific version');
        $this->addArgument('package', InputArgument::REQUIRED, 'Package to replace');
        $this->addArgument('version', InputArgument::OPTIONAL, 'Package version', '*');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $package = (string) $input->getArgument('package');
        $version = (string) $input->getArgument('version');
        $replaceBuilder = new ReplaceBuilder();
        $replaceBuilder->replace($package, $version);

        $output->writeln('Added composer replacement');

        return Command::SUCCESS;
    }
}
