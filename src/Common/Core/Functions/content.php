<?php

/**
 * Returns the body for the provided item.
 *
 * @param Content $item The item to get property from.
 *
 * @return string The content body.
 */
function get_body($item = null) : ?string
{
    return getService('core.helper.content')->getBody($item);
}

/**
 * Returns the caption for an item.
 *
 * @param mixed $item The item to get caption from.
 *
 * @return string The item caption when the photo is provided as an array (with
 *                the object, the position in the list of related contents of
 *                the same type and the caption).
 */
function get_caption($item = null) : ?string
{
    return getService('core.helper.content')->getCaption($item);
}

/**
 * Returns the content of specified type for the provided item.
 *
 * @param mixed  $item The item to return or the id of the item to return. If
 *                     not provided, the function will try to search the item in
 *                     the template.
 * @param string $type Content type used to find the content when an id
 *                     provided as first parameter.
 *
 * @return Content The content.
 */
function get_content($item = null, $type = null)
{
    return getService('core.helper.content')->getContent($item, $type);
}

/**
 * Returns the creation date for the provided item.
 *
 * @param Content $item The item to get property from.
 *
 * @return string The content creation date.
 */
function get_creation_date($item = null) : ?\Datetime
{
    return getService('core.helper.content')->getCreationDate($item);
}

/**
 * Returns the description for the provided item.
 *
 * @param Content $item The item to get property from.
 *
 * @return string The content description.
 */
function get_description($item = null) : ?string
{
    return getService('core.helper.content')->getDescription($item);
}

/**
 * Returns the id of an item.
 *
 * @param Content $content The content to get id from.
 *
 * @return int The content id.
 */
function get_id($item) : ?int
{
    return getService('core.helper.content')->getId($item);
}

/**
 * Returns the pretitle for the provided item.
 *
 * @param Content $item The item to get property from.
 *
 * @return string The content pretitle.
 */
function get_pretitle($item = null) : ?string
{
    return getService('core.helper.content')->getPretitle($item);
}

/**
 * Returns a property for the provided item.
 *
 * @param Content $item The item to get property from.
 * @param string  $name The property name.
 *
 * @return mixed The property value.
 */
function get_property($item, string $name)
{
    return getService('core.helper.content')->getProperty($item, $name);
}

/**
 * Returns the publication date for the provided item.
 *
 * @param Content $item The item to get property from.
 *
 * @return string The content publication date.
 */
function get_publication_date($item = null) : ?\Datetime
{
    return getService('core.helper.content')->getPublicationDate($item);
}

/**
 * Returns the summary for the provided item.
 *
 * @param Content $item The item to get property from.
 *
 * @return string The content summary.
 */
function get_summary($item = null) : ?string
{
    return getService('core.helper.content')->getSummary($item);
}

/**
 * Returns the title for the provided item.
 *
 * @param Content $item The item to get property from.
 *
 * @return string The content title.
 */
function get_title($item = null) : ?string
{
    return getService('core.helper.content')->getTitle($item);
}

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
    return getService('core.helper.content')->getRelated($item, $type);
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
    return getService('core.helper.content')->getRelatedContents($item, $type);
}

/**
 * Returns the list of tags for the provided item.
 *
 * @param Content $item The item to get tags from.
 *
 * @return array The list of tags.
 */
function get_tags($item = null) : array
{
    return getService('core.helper.content')->getTags($item);
}

/**
 * Returns the internal type or human-readable type for the provided item.
 *
 * @param Content $item     The item to get content type for.
 * @param bool    $readable True if the instance and item have comments enabled. False
 *                          otherwise.
 *
 * @param string The internal or human-readable type.
 */
function get_type($item = null, bool $readable = false) : ?string
{
    return getService('core.helper.content')->getType($item, $readable);
}

/**
 * Check if the content has a body.
 *
 * @param Content $item The item to check body for.
 *
 * @return bool True if the content has a body. False otherwise.
 */
function has_body($item = null) : bool
{
    return getService('core.helper.content')->hasBody($item);
}

/**
 * Checks if the item has a caption.
 *
 * @param mixed $item The item to get caption from.
 *
 * @return bool True if the item is provided as an array (with the object, the
 *              position in the list of related contents of the same type and
 *              the caption) and the caption is not empty.
 */
function has_caption($item = null) : bool
{
    return getService('core.helper.content')->hasCaption($item);
}

/**
 * Checks if the content has comments enabled or not.
 *
 * @param Content $item The item to get if comments are enabled.
 *
 * @return bool True if enabled, false otherwise.
 */
function has_comments_enabled($item = null) : bool
{
    return getService('core.helper.content')->hasCommentsEnabled($item);
}

/**
 * Check if the content has a description.
 *
 * @param Content $item The item to check description for.
 *
 * @return bool True if the content has a description. False otherwise.
 */
function has_description($item) : bool
{
    return getService('core.helper.content')->hasDescription($item);
}

/**
 * Check if the content has a pretitle.
 *
 * @param Content $item The item to check pretitle for.
 *
 * @return bool True if the content has a pretitle. False otherwise.
 */
function has_pretitle($item) : bool
{
    return getService('core.helper.content')->hasPretitle($item);
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
    return getService('core.helper.content')->hasRelatedContents($item, $type);
}

/**
 * Checks if the content has a summary.
 *
 * @param Content $item The item to check summary for.
 *
 * @return bool True if the content has a summary. False otherwise.
 */
function has_summary($item) : bool
{
    return getService('core.helper.content')->hasSummary($item);
}

/**
 * Checks if the content has tags.
 *
 * @param Content $item The item to check tags for.
 *
 * @return bool True if the content has tags. False otherwise.
 */
function has_tags($item = null) : bool
{
    return getService('core.helper.content')->hasTags($item);
}

/**
 * Checks if the content has a title.
 *
 * @param Content $item The item to check title for.
 *
 * @return bool True if the content has a title. False otherwise.
 */
function has_title($item) : bool
{
    return getService('core.helper.content')->hasTitle($item);
}
