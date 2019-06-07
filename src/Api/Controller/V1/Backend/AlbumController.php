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

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AlbumController extends ContentOldController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'ALBUM_MANAGER';

    /**
     * The route name to generate URL from when creating a new item.
     *
     * @var string
     */
    protected $getItemRoute = 'api_v1_backend_album_show';

    /**
     * {@inheritDoc}
     */
    public function getExtraData($items = null)
    {
        return array_merge(parent::getExtraData($items), [
            'categories' => $this->getCategories($items),
            'photos'     => $this->getPhotos($items),
            'tags'       => $this->getTags($items)
        ]);
    }

    /**
     * {@inheritDoc}`
     */
    public function getL10nKeys()
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
            ->filter('mapify', [ 'key' => 'pk_photo' ])
            ->get();
    }
}
