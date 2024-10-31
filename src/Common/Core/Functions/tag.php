<?php

/**
 * Returns the tag name for the provided item, optionally displaying the header.
 *
 * @param Content|null $item The item to get the tag name for. If not provided,
 *                           the function will try to search the item in the template.
 * @param bool $displayHeader1 Optional. If true, the header for the tag will be displayed.
 *
 * @return ?string The tag name if present; null otherwise.
 */
function get_tag_name($item = null, $displayHeader1 = false) : ?string
{
    return getService('core.helper.tag')->getTagName($item, $displayHeader1);
}
