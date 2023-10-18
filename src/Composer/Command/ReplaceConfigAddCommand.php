<?php declare(strict_types=1);

namespace Yireo\ReplaceTools\Composer\Command;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yireo\ReplaceTools\Composer\Service\ReplaceBuilder;

class ReplaceConfigAddCommand extends BaseCommand
{
    protected function configure(): void
    {
        $this->setName('replace:config:add');
        $this->setDescription('Add a replace configuration setting to your composer.json');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $replaceBuilder = new ReplaceBuilder();
        $jsonData = $replaceBuilder->readJsonData();
        if (array_key_exists('extra', $jsonData) && array_key_exists('replace', $jsonData['extra'])) {
            $output->writeln('Section "extra.replace" already exists');

            return Command::SUCCESS;
        }

        $jsonData['extra']['replace'] = [
            'bulk' => [],
            'include' => [],
            'exclude' => [],
        ];
        $replaceBuilder->writeJsonData($jsonData);
        $output->writeln('Added section "extra.replace"');

        return Command::SUCCESS;
    }
}
