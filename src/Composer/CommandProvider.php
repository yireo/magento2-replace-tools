<?php declare(strict_types=1);

namespace Yireo\ReplaceTools\Composer;

use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;
use Yireo\ReplaceTools\Composer\Command\ReplaceAddCommand;
use Yireo\ReplaceTools\Composer\Command\ReplaceBuildCommand;
use Yireo\ReplaceTools\Composer\Command\ReplaceBulkAddCommand;
use Yireo\ReplaceTools\Composer\Command\ReplaceBulkListCommand;
use Yireo\ReplaceTools\Composer\Command\ReplaceBulkRemoveCommand;
use Yireo\ReplaceTools\Composer\Command\ReplaceConfigAddCommand;
use Yireo\ReplaceTools\Composer\Command\ReplaceExcludeCommand;
use Yireo\ReplaceTools\Composer\Command\ReplaceIncludeCommand;
use Yireo\ReplaceTools\Composer\Command\ReplaceListCommand;
use Yireo\ReplaceTools\Composer\Command\ReplaceRemoveCommand;
use Yireo\ReplaceTools\Composer\Command\ReplaceValidateCommand;
use Yireo\ReplaceTools\Composer\Command\ReplaceMultipleAddCommand;

class CommandProvider implements CommandProviderCapability
{
    public function getCommands()
    {
        return [
            new ReplaceAddCommand(),
            new ReplaceConfigAddCommand(),
            new ReplaceRemoveCommand(),
            new ReplaceListCommand(),
            new ReplaceIncludeCommand(),
            new ReplaceExcludeCommand(),
            new ReplaceBulkAddCommand(),
            new ReplaceBulkRemoveCommand(),
            new ReplaceBulkListCommand(),
            new ReplaceValidateCommand(),
            new ReplaceBuildCommand(),
            new ReplaceMultipleAddCommand(),
        ];
    }
}
