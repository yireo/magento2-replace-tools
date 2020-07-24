<?php

declare(strict_types=1);

namespace Yireo\ReplaceTools;

use Exception;
use Github\Client;
use Github\Exception\MissingArgumentException;
use RuntimeException;
use Yireo\ReplaceTools\Repository\LocalComposerFile;
use Yireo\ReplaceTools\Repository\RemoteComposerFile;
use Gitonomy\Git\Repository as GitRepository;
use Yireo\ReplaceTools\Util\VersionUtil;

/**
 * Class Repository
 * @package Yireo\ReplaceTools
 */
class Repository
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $accountName;

    /**
     * @var GitRepository
     */
    private $gitRepository;

    /**
     * @var Client
     */
    private $client;

    /**
     * Repository constructor.
     * @param string $name
     * @param string $accountName
     * @throws Exception
     */
    public function __construct(string $name, string $accountName)
    {
        $this->name = $name;
        $this->accountName = $accountName;
        $this->gitRepository = new GitRepository($this->getFolder());
        $this->client = ClientFactory::getClient();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getName();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getFolder(): string
    {
        $folder = FilesystemResolver::getInstance()->getRootFolder() . '/' . $this->name;
        if (!is_dir($folder)) {
            throw new Exception('Folder "' . $folder . '" does not exist');
        }

        return $folder;
    }


    /**
     * @param string $branch
     * @return LocalComposerFile
     * @throws Exception
     */
    public function getLocalComposerFile(string $branch): LocalComposerFile
    {
        $this->setBranch($branch);
        return new LocalComposerFile($this, $branch);
    }

    /**
     * @param LocalComposerFile $composerFile
     * @param $branch
     * @throws Exception
     */
    public function saveComposerFile(LocalComposerFile $composerFile, $branch)
    {
        $this->setBranch($branch);
        file_put_contents($this->getFolder() . '/composer.json', $composerFile->getContents());
        exec('git commit -qm "Updating composer file automatically" composer.json');
        exec('git push origin ' . $branch);
    }

    /**
     * @param string $branch
     * @return RemoteComposerFile
     * @throws Exception
     */
    public function getRemoteComposerFile(string $branch): RemoteComposerFile
    {
        $this->setBranch($branch);
        return new RemoteComposerFile($this, $branch);
    }

    /**
     * @return array
     */
    public function getAllReleases(): array
    {
        return $this->client->api('repo')->releases()->all($this->accountName, $this->name);
    }

    /**
     * @param string $branchName
     * @return array
     */
    public function getReleasesByBranch(string $branchName): array
    {
        $allReleases = $this->getAllReleases();
        $branchReleases = [];
        foreach ($allReleases as $release) {
            $releaseTag = $release['tag_name'];
            if ($release['target_commitish'] === $branchName) {
                $branchReleases[$releaseTag] = $release;
            }
        }

        if (empty($branchReleases)) {
            throw new RuntimeException('No releases found for branch "' . $branchName . '"');
        }

        return $branchReleases;
    }

    /**
     * @param string $branchName
     * @return array
     */
    public function getLatestReleaseByBranchName(string $branchName): array
    {
        $branchReleases = $this->getReleasesByBranch($branchName);
        krsort($branchReleases);
        return array_shift($branchReleases);
    }

    /**
     * @param string $branchName
     * @return string
     */
    public function getInitialReleaseByBranchName(string $branchName): string
    {
        return preg_replace('/^magento-2./', '', $branchName).'.0';
    }

    /**
     * @return array
     */
    public function getLatestRelease(): array
    {
        return $this->client->api('repo')->releases()->latest($this->accountName, $this->name);
    }

    /**
     * @param string $branchName
     * @return string
     */
    public function getNewVersionByBranchName(string $branchName): string
    {
        try {
            $latestRelease = $this->getLatestReleaseByBranchName($branchName);
            return (new VersionUtil())->getNewVersion($latestRelease['tag_name']);
        } catch(\RuntimeException $e) {
            return $this->getInitialReleaseByBranchName($branchName);
        }
    }

    /**
     * @param string $branch
     * @return bool
     * @throws Exception
     */
    public function update(string $branch): bool
    {
        chdir($this->getFolder());
        exec('git checkout -q ' . $branch);
        exec('git pull -q origin ' . $branch);
        return true;
    }

    /**
     * @param string $branch
     * @param string $version
     * @return array
     * @throws MissingArgumentException
     */
    public function release(string $branch, string $version): array
    {
        return $this->client->api('repo')->releases()->create(
            $this->accountName,
            $this->name,
            array(
                'tag_name' => $version,
                'target_commitish' => $branch,
                'name' => $version
            )
        );
    }

    /**
     * @param string $branch
     * @throws Exception
     */
    private function setBranch(string $branch)
    {
        chdir($this->getFolder());
        exec('git fetch -q --all');
        exec('git checkout -q ' . $branch);
        exec('git pull -q origin ' . $branch);
    }
}
