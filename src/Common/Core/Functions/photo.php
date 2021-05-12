<?php

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
    return getService('core.helper.photo')->getPhotoPath($item, $transform, $params, $absolute);
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
    return getService('core.helper.photo')->getPhotoSize($item);
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
    return getService('core.helper.photo')->getPhotoWidth($item);
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
    return getService('core.helper.photo')->getPhotoHeight($item);
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
    return getService('core.helper.photo')->getPhotoMimeType($item);
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
    return getService('core.helper.photo')->hasPhotoSize($item);
}
