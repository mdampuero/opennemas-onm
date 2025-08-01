<?php

/**
 * Returns the list of related contents for the provided item based on the
 * relation type.
 *
 * @param Content $item The item to get related contents for.
 * @param string  $type The relation type.
 *
 * @return array The list of related contents.
 */
function get_related($item, string $type) : array
{
    return getService('core.helper.related')->getRelated($item, $type);
}

/**
 * Alias to get_related function to use only for 'related_' types.
 *
 * @param Content $item The item to get related contents for.
 * @param string  $type The type of the related contents (frontpage|inner).
 *
 * @return array The list of related contents.
 */
function get_related_contents($item, string $type) : array
{
    return getService('core.helper.related')->getRelatedContents($item, $type);
}

/**
 * Checks if the item has related contents in the specified relation.
 *
 * @param Content $item The item to check.
 * @param string  $type The relation type.
 *
 * @return bool True if the content has related contents. False otherwise.
 */
function has_related_contents($item, string $type) : bool
{
    return getService('core.helper.related')->hasRelatedContents($item, $type);
}
