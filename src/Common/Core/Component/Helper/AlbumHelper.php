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
     * The related helper.
     *
     * @var RelatedHelper
     */
    protected $relatedHelper;

    /**
     * Initializes the AlbumHelper.
     *
     * @param ContentHelper $contentHelper The content helper.
     * @param RelatedHelper $relatedHelper The related helper.
     */
    public function __construct(ContentHelper $contentHelper, RelatedHelper $relatedHelper)
    {
        $this->contentHelper = $contentHelper;
        $this->relatedHelper = $relatedHelper;
    }
    /**
     * Returns the list of photos of the album.
     *
     * @param Content $item The album.
     * @param int     $max  The max number of photos.
     *
     * @return array The list of photos.
     */
    public function getAlbumPhotos($item, $max = null) : array
    {
        $photos = $this->relatedHelper->getRelated(
            $this->contentHelper->getContent($item),
            'photo'
        );

        return !empty($max)
            ? array_slice($photos, 0, $max)
            : $photos;
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
