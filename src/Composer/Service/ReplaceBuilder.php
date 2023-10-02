<?php declare(strict_types=1);

namespace Yireo\ReplaceTools\Composer\Service;

use Composer\Factory;
use GuzzleHttp\Exception\GuzzleException;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\FilesystemException;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Yireo\ReplaceTools\Composer\Exception\HttpClientException;
use Yireo\ReplaceTools\Composer\Exception\PackageException;
use Yireo\ReplaceTools\Composer\Model\BulkReplacement;
use Yireo\ReplaceTools\Composer\Model\Replacement;
use Yireo\ReplaceTools\Composer\Model\ReplacementCollection;

class ReplaceBuilder
{
    private string $composerFile = '';
    private Filesystem $filesystem;

    /**
     * @param FilesystemAdapter|null $filesystemAdapter
     * @param string $composerFile
     */
    public function __construct(
        ?Filesystem $filesystem = null,
        string $composerFile = ''
    ) {
        if (empty($composerFile)) {
            $composerFile = Factory::getComposerFile();
        }

        if (empty($filesystem)) {
            $filesystem = new Filesystem(new LocalFilesystemAdapter(dirname($composerFile)));
        }

        $this->composerFile = $composerFile;
        $this->filesystem = $filesystem;
    }

    /**
     * @return ReplacementCollection
     * @throws FilesystemException
     */
    public function read(): ReplacementCollection
    {
        $jsonData = $this->readJsonData();
        $replacements = $jsonData['replace'] ?? [];
        $collection = new ReplacementCollection();
        foreach ($replacements as $package => $version) {
            $collection->add(new Replacement($package, $version));
        }

        return $collection;
    }

    /**
     * @param ReplacementCollection $replacements
     * @return void
     * @throws FilesystemException
     */
    public function write(ReplacementCollection $replacements)
    {
        $jsonData = $this->readJsonData();
        $replacementData = $replacements->toArray();
        ksort($replacementData);
        $jsonData['replace'] = $replacementData;
        $this->writeJsonData($jsonData);
    }

    /**
     * @param string $package
     * @param string $version
     * @return void
     * @throws FilesystemException
     */
    public function replace(string $package, string $version)
    {
        $replacements = $this->read();
        $replacements->add(new Replacement($package, $version));
        $this->write($replacements);
    }

    /**
     * @param Replacement $replacement
     * @return void
     * @throws FilesystemException
     */
    public function remove(Replacement $replacement)
    {
        $replacements = $this->read();
        $replacements->remove($replacement);
        $this->write($replacements);
    }

    /**
     * @return string[]
     */
    public function suggestedBulks(): array
    {
        return [
            'yireo/magento2-replace-core',
            'yireo/magento2-replace-bundled',
            'yireo/magento2-replace-inventory',
            'yireo/magento2-replace-graphql',
            'yireo/magento2-replace-pagebuilder',
            'yireo/magento2-replace-sample-data',
            'yireo/magento2-replace-content-staging',
        ];
    }

    /**
     * @return BulkReplacement[]
     * @throws FilesystemException
     */
    public function readBulks(): array
    {
        $jsonData = $this->readJsonData();
        if (empty($jsonData['extra']) || empty($jsonData['extra']['replace']) || empty($jsonData['extra']['replace']['bulk'])) {
            return [];
        }

        $bulkReplacements = [];
        foreach ($jsonData['extra']['replace']['bulk'] as $bulkName) {
            $bulkReplacements[] = new BulkReplacement($bulkName);
        }

        return $bulkReplacements;
    }

    /**
     * @return string[]
     * @throws FilesystemException
     */
    public function readRequires(): array
    {
        $jsonData = $this->readJsonData();
        $requires = $jsonData['require'] ?? [];
        return array_keys($requires);
    }

    /**
     * @return ReplacementCollection
     * @throws FilesystemException
     */
    public function readExcludes(): ReplacementCollection
    {
        $collection = new ReplacementCollection();
        $jsonData = $this->readJsonData();
        if (empty($jsonData['extra']) || empty($jsonData['extra']['replace']) || empty($jsonData['extra']['replace']['exclude'])) {
            return $collection;
        }

        foreach ($jsonData['extra']['replace']['exclude'] as $composerName => $version) {
            $collection->add(new Replacement($composerName, $version));
        }

        return $collection;
    }

    /**
     * @param ReplacementCollection $collection
     * @return void
     * @throws FilesystemException
     */
    public function writeExcludes(ReplacementCollection $collection): void
    {
        $jsonData = $this->readJsonData();
        $jsonData['extra']['replace']['exclude'] = $collection->toArray();
        $this->writeJsonData($jsonData);
    }

    /**
     * @param Replacement $replacement
     * @return void
     * @throws FilesystemException
     */
    public function addExclude(Replacement $replacement)
    {
        $collection = $this->readExcludes();
        $collection->add($replacement);
        $this->writeExcludes($collection);
    }

    /**
     * @return ReplacementCollection
     * @throws FilesystemException
     */
    public function readIncludes(): ReplacementCollection
    {
        $collection = new ReplacementCollection();
        $jsonData = $this->readJsonData();
        if (empty($jsonData['extra']) || empty($jsonData['extra']['replace']) || empty($jsonData['extra']['replace']['include'])) {
            return $collection;
        }

        foreach ($jsonData['extra']['replace']['include'] as $composerName => $version) {
            $collection->add(new Replacement($composerName, $version));
        }

        return $collection;
    }

    /**
     * @param ReplacementCollection $collection
     * @return void
     * @throws FilesystemException
     */
    public function writeIncludes(ReplacementCollection $collection): void
    {
        $jsonData = $this->readJsonData();
        $jsonData['extra']['replace']['include'] = $collection->toArray();
        $this->writeJsonData($jsonData);
    }

