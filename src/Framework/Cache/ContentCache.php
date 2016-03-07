<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Framework\Cache;

/**
 * Handles the content caches
 *
 * @package Framework_Cache
 **/
class ContentCache
{
    /**
     * Gets the earlier starttime of scheduled contents from a contents array
     *
     * @param array $contents Array of Contents.
     *
     * @return string The minor starttime of scheduled contents or null
     */
    public function getEarlierStarttimeOfScheduledContents($contents)
    {
        $current = date('Y-m-d H:i:s');
        $expires = null;
        foreach ($contents as $content) {
            if ($content->starttime > $current
                && (empty($expires)
                    || $content->starttime < $expires)
            ) {
                $expires = $content->starttime;
            }
        }

        return $expires;
    }
}
