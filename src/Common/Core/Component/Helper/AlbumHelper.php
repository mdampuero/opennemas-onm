<?php

namespace Common\Core\Component\Helper;

/**
 * Helper class to retrieve album data.
 */
class AlbumHelper
{
    /**
     * The content helper.
     *
     * @var ContentHelper
     */
    protected $contentHelper;

    /**
     * Initializes the AlbumHelper.
     *
     * @param ContentHelper $contentHelper The content helper.
     */
    public function __construct(ContentHelper $contentHelper)
    {
        $this->contentHelper = $contentHelper;
    }
    /**
     * Returns the list of photos of the album.
     *
     * @param Content $item The album.
     *
     * @return array The list of photos.
     */
    public function getAlbumPhotos($item) : array
    {
        return $this->contentHelper->getRelated($this->contentHelper->getContent($item), 'photo');
    }

    /**
     * Checks if the album has photos.
     *
     * @param Content $item The album.
     *
     * @return bool True if the album has photos. False otherwise.
     */
    public function hasAlbumPhotos($item) : bool
    {
        return !empty($this->getAlbumPhotos($item));
    }
}
