<?php declare(strict_types=1);

namespace Yireo\ReplaceTools\Composer\Model;

use AWS\CRT\HTTP\Request;

class ReplacementCollection
{
    /**
     * @var Replacement[]
     */
    private array $replacements;

    /**
     * @param Replacement[] $replacements
     */
    public function __construct(
        array $replacements = []
    ) {
        $this->replacements = $replacements;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->replacements);
    }

    /**
     * @return bool
     */
    public function empty(): bool
    {
        return count($this->replacements) < 1;
    }

    /**
     * @return Replacement[]
     */
    public function get(): array
    {
        return $this->replacements;
    }

    /**
     * @param Replacement $replacement
     * @return void
     */
    public function add(Replacement $replacement): void
    {
        $this->replacements[] = new Replacement($this->toMagentoNs($replacement->getComposerName()), $replacement->getVersion());

        if (preg_match('#^(magento|mage-os)\/#', $replacement->getComposerName())) {
            $this->replacements[] = new Replacement($this->toMageOSNs($replacement->getComposerName()), $replacement->getVersion());
        }
    }

    public function remove(Replacement $removeReplacement)
    {
        foreach ($this->replacements as $index => $replacement) {
            if ($this->toMagentoNs($removeReplacement->getComposerName()) === $replacement->getComposerName()) {
                unset($this->replacements[$index]);
            }

            if ($this->toMageOSNs($removeReplacement->getComposerName()) === $replacement->getComposerName()) {
                unset($this->replacements[$index]);
            }
        }
    }

    /**
     * @param ReplacementCollection $replacements
     */
    public function merge(ReplacementCollection $replacements)
    {
        foreach ($replacements->get() as $replacement) {
            $this->add($replacement);
        }
    }

    /**
     * @param Replacement $searchReplacement
     * @return bool
     */
    public function contains(Replacement $searchReplacement): bool
    {
        foreach ($this->replacements as $replacement) {
            if ($replacement->getComposerName() === $searchReplacement->getComposerName()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string[]
     */
    public function toArray(): array
    {
        $replacementArray = [];
        foreach ($this->replacements as $replacement) {
            $replacementArray[$replacement->getComposerName()] = $replacement->getVersion();
        }

        return $replacementArray;
    }

    private function toMagentoNs(string $composerName): string
    {
        return preg_replace('#^mage-os\/#', 'magento/', $composerName);
    }

    private function toMageOSNs(string $composerName): string
    {
        return preg_replace('#^magento\/#', 'mage-os/', $composerName);
    }
}
