<?php

use Api\Exception\GetItemException;
use Repository\EntityManager;

/**
 * Returns the body for the provided item.
 *
 * @param Content $item The item to get property from.
 *
 * @return string The content body.
 */
function get_body($item = null) : ?string
{
    $map = [
        'album' => 'description',
        'poll'  => 'description',
        'video' => 'description'
    ];

    $value = array_key_exists(get_type($item), $map) ? get_property($item, $map[get_type($item)]) :
        get_property($item, 'body');

    return !empty($value) ? $value : null;
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
    if (!is_array($item)) {
        return null;
    }

    return array_key_exists('caption', $item)
        ? htmlentities($item['caption'])
        : null;
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
    $item = $item ?? getService('core.template.frontend')->getValue('item');

    // Item as a related content (array with item + caption + position)
    if (is_array($item) && array_key_exists('item', $item)) {
        $item = $item['item'];
    }

    if (!is_object($item) && is_numeric($item) && !empty($type)) {
        try {
            $item = getService('entity_repository')->find($type, $item);
        } catch (GetItemException $e) {
            return null;
        }
    }

    if (!$item instanceof \Common\Model\Entity\Content
        && !$item instanceof \Content
    ) {
        return null;
    }

    return getService('core.helper.content')->isReadyForPublish($item) ? $item : null;
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
    $value = get_property($item, 'created');

    return is_object($value) ? $value : new \Datetime($value);
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
 * @param bool   $deep Whether to return the final featured media. Fox example,
 *                     if the featured media is a video, with true, the
 *                     function will return the thumbnail of the video but,
 *                     with false, the function will return the video.
 *
 * @return Content The featured media.
 */
function get_featured_media($item, $type, $deep = true)
{
    $item = get_content($item);
    $map  = [
        'article' => [
            'frontpage' => [ 'featured_frontpage' ],
            'inner'     => [ 'featured_inner' ]
        ], 'opinion' => [
            'frontpage' => [ 'featured_frontpage' ],
            'inner'     => [ 'featured_inner' ]
        ], 'album' => [
            'frontpage' => [ 'featured_frontpage' ],
            'inner'     => []
        ], 'event' => [
            'frontpage' => [ 'featured_frontpage' ],
            'inner'     => [ 'featured_inner' ]
        ], 'video' => [
            'frontpage' => [ 'featured_frontpage' ],
            'inner'     => []
        ], 'book' => [
            'frontpage' => [ 'cover_id' ],
            'inner'     => []
        ], 'special' => [
            'frontpage' => [ 'img1' ],
            'inner'     => [ ]
        ]
    ];

    if (empty($item)
        || !array_key_exists(get_type($item), $map)
        || !array_key_exists($type, $map[get_type($item)])
    ) {
        return null;
    }

    if (in_array(get_type($item), EntityManager::ORM_CONTENT_TYPES)) {
        if (get_type($item) === 'video') {
            return $type === 'inner' ? $item : get_video_thumbnail($item);
        }

        if (get_type($item) === 'album' && $type === 'inner') {
            return $item;
        }

        $related = get_related($item, $map[get_type($item)][$type][0]);

        if (empty($related)) {
            return null;
        }

        $media = get_content(array_shift($related));

        if ($deep &&
            in_array(get_type($media), [ 'video', 'album' ]) &&
            $type === 'frontpage'
            ) {
            return get_featured_media($media, 'frontpage');
        }

        return $media;
    }

    foreach ($map[get_type($item)][$type] as $key) {
        if (empty($item->{$key})) {
            continue;
        }

        $featured = null;

        if ($item->external) {
            $related  = getService('core.template.frontend')->getValue('related', []);
            $featured = array_key_exists($item->{$key}, $related) ? $related[$item->{$key}] : null;
        } else {
            $featured = get_content(
                $item->{$key},
                preg_match('/img|cover|thumbnail/', $key) ? 'Photo' : 'Video'
            );
        }
    }

    return !empty($featured) ? $featured : null;
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
            'frontpage' => [ 'featured_frontpage' ],
            'inner'     => [ 'featured_inner' ]
        ], 'opinion' => [
            'frontpage' => [ 'featured_frontpage' ],
            'inner'     => [ 'featured_inner' ]
        ], 'album' => [
            'frontpage' => [],
            'inner'     => []
        ], 'event' => [
            'frontpage' => [ 'featured_frontpage' ],
            'inner'     => [ 'featured_inner' ]
        ], 'video' => [
            'frontpage' => [ 'featured_frontpage' ]
        ]
    ];

    if (empty($item)
        || !array_key_exists(get_type($item), $map)
        || !array_key_exists($type, $map[get_type($item)])
    ) {
        return null;
    }

    if (in_array(get_type($item), EntityManager::ORM_CONTENT_TYPES)) {
        $key = array_shift($map[get_type($item)][$type]);

        $related = array_filter($item->related_contents, function ($a) use ($key) {
            return $a['type'] === $key;
        });

        return !empty($related)
            ? htmlentities(array_shift($related)['caption'])
            : null;
    }

    foreach ($map[get_type($item)][$type] as $key) {
        if (!empty($item->{$key})) {
            return htmlentities($item->{$key});
        }
    }

    return null;
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
    $item = get_content($item);

    return empty($item) ? null : $item->pk_content;
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
 * Returns the publication date for the provided item.
 *
 * @param Content $item The item to get property from.
 *
 * @return string The content publication date.
 */
