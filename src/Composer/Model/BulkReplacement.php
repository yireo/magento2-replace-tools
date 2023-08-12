<?php declare(strict_types=1);

namespace Yireo\ReplaceTools\Composer\Model;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
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

        $client = new Client();
        $response = $client->get('https://repo.packagist.org/p2/'.$this->composerName.'.json');
        if ($response->getStatusCode() !== 200) {
            throw new HttpClientException('Packagist call failed: '.$response->getReasonPhrase());
        }

        $body = $response->getBody();
        $data = json_decode($body->getContents(), true);
        if (empty($data) || empty($data['packages']) || empty($data['packages'][$this->composerName])) {
            throw new PackageException('Unknown package "'.$this->composerName.'"');
        }

        $collection = new ReplacementCollection;
        if (empty($data['packages'][$this->composerName][0]['replace'])) {
            return $collection;
        }

        foreach ($data['packages'][$this->composerName][0]['replace'] as $package => $version) {
            $collection->add(new Replacement($package, $version));
        }

        $collections[$this->getComposerName()] = $collection;
        return $collection;
    }
}
