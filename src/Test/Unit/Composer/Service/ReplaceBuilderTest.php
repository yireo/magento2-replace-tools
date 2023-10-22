<?php declare(strict_types=1);

namespace Yireo\ReplaceTools\Test\Unit\Composer;

use GuzzleHttp\Exception\ClientException;
use League\Flysystem\Filesystem;
use League\Flysystem\InMemory\InMemoryFilesystemAdapter;
use PHPUnit\Framework\TestCase;
use Yireo\ReplaceTools\Composer\Model\Replacement;
use Yireo\ReplaceTools\Composer\Service\ReplaceBuilder;
use Yireo\ReplaceTools\Test\Unit\Fixture\ComposerJsonFixture;

class ReplaceBuilderTest extends TestCase
{
    public function testCollectionWithSimpleReplaces()
    {
        $composerJson = $this->getFixture()
            ->addReplace('yireo/foobar1')
            ->addReplace('yireo/foobar2')
            ->output();
        $filesystem = $this->getFilesystemFixture($composerJson);
        $replaceBuilder = new ReplaceBuilder($filesystem);

        $collection = $replaceBuilder->read();
        $this->assertEquals(2, $collection->count());

        $collection->add(new Replacement('yireo/foobar3'));
        $replaceBuilder->write($collection);

        $collection = $replaceBuilder->read();
        $this->assertEquals(3, $collection->count());

        $collection->add(new Replacement('yireo/foobar3'));
        $replaceBuilder->write($collection);

        $collection = $replaceBuilder->read();
        $this->assertEquals(3, $collection->count());
    }

    public function testValidateIncludeReplacement()
    {
        $composerJson = $this->getFixture()
            ->addIncludeReplace('yireo/foobar1')
            ->addReplace('yireo/foobar2')
            ->output();
        $filesystem = $this->getFilesystemFixture($composerJson);
        $replaceBuilder = new ReplaceBuilder($filesystem);

        $errors = $replaceBuilder->getErrors();
        $this->assertEquals(1, count($errors));
        $this->assertStringContainsString('"yireo/foobar2" not configured via "extra.replace"', $errors[0]);

        $replaceBuilder->build();
        $errors = $replaceBuilder->getErrors();
        $this->assertEquals(0, count($errors));
    }

    public function testValidateBulkReplacements()
    {
        $composerJson = $this->getFixture()
            ->addBulkReplace('yireo/magento2-replace-graphql')
            ->output();
        $filesystem = $this->getFilesystemFixture($composerJson);
        $replaceBuilder = new ReplaceBuilder($filesystem);

        $errors = $replaceBuilder->getErrors();
        $this->assertEquals(0, count($errors), 'Expected no errors');

        $replaceBuilder->build();
        $collection = $replaceBuilder->read();
        $this->assertTrue($collection->count() > 40);
    }

    public function testValidateWrongBulkReplacement()
    {
        $composerJson = $this->getFixture()
            ->addBulkReplace('yireo/not-existing')
            ->output();
        $filesystem = $this->getFilesystemFixture($composerJson);
        $replaceBuilder = new ReplaceBuilder($filesystem);

        $this->expectException(ClientException::class);
        $replaceBuilder->build();
    }

    public function testValidateWithEmptyBulkReplacement()
    {
        $composerJson = $this->getFixture()
            ->addBulkReplace('yireo/magento2-replace-tools')
            ->output();
        $filesystem = $this->getFilesystemFixture($composerJson);
        $replaceBuilder = new ReplaceBuilder($filesystem);

        $collection = $replaceBuilder->getConfigured();
        $this->assertEquals(0, $collection->count());
    }

    private function getFixture(): ComposerJsonFixture
    {
        return new ComposerJsonFixture();
    }

    private function getFilesystemFixture(string $composerJson)
    {
        $memoryAdapter = new InMemoryFilesystemAdapter();
        $filesystem = new Filesystem($memoryAdapter);
        $filesystem->write('/composer.json', $composerJson);
        return $filesystem;
    }
}