function get_publication_date($item = null) : ?\Datetime
{
    $value = get_property($item, 'starttime') ?? get_property($item, 'created');

    return is_object($value) ? $value : new \Datetime($value);
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
    if ($item->content_type_name === 'opinion') {
        return get_property($item, 'description');
    }

    $value = get_property($item, 'summary');

    //TODO: Recover use of htmlentities when possible
    return !empty($value) ? $value : null;
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
    if (empty($item->related_contents)) {
        return [];
    }

    $items = array_filter($item->related_contents, function ($a) use ($type) {
        return $a['type'] === $type;
    });

    usort($items, function ($a, $b) {
        return $a['position'] <=> $b['position'];
    });

    if ($item->external) {
        $related = getService('core.template.frontend')->getValue('related');

        return array_filter(array_map(function ($a) use ($related) {
            return $related[$a['target_id']];
        }, $items));
    }

    return array_filter(array_map(function ($a) {
        $content = get_content($a['target_id'], $a['content_type_name']);

        return empty($content)
            ? null
            : [
                'item' => $content,
                'caption' => $a['caption'],
                'position' => $a['position']
            ];
    }, $items));
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
    return get_related($item, 'related_' . $type);
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
    $value = get_property($item, 'tags');

    if (empty($value)) {
        return [];
    }

    try {
        return getService('api.service.tag')->getListByIds($value)['items'];
    } catch (\Exception $e) {
        return [];
    }
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
 * Check if the content has a body.
 *
 * @param Content $item The item to check body for.
 *
 * @return bool True if the content has a body. False otherwise.
 */
function has_body($item = null) : bool
{
    $token = getService('core.template.frontend')->getValue('o_token');

    return !empty(get_body($item))
        && !getService('core.helper.subscription')->isHidden($token, 'body');
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
    return !empty(get_caption($item));
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
    return !empty(get_property($item, 'with_comment'));
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

    return !empty(get_featured_media($item, $type))
        && !getService('core.helper.subscription')->isHidden($token, 'media');
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

    return !empty(get_featured_media_caption($item, $type))
        && !getService('core.helper.subscription')->isHidden($token, 'media');
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

    return !empty(get_pretitle($item))
        && !getService('core.helper.subscription')->isHidden($token, 'pretitle');
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
    $token = getService('core.template.frontend')->getValue('o_token');

    return !empty(get_related_contents($item, $type))
        && !getService('core.helper.subscription')->isHidden($token, 'related_contents');
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
    $token = getService('core.template.frontend')->getValue('o_token');

    return !empty(get_summary($item))
        && !getService('core.helper.subscription')->isHidden($token, 'summary');
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
    return !empty(get_tags($item));
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
    $token = getService('core.template.frontend')->getValue('o_token');

    return !empty(get_title($item))
        && !getService('core.helper.subscription')->isHidden($token, 'title');
}
