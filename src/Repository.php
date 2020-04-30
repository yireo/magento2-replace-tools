<?php

declare(strict_types=1);

namespace Yireo\ReplaceTools;

use Exception;
use Yireo\ReplaceTools\Repository\LocalComposerFile;
use Yireo\ReplaceTools\Repository\RemoteComposerFile;
use Gitonomy\Git\Repository as GitRepository;

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
     * @var GitRepository
     */
    private $gitRepository;

    /**
     * Repository constructor.
     * @param string $name
     * @throws Exception
     */
    public function __construct(string $name)
    {
        $this->name = $name;
        $this->gitRepository = new GitRepository($this->getFolder());
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

    public function release(string $branch)
    {
        $client = (new ClientFactory())->getClient();
        $releases = $client->api('repo')->releases()->all('twbs', 'bootstrap');
        return $releases;
        //$response = $client->api('repo')->releases()->remove('twbs', 'bootstrap', $id);
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
