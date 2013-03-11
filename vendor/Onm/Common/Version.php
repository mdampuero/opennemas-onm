<?php
/**
 * Defines the Onm\Common\Version
 *
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Onm_Common
 */
namespace Onm\Common;

/**
 * Class to store and retrieve the version of Onm
 *
 * @package Onm_Common
 */
class Version
{
    /**
     * Current Onm framework Version
     */
    const VERSION = '1.0';

    /**
     * Compares a Onm version with the current one.
     *
     * @param string $version Onm version to compare.
     *
     * @return int Returns -1 if older, 0 if it is the same, 1 if version
     *             passed as argument is newer.
     */
    public static function compare($version)
    {
        $currentVersion = str_replace(' ', '', strtolower(self::VERSION));
        $version = str_replace(' ', '', $version);

        return version_compare($version, $currentVersion);
    }
}
