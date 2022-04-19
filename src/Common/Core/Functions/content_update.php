<?php

/**
 * Get image id from item
 *
 * @param mixed $item The item to get image from.
 *
 * @return int Image id.
 */
function get_update_image($item = null) : int
{
    return getService('core.helper.content_update')->getImage($item);
}

/**
 * Get body from item
 *
 * @param mixed $item The item to get body from.
 *
 * @return string Item body.
 */
function get_update_body($item = null) : string
{
    return getService('core.helper.content_update')->getBody($item);
}

/**
 * Checks if the item has a image.
 *
 * @param mixed $item The item to get image from.
 *
 * @return bool True if image exists, false otherwise
 */
function has_update_image($item = null) : bool
{
    return getService('core.helper.content_update')->hasImage($item);
}

/**
 * Checks if the body exists.
 *
 * @param mixed $item The item to get if body exists.
 *
 * @return bool True if enabled, false otherwise.
 */
function has_update_body($item = null) : bool
{
    return getService('core.helper.content_update')->hasBody($item);
}
