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
