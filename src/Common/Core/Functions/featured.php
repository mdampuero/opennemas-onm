<?php

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
    return getService('core.helper.featured_media')->getFeaturedMedia($item, $type, $deep);
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
    return getService('core.helper.featured_media')->getFeaturedMediaCaption($item, $type);
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
    return getService('core.helper.featured_media')->hasFeaturedMedia($item, $type);
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
    return getService('core.helper.featured_media')->hasFeaturedMediaCaption($item, $type);
}
