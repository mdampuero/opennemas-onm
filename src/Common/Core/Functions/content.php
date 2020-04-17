<?php

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
    $item = $item ?? getService('core.template.frontend')->getValue('item');

    if (!is_object($item) && is_numeric($item) && !empty($type)) {
        $item = getService('entity_repository')->find($type, $item);
    }

    return $item instanceof \Common\ORM\Entity\Content
            || $item instanceof \Content
        ? $item
        : null;
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
    $value = get_property($item, 'description');

    return !empty($value) ? htmlentities($value) : null;
}

/**
 * Returns the featured media for the provided item based on the featured type.
 *
 * @param mixed  $item The item to get featured media for.
 * @param string $type The featured type.
 *
 * @return Content The featured media.
 */
function get_featured_media($item, $type)
{
    $item = get_content($item);
    $map  = [
        'article' => [
            'frontpage' => [ 'fk_video', 'img1' ],
            'inner'     => [ 'fk_video2', 'img2' ]
        ], 'opinion' => [
            'frontpage' => [ 'img1' ],
            'inner'     => [ 'img2' ]
        ], 'album' => [
            'frontpage' => [ 'cover_id' ],
            'inner'     => []
        ]
    ];

    if (empty($item)
        || !array_key_exists(get_type($item), $map)
        || !array_key_exists($type, $map[get_type($item)])
    ) {
        return null;
    }

    foreach ($map[get_type($item)][$type] as $key) {
        if (!empty($item->{$key})) {
            return get_content(
                $item->{$key},
                preg_match('/img|cover/', $key) ? 'Photo' : 'Video'
            );
        }
    }

    return null;
}

/**
 * Returns the featured media caption for the provided item based on the
 * featured type.
 *
 * @param mixed  $item The item to get featured media caption for.
 * @param string $type The featured type.
 *
 * @return Content The featured media caption.
 */
function get_featured_media_caption($item, $type)
{
    $item = get_content($item);
    $map  = [
        'article' => [
            'frontpage' => [ 'footer_video1', 'img1_footer' ],
            'inner'     => [ 'footer_video2', 'img2_footer' ]
        ], 'opinion' => [
            'frontpage' => [ 'img1_footer' ],
            'inner'     => [ 'img2_footer' ]
        ], 'album' => [
            'frontpage' => [],
            'inner'     => []
        ]
    ];

    if (empty($item)
        || !array_key_exists(get_type($item), $map)
        || !array_key_exists($type, $map[get_type($item)])
    ) {
        return null;
    }

    foreach ($map[get_type($item)][$type] as $key) {
        if (!empty($item->{$key})) {
            return $item->{$key};
        }
    }

    return null;
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
    $value = get_property($item, 'pretitle');

    return !empty($value) ? htmlentities($value) : null;
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
    $item = get_content($item);

    if (empty($item)) {
        return null;
    }

    return !empty($item->{$name}) ? $item->{$name} : null;
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
    $value = get_property($item, 'summary');

    return !empty($value) ? htmlentities($value) : null;
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
    $value = get_property($item, 'title');

    return !empty($value) ? htmlentities($value) : null;
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
    $value = get_property($item, 'content_type_name');

    return !empty($value)
        ? !$readable ? $value : _(ucfirst(implode(' ', explode('_', $value))))
        : null;
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
    return !empty(get_description($item));
}

/**
 * Check if the content has a featured media content.
 *
 * @param Content $item The item to check featured media for.
 * @param string  $type The featured type.
 *
 * @return bool True if the content has a featured media. False otherwise.
 */
function has_featured_media($item, string $type) : bool
{
    $token = getService('core.template.frontend')->getValue('o_token');

    return !getService('core.helper.subscription')->isHidden($token, 'media')
        && !empty(get_featured_media($item, $type));
}

/**
 * Check if the content has a featured media caption.
 *
 * @param Content $item The item to check featured media for.
 * @param string  $type The featured type.
 *
 * @return bool True if the content has a featured media caption. False
 *              otherwise.
 */
function has_featured_media_caption($item, string $type) : bool
{
    $token = getService('core.template.frontend')->getValue('o_token');

    return !getService('core.helper.subscription')->isHidden($token, 'media')
        && !empty(get_featured_media_caption($item, $type));
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
    $token = getService('core.template.frontend')->getValue('o_token');

    return !getService('core.helper.subscription')->isHidden($token, 'pretitle')
        && !empty(get_pretitle($item));
}

/**
 * Check if the content has a summary.
 *
 * @param Content $item The item to check summary for.
 *
 * @return bool True if the content has a summary. False otherwise.
 */
function has_summary($item) : bool
{
    $token = getService('core.template.frontend')->getValue('o_token');

    return !getService('core.helper.subscription')->isHidden($token, 'summary')
        && !empty(get_summary($item));
}

/**
 * Check if the content has a title.
 *
 * @param Content $item The item to check title for.
 *
 * @return bool True if the content has a title. False otherwise.
 */
function has_title($item) : bool
{
    $token = getService('core.template.frontend')->getValue('o_token');

    return !getService('core.helper.subscription')->isHidden($token, 'title')
        && !empty(get_title($item));
}
