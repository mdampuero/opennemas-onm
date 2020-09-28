<?php

/**
 * Returns the URL for a photo.
 *
 * @param Content $item      The photo to generate path for.
 * @param string  $transform The transform to apply.
 * @param array   $params    The list of parameters for the transform.
 *
 * @return string The URL for the image.
 */
function get_photo_path($item, string $transform = null, array $params = [])
{
    if (is_string($item)) {
        return $item;
    }

    $url = getService('core.helper.url_generator')->generate($item);

    // Do not transform if empty or external photo
    if (empty($transform) || preg_match('/^https?.*/', $url)) {
        return $url;
    }

    return getService('router')->generate('asset_image', [
        'params' => implode(',', array_merge([ $transform ], $params)),
        'path'   => $url
    ]);
}

/**
 * Returns the size for the provided photo.
 *
 * @param Content $item The photo to get property from.
 *
 * @return string The photo size.
 */
function get_photo_size($item = null) : ?string
{
    $value = get_property($item, 'size');

    return !empty($value) ? $value : null;
}

/**
 * Returns the width for the provided photo.
 *
 * @param Content $item The photo to get property from.
 *
 * @return string The photo width.
 */
function get_photo_width($item = null) : ?string
{
    $value = get_property($item, 'width');

    return !empty($value) ? $value : null;
}

/**
 * Returns the height for the provided photo.
 *
 * @param Content $item The photo to get property from.
 *
 * @return string The photo height.
 */
function get_photo_height($item = null) : ?string
{
    $value = get_property($item, 'height');

    return !empty($value) ? $value : null;
}

/**
 * Returns the height for the provided photo.
 *
 * @param Content $item The photo to get property from.
 *
 * @return string The photo height.
 */
function get_photo_mime_type($item = null) : ?string
{
    $path = get_photo_path($item);
    if (!preg_match('/^http?.*/', $path)) {
        $path = getService('core.instance')->getMainDomain() . $path;
    }
    $value = mime_content_type($path);

    return !empty($value) ? $value : null;
}
