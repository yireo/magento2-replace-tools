<?php declare(strict_types=1);

namespace Yireo\ReplaceTools\Composer\Model;

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
        $this->replacements[] = $replacement;
    }

    public function remove(Replacement $replacement)
    {
        foreach ($this->replacements as $index => $r) {
            if ($replacement->getComposerName() === $r->getComposerName()) {
                unset($this->replacements[$index]);
                break;
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
}
