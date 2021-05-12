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
    return getService('core.helper.video')->getVideoEmbedHtml($item);
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
    return getService('core.helper.video')->getVideoEmbedUrl($item);
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
    return getService('core.helper.video')->getVideoHtml($item, $width, $height, $amp);
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
    return getService('core.helper.video')->getVideoType($item);
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
    return getService('core.helper.video')->getVideoPath($item);
}

/**
 * Returns the thumbnail for the provided item.
 *
 * @param Content $item The item to get thumbnail from.
 *
 * @return mixed The thumbnail id or the thumbnail string.
 */
function get_video_thumbnail($item)
{
    return getService('core.helper.video')->getVideoThumbnail($item);
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
    return getService('core.helper.video')->hasVideoEmbedHtml($item);
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
    return getService('core.helper.video')->hasVideoEmbedUrl($item);
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
    return getService('core.helper.video')->hasVideoPath($item);
}
