<?php
declare(strict_types=1);

namespace Yireo\ReplaceTools\Test\Unit\Util;

use PHPUnit\Framework\TestCase;
use Yireo\ReplaceTools\Util\VersionUtil;

class VersionUtilTest extends TestCase
{
    public function testGetNewVersion()
    {
        $versionUtil = new VersionUtil();
        $this->assertSame('1.0.1', $versionUtil->getNewVersion('1.0.0'));
        $this->assertSame('1.0.2', $versionUtil->getNewVersion('1.0.1'));
    }
}
