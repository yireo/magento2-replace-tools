<?php declare(strict_types=1);

namespace Yireo\ReplaceTools\Test\Unit\Composer;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Yireo\ReplaceTools\Composer\CommandProvider;
use Yireo\ReplaceTools\Composer\Plugin;

class CommandProviderTest extends TestCase
{
    public function testBasicFeatures()
    {
        $commandProvider = new CommandProvider();
        $this->assertInstanceOf(CommandProvider::class, $commandProvider);

        $commands = $commandProvider->getCommands();
        foreach ($commands as $command) {
            $this->assertInstanceOf(Command::class, $command);
        }
    }
}
