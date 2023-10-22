<?php declare(strict_types=1);

namespace Yireo\ReplaceTools\Test\Unit\Composer;

use PHPUnit\Framework\TestCase;
use Yireo\ReplaceTools\Composer\Plugin;

class PluginTest extends TestCase
{
    public function testBasicFeatures()
    {
        $plugin = new Plugin();
        $this->assertInstanceOf(Plugin::class, $plugin);

        $capabilities = $plugin->getCapabilities();
        $this->assertNotEmpty($capabilities);
        $this->assertTrue(count($capabilities) === 1);
    }
}
