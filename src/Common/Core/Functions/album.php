<?php

/**
 * Returns the list of photos of the album.
 *
 * @param Content $item The album.
 * @param int     $max  The max number of photos.
 *
 * @return array The list of photos.
 */
function get_album_photos($item, $max = null) : array
{
    return getService('core.helper.album')->getAlbumPhotos($item, $max);
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
    return getService('core.helper.album')->hasAlbumPhotos($item);
}
