<?php declare(strict_types=1);

namespace Yireo\ReplaceTools\Composer\Command;

use Composer\Command\BaseCommand;
use Magento\AsynchronousOperations\Model\ResourceModel\Bulk;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yireo\ReplaceTools\Composer\Util\BulkReplacement;
use Yireo\ReplaceTools\Composer\Util\Replacer;

class ReplaceBulkAddCommand extends BaseCommand
{
    protected function configure(): void
    {
        $this->setName('replace:bulk:add');
        $this->setDescription('Add multiple replacements via a bulk package');
        $this->addArgument('package', InputArgument::REQUIRED, 'Bulk package to use');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $bulkPackageName = (string) $input->getArgument('package');
        $bulkReplacement = new BulkReplacement($bulkPackageName);
        $replacementsFromBulk = $bulkReplacement->fetchReplacements();
        if (empty($replacementsFromBulk)) {
            $output->writeln('<error>No replacements loaded from bulk</error>');
        }

        $replacer = new Replacer();
        $replacer->addBulk($bulkPackageName, $replacementsFromBulk);

        return Command::SUCCESS;
    }
}
