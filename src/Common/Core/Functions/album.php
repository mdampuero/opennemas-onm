<?php

/**
 * Returns the list of photos of the album.
 *
 * @param Content $item The album.
 *
 * @return array The list of photos.
 */
function get_album_photos($item) : array
{
    return get_related(get_content($item), 'photo');
}

/**
 * Checks if the album has photos.
 *
 * @param Content $item The album.
 *
 * @return bool True if the album has photos. False otherwise.
 */
function has_album_photos($item) : bool
{
    return !empty(get_album_photos($item));
}
