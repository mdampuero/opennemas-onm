<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Onm\Common;

/**
 * Class to store and retrieve the version of Onm
 *
 * @link    www.opennemas.org
 * @since   0.8
 * @version $Revision$
 * @author  Fran Dieguez <fran@openhost.es>
 */
class Version
{
    /**
     * Current Doctrine Version
     */
    const VERSION = '0.8.2.1';

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
