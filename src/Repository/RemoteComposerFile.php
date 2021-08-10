<?php declare(strict_types=1);

namespace Yireo\ReplaceTools\Repository;

use Exception;

/**
 * Class RemoteComposerFile
 * @package Yireo\ReplaceTools\Repository
 */
class RemoteComposerFile extends AbstractComposerFile
{
    /**
     * @var string
     */
    private $githubAccount = 'yireo';

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function load(): array
    {
        $remoteUrl = 'https://raw.githubusercontent.com/' . $this->githubAccount . '/' . $this->repository->getName() . '/' . $this->branch . '/composer.json';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $remoteUrl);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $body = curl_exec($ch);
        curl_close($ch);

        if (empty($body)) {
            throw new Exception('Empty body for URL: ' . $remoteUrl);
        }

        $this->data = json_decode($body, true);
        if (empty($this->data)) {
            throw new Exception('Empty data for URL: ' . $remoteUrl);
        }

        return $this->data;
    }
}
