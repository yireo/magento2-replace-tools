<?php declare(strict_types=1);

namespace Yireo\ReplaceTools\Composer\Command;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yireo\ReplaceTools\Composer\Model\Replacement;
use Yireo\ReplaceTools\Composer\Service\ReplaceBuilder;

class ReplaceIncludeCommand extends BaseCommand
{
    protected function configure(): void
    {
        $this->setName('replace:include');
        $this->setDescription('Add a replacement to the extra.replace.include section');
        $this->addArgument('package', InputArgument::REQUIRED, 'Composer package name');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $package = (string) $input->getArgument('package');
        $replaceBuilder = new ReplaceBuilder();
        $replaceBuilder->addInclude(new Replacement($package));

        $output->writeln('Included composer replacement');

        return Command::SUCCESS;
    }
}
