<?php

function get_photo_path($item, string $transform = null, array $params = [])
{
    $item = get_content($item, 'Photo');

    $url = getService('core.helper.url_generator')->generate($item);

    if (empty($transform)) {
        return $url;
    }

    return getService('router')->generate('asset_image', [
        'params' => implode(',', array_merge([ $transform ], $params)),
        'path'   => $url
    ]);
}