    /**
     * @param Replacement $replacement
     * @return void
     * @throws FilesystemException
     */
    public function addInclude(Replacement $replacement)
    {
        $collection = $this->readIncludes();
        $collection->add($replacement);
        $this->writeIncludes($collection);
    }

    /**
     * @param BulkReplacement[] $bulkReplacements
     * @return void
     * @throws FilesystemException
     */
    public function writeBulks(array $bulkReplacements)
    {
        $bulkReplacementArray = [];
        foreach ($bulkReplacements as $bulkReplacement) {
            $bulkReplacementArray[] = $bulkReplacement->getComposerName();
        }

        $bulkReplacementArray = array_unique($bulkReplacementArray);
        $jsonData = $this->readJsonData();
        $jsonData['extra']['replace']['bulk'] = $bulkReplacementArray;
        $this->writeJsonData($jsonData);
    }

    /**
     * @param BulkReplacement $bulkReplacement
     * @return void
     * @throws FilesystemException
     */
    public function addBulk(BulkReplacement $bulkReplacement)
    {
        $bulkReplacements = $this->readBulks();
        $bulkReplacements[] = $bulkReplacement;
        $this->writeBulks($bulkReplacements);
    }

    /**
     * @param BulkReplacement $bulkReplacement
     * @return void
     * @throws FilesystemException
     */
    public function removeBulk(BulkReplacement $bulkReplacement)
    {
        $bulks = $this->readBulks();
        $key = array_search($bulkReplacement->getComposerName(), $bulks);
        if (false !== $key) {
            unset($bulks[$key]);
        }

        $this->writeBulks($bulks);
    }

    /**
     * @return ReplacementCollection
     * @throws FilesystemException
     */
    public function getConfigured(): ReplacementCollection
    {
        $collection = new ReplacementCollection();
        foreach ($this->readBulks() as $bulkReplacement) {
            $collection->merge($bulkReplacement->fetch());
        }

        foreach ($this->readExcludes()->get() as $excludeReplacement) {
            $collection->remove($excludeReplacement);
        }

        foreach ($this->readIncludes()->get() as $includeReplacement) {
            $collection->add($includeReplacement);
        }

        return $collection;
    }

    /**
     * @return string[]
     * @throws GuzzleException
     * @throws HttpClientException
     * @throws PackageException
     */
    public function getErrors(): array
    {
        $errors = [];
        $matchingBulks = [];
        $configured = $this->getConfigured();
        $currentReplacements = $this->read();
        foreach ($currentReplacements->get() as $current) {
            if ($configured->contains($current)) {
                continue;
            }

            foreach ($this->suggestedBulks() as $bulkName) {
                $bulk = new BulkReplacement($bulkName);
                if ($bulk->contains($current)) {
                    if (isset($matchingBulks[$bulk->getComposerName()])) {
                        $matchingBulks[$bulk->getComposerName()][] = $current->getComposerName();
                    } else {
                        $matchingBulks[$bulk->getComposerName()] = [$current->getComposerName()];
                    }

                    break;
                }
            }

            $currentName = $current->getComposerName();
            $errors[] = '"'.$currentName.'" not configured via "extra.replace"';
        }

        foreach ($matchingBulks as $matchingBulk => $matchingBulkPackages) {
            $bulk = new BulkReplacement($matchingBulk);
            $error = 'Bulk "'.$matchingBulk.'" ('.$bulk->count().') matches with '.count($matchingBulkPackages).' entries: ';
            $error .= implode(', ', array_splice($matchingBulkPackages, 0, 3));
            if (count($matchingBulkPackages) > 3) {
                $error .= ', ...';
            }

            $errors[] = $error;
        }

        $requires = $this->readRequires();
        foreach ($this->suggestedBulks() as $bulkName) {
            if (in_array($bulkName, $requires)) {
                $errors[] = 'Bulk "'.$bulkName.'" should not be in "require" section but in "extra.replace.bulk"';
            }
        }

        return array_merge($errors, $this->getErrorsFromBasicStructure());
    }

    private function getErrorsFromBasicStructure(): array
    {
        $errors = [];

        $jsonData = $this->readJsonData();
        if (!isset($jsonData['extra'])) {
            return ['No section "extra" yet'];
        }

        if (!isset($jsonData['extra']['replace'])) {
            return ['No section "extra.replace" yet'];
        }

        $elements = array_keys($jsonData['extra']['replace']);
        $allowedElements = ['bulk', 'include', 'exclude'];
        foreach ($elements as $element) {
            if (!in_array($element, $allowedElements)) {
                $errors[] = 'Unknown element "'.$element.'" in section "extra.replace"';
            }
        }

        return $errors;
    }

    /**
     * @return string[]
     * @throws FilesystemException
     */
    public function build(): array
    {
        $messages = [];
        $configuredReplacements = $this->getConfigured();
        $currentReplacements = $this->read();
        foreach ($currentReplacements->get() as $currentReplacement) {
            if (false === $configuredReplacements->contains($currentReplacement)) {
                $this->addInclude($currentReplacement);
                $messages[] = 'Adding replacement "'.$currentReplacement->getComposerName(
                    ).'" to "extra.replace.include"';
            }
        }

        $this->write($configuredReplacements);

        return $messages;
    }

    /**
     * @return array
     * @throws FilesystemException
     */
    private function readJsonData(): array
    {
        return json_decode($this->filesystem->read($this->composerFile), true);
    }

    /**
     * @param array $jsonData
     * @return void
     * @throws FilesystemException
     */
    private function writeJsonData(array $jsonData)
    {
        $contents = json_encode($jsonData, JSON_PRETTY_PRINT + JSON_UNESCAPED_SLASHES);
        $this->filesystem->write($this->composerFile, $contents);
    }
}
