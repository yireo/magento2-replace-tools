<?php

declare(strict_types=1);

namespace Yireo\ReplaceTools;

use Exception;
use Github\Client;

/**
 * Class ClientFactory
 * @package Yireo\ReplaceTools
 */
class ClientFactory
{
    /**
     * @return Client
     * @throws Exception
     */
    static public function getClient(): Client
    {
        $environment = require(__DIR__ . '/../.env.php');
        if (empty($environment['GITHUB_TOKEN']) || empty($environment['GITHUB_PASSWORD'])) {
            throw new Exception('GitHub token or password are missing');
        }

        $client = new Client();
        $client->authenticate(
            $environment['GITHUB_TOKEN'],
            $environment['GITHUB_PASSWORD'],
            Client::AUTH_HTTP_TOKEN
        );

        return $client;
    }
}
