<?php declare(strict_types=1);

namespace Yireo\ReplaceTools\Test\Unit\Composer\Model;

use PHPUnit\Framework\TestCase;
use Yireo\ReplaceTools\Composer\Model\Replacement;
use Yireo\ReplaceTools\Composer\Model\ReplacementCollection;

class ReplacementCollectionTest extends TestCase
{
    public function testSimpleAdd()
    {
        $replacementCollection = new ReplacementCollection();
        $replacementCollection->add(new Replacement('foo/bar'));
        $this->assertTrue(count($replacementCollection->get()) === 1);
        $first = $replacementCollection->get()[0];
        $this->assertEquals('foo/bar', $first->getComposerName());
    }

    public function testMagentoPackageAdd()
    {
        $replacementCollection = new ReplacementCollection();
        $replacementCollection->add(new Replacement('magento/bar'));
        $this->assertTrue(count($replacementCollection->get()) === 2);

        $foundMagento = false;
        $foundMageOS = false;
        foreach ($replacementCollection->get() as $replacement) {
            if ($replacement->getComposerName() === 'magento/bar') {
                $foundMagento = true;
            }

            if ($replacement->getComposerName() === 'mage-os/bar') {
                $foundMageOS = true;
            }
        }

        $this->assertTrue($foundMagento);
        $this->assertTrue($foundMageOS);
    }

    public function testMageOSPackageAdd()
    {
        $replacementCollection = new ReplacementCollection();
        $replacementCollection->add(new Replacement('mage-os/bar'));
        $this->assertTrue(count($replacementCollection->get()) === 2);

        $foundMagento = false;
        $foundMageOS = false;
        foreach ($replacementCollection->get() as $replacement) {
            if ($replacement->getComposerName() === 'magento/bar') {
                $foundMagento = true;
            }

            if ($replacement->getComposerName() === 'mage-os/bar') {
                $foundMageOS = true;
            }
        }

        $this->assertTrue($foundMagento);
        $this->assertTrue($foundMageOS);
    }
}
