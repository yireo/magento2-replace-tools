<?php declare(strict_types=1);

namespace Yireo\ReplaceTools\Composer\Model;

use Composer\Factory;
use Composer\IO\BufferIO;
use Composer\IO\NullIO;
use Composer\Package\BasePackage;
use Composer\Repository\RepositoryFactory;
use Composer\Repository\RepositoryManager;
use Composer\Util\HttpDownloader;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Yireo\ReplaceTools\Composer\Exception\EmptyBulkException;
use Yireo\ReplaceTools\Composer\Exception\HttpClientException;
use Yireo\ReplaceTools\Composer\Exception\PackageException;

class BulkReplacement
{
    /**
     * @var string
     */
    private string $composerName;

    /**
     * @param string $composerName
     */
    public function __construct(
        string $composerName
    ) {
        $this->composerName = $composerName;
    }

    /**
     * @return string
     */
    public function getComposerName(): string
    {
        return $this->composerName;
    }

    /**
     * @return int
     * @throws GuzzleException
     * @throws HttpClientException
     * @throws PackageException
     */
    public function count(): int
    {
        return $this->fetch()->count();
    }

    /**
     * @param Replacement $search
     * @return bool
     * @throws GuzzleException
     * @throws HttpClientException
     * @throws PackageException
     */
    public function contains(Replacement $search): bool
    {
        foreach ($this->fetch()->get() as $replacement) {
            if ($replacement->getComposerName() === $search->getComposerName()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return ReplacementCollection
     * @throws HttpClientException
     * @throws PackageException
     * @throws GuzzleException
     */
    public function fetch(): ReplacementCollection
    {
        static $collections = [];
        if (isset($collections[$this->getComposerName()])) {
            return $collections[$this->getComposerName()];
        }

        $io = new BufferIO();
        $composerConfig = Factory::createConfig($io, getcwd());

        $localComposerFile = getcwd().'/composer.json';
        if (file_exists($localComposerFile)) {
            $localComposerConfig = json_decode(file_get_contents($localComposerFile), true);
            $composerConfig->merge($localComposerConfig);
        }

        $httpDownloader = Factory::createHttpDownloader($io, $composerConfig);
        $repositoryManager = RepositoryFactory::manager($io, $composerConfig, $httpDownloader);

        $composerRepositories = $composerConfig->getRepositories();
        //if (empty($composerRepositories)) {
            //echo "No composer repositories found\n";
        //}

        $bulkReplacementPackage = null;
        foreach ($composerRepositories as $composerRepository) {
            //echo $composerRepository['url']."\n"; // @todo: Add debugging output
            $repository = $repositoryManager->createRepository($composerRepository['type'], $composerRepository);
            $bulkReplacementPackage = $repository->findPackage($this->composerName, '*');

            if ($bulkReplacementPackage instanceof BasePackage) {
                echo $bulkReplacementPackage->getName().":".$bulkReplacementPackage->getVersion()."\n"; // @todo: Add debugging output

                break;
            }
        }

        if (false === $bulkReplacementPackage instanceof BasePackage) {
            throw new EmptyBulkException('No bulk package found with name "'.$this->composerName.'"');
        }

        $collection = new ReplacementCollection;
        foreach ($bulkReplacementPackage->getReplaces() as $replace) {
            $collection->add(new Replacement($replace->getTarget(), $replace->getPrettyConstraint()));
        }

        $collections[$this->getComposerName()] = $collection;
        //@todo echo $io->getOutput();

        return $collection;
    }
}
