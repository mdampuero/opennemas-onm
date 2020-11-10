<?php

/**
 * Returns the video embed html.
 *
 * @param Content $item The item to get embed html for.
 *
 * @return string The embed html.
 */
function get_video_embed_html($item)
{
    $information = get_property($item, 'information');

    return !empty($information['embedHTML']) ? $information['embedHTML'] : null;
}

/**
 * Returns the video embed url.
 *
 * @param Content $item The item to get embed url for.
 *
 * @return string The embed url.
 */
function get_video_embed_url($item)
{
    $information = get_property($item, 'information');

    return !empty($information['embedUrl']) ? $information['embedUrl'] : null;
}

/**
 * Returns the video html.
 *
 * @param Content $item The item to get html for.
 *
 * @return string The html code.
 */
function get_video_html($item, $width = null, $height = null, $amp = false)
{
    $output = get_output($item, $width, $height);

    if ($amp) {
        $output = getService('data.manager.filter')
            ->set($output)
            ->filter('amp')
            ->get();
    }

    return $output;
}

/**
 * Returns the code output for the video.
 *
 * @param Content $item The item to get code for.
 * @param int $width    The width of the video.
 * @param int $height   The height of the video.
 *
 * @return string The html code.
 */
function get_output($item, $width, $height)
{
    $width  = $width ?? '560';
    $height = $height ?? '320';


    if ($item->type === 'script') {
        return sprintf('<div>%s</div>', $item->body);
    }

    $tpl = !empty($item->information['source'])
        ? 'video/render/external.tpl'
        : 'video/render/web-source.tpl';

    return getService('core.template.admin')
        ->fetch(
            $tpl,
            ['info' => $item->information, 'height' => $height, 'width' => $width]
        );
}

/**
 * Returns the type for the provided item.
 *
 * @param Content $item The item to get type for.
 *
 * @return string The content type.
 */
function get_video_type($item)
{
    $value = get_property($item, 'type');

    return !empty($value) ? $value : null;
}

/**
 * Returns the path for the provided item.
 *
 * @param Content $item The item to get path for.
 *
 * @return string The content path.
 */
function get_video_path($item)
{
    $value = get_property($item, 'path');

    return !empty($value) ? $value : null;
}

/**
 * Returns if the video has embed html or not.
 *
 * @param Content $item The item to check if has embed html or not.
 *
 * @return boolean true if has embed html.
 */
function has_video_embed_html($item)
{
    $value = get_video_embed_html($item);

    return !empty($value);
}

/**
 * Returns if the video has embed url or not.
 *
 * @param Content $item The item to check if has embed url or not.
 *
 * @return boolean true if has embed url.
 */
function has_video_embed_url($item)
{
    $value = get_video_embed_url($item);

    return !empty($value);
}

/**
 * Returns if the video has path or not.
 *
 * @param Content $item The item to check if has path or not.
 *
 * @return boolean true if has path.
 */
function has_video_path($item)
{
    $value = get_video_path($item);

    return !empty($value);
}
