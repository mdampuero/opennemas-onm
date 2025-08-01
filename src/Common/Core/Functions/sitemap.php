<?php

/**
 * Returns the date of the sitemap or the current date if the sitemap doesn't exists.
 *
 * @param string $path The path to the sitemap.
 *
 * @return string The date of the sitemap.
 */
function get_sitemap_date($path)
{
    return getService('core.helper.sitemap')->getSitemapDate($path);
}
