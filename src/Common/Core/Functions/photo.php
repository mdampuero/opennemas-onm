<?php

use Framework\Component\MIME\MimeTypeTool;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Returns the URL for a photo.
 *
 * @param Content $item      The photo to generate path for.
 * @param string  $transform The transform to apply.
 * @param array   $params    The list of parameters for the transform.
 * @param bool    $absolute  Wheter to generate an absolute URL.
 *
 * @return string The URL for the image.
 */
function get_photo_path($item, string $transform = null, array $params = [], $absolute = false)
{
    if (empty($item)) {
        return null;
    }

    if (is_string($item)) {
        return $item;
    }

    $absolute = $absolute
        ? UrlGeneratorInterface::ABSOLUTE_URL
        : UrlGeneratorInterface::ABSOLUTE_PATH;

    $url = getService('core.helper.url_generator')->generate($item);

    // Do not transform if empty or external photo
    if (empty($transform) || preg_match('/^https?.*/', $url)) {
        return $url;
    }

    return getService('router')->generate('asset_image', [
        'params' => implode(',', array_merge([ $transform ], $params)),
        'path'   => $url
    ], $absolute);
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
        $instance = getService('core.instance');
        $path     = $instance->getBaseUrl() . $path;
    }
    $value = MimeTypeTool::getMimeType($path);

    return !empty($value) ? $value : null;
}

/**
 * Returns if the provided photo has size or not.
 *
 * @param mixed $item The photo to get size from.
 *
 * @return boolean True if the photo has size. False otherwise.
 */
function has_photo_size($item = null)
{
    return !empty(get_photo_size($item));
}
