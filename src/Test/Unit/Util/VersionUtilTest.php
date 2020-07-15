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
        $this->assertSame('1.0.0-p1', $versionUtil->getNewVersion('1.0.0'));
        $this->assertSame('1.0.0-p2', $versionUtil->getNewVersion('1.0.0-p1'));
        $this->assertSame('1.0.0-p3', $versionUtil->getNewVersion('1.0.0-p2'));
    }
}
