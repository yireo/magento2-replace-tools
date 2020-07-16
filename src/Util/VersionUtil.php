<?php

declare(strict_types=1);

namespace Yireo\ReplaceTools\Util;

class VersionUtil
{
    /**
     * @param string $version
     * @return string
     */
    public function getNewVersion(string $version): string
    {
        if (preg_match('/^([0-9]+)\.([0-9]+)\.([0-9]+)/', $version, $match)) {
            return $match[1] . '.' . $match[2] . '.' . (int)($match[3] +1);
        }

        return $version;
    }
}
