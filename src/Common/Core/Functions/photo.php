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
 * Returns the srcset of the provided photo path.
 *
 * @param string $device The device to get sizes from.
 *
 * @return string The srcset to show.
 */
function get_photo_sizes($device = 'desktop')
{
    return getService('core.helper.photo')->getPhotoSizes($device);
}

/**
 * Returns the srcset of the provided photo path.
 *
 * @param string  $photo     The photo to get srcset from.
 * @param string  $transform The transformation to apply.
 * @param string  $device    The type of device to get srcset for.
 *
 * @return string The srcset to show.
 */
function get_photo_srcset($photo, $transform, $device = 'desktop')
{
    return getService('core.helper.photo')->getPhotoSrcSet($photo, $transform, $device);
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

/**
 * Returns if the provided data has srcset.
 *
 * @param string  $path      The photo path.
 * @param string  $transform The name of the transformation to apply.
 * @param integer $height    The height of the transformation or null.
 * @param integer $width     The width of the transformation or null.
 *
 * @return bool Wether the photo has srcset or not.
 */
function has_photo_srcset($path, $transform = null, $height = null, $width = null)
{
    return getService('core.helper.photo')->hasPhotoSrcSet($path, $transform, $height, $width);
}
