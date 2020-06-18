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
        static $client;
        if ($client instanceof Client) {
            return $client;
        }

        $environment = Environment::getEnvironment();

        $client = new Client();
        $client->authenticate(
            $environment['GITHUB_TOKEN'],
            $environment['GITHUB_PASSWORD'],
            Client::AUTH_HTTP_TOKEN
        );

        return $client;
    }
}
