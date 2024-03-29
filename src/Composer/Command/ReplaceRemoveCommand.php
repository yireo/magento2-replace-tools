<?php declare(strict_types=1);

namespace Yireo\ReplaceTools\Composer\Command;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yireo\ReplaceTools\Composer\Model\Replacement;
use Yireo\ReplaceTools\Composer\Service\ReplaceBuilder;

class ReplaceRemoveCommand extends BaseCommand
{
    protected function configure(): void
    {
        $this->setName('replace:remove');
        $this->setDescription('Remove a composer replacement from the replace-section');
        $this->addArgument('package', InputArgument::REQUIRED, 'Package to remove');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $package = (string) $input->getArgument('package');
        $replaceBuilder = new ReplaceBuilder();
        $replaceBuilder->remove(new Replacement($package));

        $output->writeln('Removed composer replacement');

        return Command::SUCCESS;
    }
}
