<?php
/**
 * Returns a property for the provided item.
 *
 * @param Content $item        The item to get property from.
 *
 * @return mixed The maps value.
 */
function get_maps($item = null)
{
    return getService('core.helper.obituary')->getMaps($item);
}

/**
 * Returns a property for the provided item.
 *
 * @param Content $item        The item to get property from.
 *
 * @return mixed The mortuary value.
 */
function get_mortuary($item = null)
{
    return getService('core.helper.obituary')->getMortuary($item);
}

/**
 * Returns a property for the provided item.
 *
 * @param Content $item        The item to get property from.
 *
 * @return mixed The website value.
 */
function get_website($item = null)
{
    return getService('core.helper.obituary')->getWebsite($item);
}
