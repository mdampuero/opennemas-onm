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
    protected $getItemRoute = 'api_v1_backend_video_show';

    /**
     * {@inheritDoc}
     **/
    public function getExtraData($items = null)
    {
        return array_merge(parent::getExtraData($items), [
            'photos' => $this->getPhotos($items)
        ]);
    }

    /**
     * {@inheritDoc}`
     **/
    public function getL10nKeys()
    {
        return $this->get($this->service)->getL10nKeys('album');
    }

    /**
     * Returns the list of photos, description and its positions for a list of photos
     *
     * @param Album $item The item to fetch photos from
     * @return array
     **/
    public function getPhotos($items)
    {
        $photos = [];

        if (!is_array($items)) {
            $items = [ $items ];
        }

        foreach ($items as $item) {
            $ids = array_map(function ($photo) {
                return $photo['pk_photo'];
            }, $item->photos);

            // Use id as array key
            $evenMore = $this->get('api.service.content_old')->getListByIds($ids)['items'];

            foreach ($evenMore as $ph) {
                $photos[] = $ph;
            }
        }

        return $photos;
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
        $em    = $this->get('entity_repository');
        $extra = [];

        if (empty($content)) {
            return $extra;
        }

        if (is_object($content)) {
            $content = [ $content ];
        }

        foreach ($content as $item) {
            if (empty($content->cover_id)) {
                continue;
            }

            $photo = $em->find('Photo', $item->cover_id);

            $extra[] = \Onm\StringUtils::convertToUtf8($photo);
        }

        return $extra;
    }
}
