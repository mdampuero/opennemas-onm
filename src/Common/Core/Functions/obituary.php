<?php
/**
 * Returns a property for the provided item.
 *
 * @param Content $item        The item to get property from.
 *
 * @return mixed The date value.
 */
function get_date($item = null)
{
    return getService('core.helper.obituary')->getDate($item);
}

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

/**
 * Returns true if the obituary has date.
 *
 * @param Content $item The item to check if has date or not.
 *
 * @return Boolean True if the item has date, False otherwise.
 */
function has_date($item = null)
{
    return getService('core.helper.obituary')->hasDate($item);
}

/**
 * Returns true if the obituary has google maps url.
 *
 * @param Content $item The item to check if has url or not.
 *
 * @return Boolean True if the item has url, False otherwise.
 */
function has_maps($item = null)
{
    return getService('core.helper.obituary')->hasMaps($item);
}

/**
 * Returns true if the obituary has mortuary.
 *
 * @param Content $item The item to check if has mortuary or not.
 *
 * @return Boolean True if the item has mortuary, False otherwise.
 */
function has_mortuary($item = null)
{
    return getService('core.helper.obituary')->hasMortuary($item);
}

/**
 * Returns true if the obituary has website.
 *
 * @param Content $item The item to check if has website or not.
 *
 * @return Boolean True if the item has website, False otherwise.
 */
function has_website($item = null)
{
    return getService('core.helper.obituary')->hasWebsite($item);
}
