<?php declare(strict_types=1);

namespace Yireo\ReplaceTools\Composer\Command;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yireo\ReplaceTools\Composer\Model\Replacement;
use Yireo\ReplaceTools\Composer\Service\ReplaceBuilder;

class ReplaceMultipleAddCommand extends BaseCommand
{
    protected function configure(): void
    {
        $this->setName('replace:multiple:add');
        $this->setDescription('Replace multiple composer packages');
        $this->addArgument('packages', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'Packages to replace (separate multiple packages with a space)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $packages = $input->getArgument('packages');

        foreach ($packages as $package) {
            $replaceBuilder = new ReplaceBuilder();
            $replaceBuilder->replace($package, '*');
            $replaceBuilder->addInclude(new Replacement($package));

            $output->writeln('Added composer replacement for ' . $package);
        }

        return Command::SUCCESS;
    }
}
