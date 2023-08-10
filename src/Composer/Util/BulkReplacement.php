<?php declare(strict_types=1);

namespace Yireo\ReplaceTools\Composer\Util;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Yireo\ReplaceTools\Composer\Exception\HttpClientException;
use Yireo\ReplaceTools\Composer\Exception\PackageException;

class BulkReplacement
{
    private string $packageName;

    public function __construct(
        string $packageName
    ) {
        $this->packageName = $packageName;
    }

    /**
     * @return void
     * @throws HttpClientException
     * @throws PackageException
     * @throws GuzzleException
     */
    public function fetchReplacements(): array
    {
        $client = new Client();
        $response = $client->get('https://repo.packagist.org/p2/'.$this->packageName.'.json');
        if ($response->getStatusCode() !== 200) {
            throw new HttpClientException('Packagist call failed: '.$response->getReasonPhrase());
        }

        $body = $response->getBody();
        $data = json_decode($body->getContents(), true);
        if (empty($data) || empty($data['packages']) || empty($data['packages'][$this->packageName])) {
            throw new PackageException('Unknown package "'.$this->packageName.'"');
        }

        if (empty($data['packages'][$this->packageName][0]['replace'])) {
            return [];
        }

        return $data['packages'][$this->packageName][0]['replace'];
    }
}
