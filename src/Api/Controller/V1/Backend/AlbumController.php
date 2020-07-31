<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Api\Controller\V1\Backend;

use Symfony\Component\HttpFoundation\Request;

class AlbumController extends ContentOldController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'ALBUM_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected $getItemRoute = 'api_v1_backend_album_get_item';

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.album';

    /**
     * {@inheritDoc}
     */
    protected function getExtraData($items = null)
    {
        return array_merge(parent::getExtraData($items), [
            'categories' => $this->getCategories($items),
            'photos'     => $this->getPhotos($items),
            'tags'       => $this->getTags($items)
        ]);
    }

    /**
     * {@inheritDoc}
     */
    protected function getL10nKeys()
    {
        return $this->get($this->service)->getL10nKeys('album');
    }

    /**
     * Returns the list of photos for an item or a list of items.
     *
     * @param mixed $items The item or the list of items to get photos for.
     *
     * @return array The list of photos.
     */
    protected function getPhotos($items = null)
    {
        if (empty($items)) {
            return [];
        }

        if (!is_array($items)) {
            $items = [ $items ];
        }

        $ids = [];

        foreach ($items as $item) {
            if (!empty($item->cover_id)) {
                $ids[] = $item->cover_id;
            }

            $ids = array_unique(array_merge($ids, array_map(function ($photo) {
                return $photo['pk_photo'];
            }, $item->photos)));
        }

        $photos = $this->get('api.service.content_old')
            ->getListByIds($ids)['items'];

        return $this->get('data.manager.filter')
            ->set($photos)
            ->filter('mapify', [ 'key' => 'pk_content' ])
            ->get();
    }

    /**
     * Returns the list of contents related with items.
     *
     * @param Content $content The content.
     *
     * @return array The list of photos linked to the content.
     */
    protected function getRelatedContents($content)
    {
        $service = $this->get('api.service.photo');
        $extra   = [];

        if (empty($content)) {
            return $extra;
        }

        if (is_object($content)) {
            $content = [ $content ];
        }

        foreach ($content as $item) {
            try {
                $photo = $service->getItem($item->cover_id);

                if (!empty($photo)) {
                    $extra[] = $service->responsify($photo);
                }
            } catch (GetItemException $e) {
            }
        }

        return $extra;
    }
}
