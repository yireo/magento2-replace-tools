<?php declare(strict_types=1);

namespace Yireo\ReplaceTools\Composer\Util;

use Composer\Factory;
use Composer\Json\JsonFile;

class Replacer
{
    public function getReplacements(): array
    {
        $jsonData = $this->getJsonData();
        return $jsonData['replace'] ?? [];
    }

    public function setReplacements(array $replacements)
    {
        $jsonData = $this->getJsonData();
        $jsonData['replace'] = $replacements;
        $this->writeJsonData($jsonData);
    }

    public function replace(string $package, string $version)
    {
        $replacements = $this->getReplacements();
        $replacements[$package] = $version;
        $this->setReplacements($replacements);
    }

    public function remove(string $package)
    {
        $replacements = $this->getReplacements();
        if (isset($replacements[$package])) {
            unset($replacements[$package]);
            $this->setReplacements($replacements);
        }
    }

    public function getBulks(): array
    {
        $jsonData = $this->getJsonData();
        if (empty($jsonData['extra']) || empty($jsonData['extra']['replace']) || empty($jsonData['extra']['replace']['bulk'])) {
            return [];
        }

        return $jsonData['extra']['replace']['bulk'];
    }

    public function setBulks(array $bulks)
    {
        $jsonData = $this->getJsonData();
        $jsonData['extra']['replace']['bulk'] = $bulks;
        $this->writeJsonData($jsonData);
    }

    public function addBulk(string $bulkPackageName, array $bulkReplacements)
    {
        $bulks = $this->getBulks();
        $bulks[$bulkPackageName] = true;
        $this->setBulks($bulks);

        $replacements = $this->getReplacements();
        $replacements = array_merge($replacements, $bulkReplacements);
        $this->setReplacements($replacements);
    }

    /**
     * @return array
     */
    private function getJsonData(): array
    {
        $json = new JsonFile(Factory::getComposerFile());
        return json_decode(file_get_contents($json->getPath()), true);
    }

    private function writeJsonData(array $jsonData)
    {
        file_put_contents($this->getJsonPath(), json_encode($jsonData, JSON_PRETTY_PRINT + JSON_UNESCAPED_SLASHES));
    }

    private function getJsonPath(): string
    {
        $json = new JsonFile(Factory::getComposerFile());
        return $json->getPath();
    }
}
