<?php declare(strict_types=1);

namespace Yireo\ReplaceTools\Repository;

use Exception;

/**
 * Class ComposerFile
 * @package Yireo\ReplaceTools\Repository
 */
class LocalComposerFile extends AbstractComposerFile
{
    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function load(): array
    {
        $file = $this->repository->getFolder() . '/composer.json';
        if (!is_file($file)) {
            throw new Exception('No file found: ' . $file);
        }

        $contents = file_get_contents($file);
        if (empty($contents)) {
            throw new Exception('Empty content: ' . $file);
        }

        $this->data = json_decode($contents, true);
        if (empty($this->data)) {
            throw new Exception('Empty data: ' . $file);
        }

        return $this->data;
    }
}
