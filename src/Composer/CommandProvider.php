<?php declare(strict_types=1);

namespace Yireo\ReplaceTools\Composer;

use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;
use Yireo\ReplaceTools\Composer\Command\ReplaceAddCommand;
use Yireo\ReplaceTools\Composer\Command\ReplaceBulkAddCommand;
use Yireo\ReplaceTools\Composer\Command\ReplaceListCommand;
use Yireo\ReplaceTools\Composer\Command\ReplaceRemoveCommand;

class CommandProvider implements CommandProviderCapability
{
    public function getCommands()
    {
        return [
            new ReplaceAddCommand(),
            new ReplaceRemoveCommand(),
            new ReplaceListCommand(),
            new ReplaceBulkAddCommand(),
        ];
    }
}
