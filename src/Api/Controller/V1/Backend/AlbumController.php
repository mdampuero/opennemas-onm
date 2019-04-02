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
        $us = $this->get('api.service.category');

        $categories = $this->container->get('data.manager.filter')
            ->set($us->getList('inmenu=1')['items'])
            ->filter('mapify', [ 'key' => 'pk_content_category' ])
            ->get();

        return array_merge(parent::getExtraData($items), [
            'categories' => $us->responsify($categories),
        ]);
    }
    /**
     * Returns the list of l10n keys
     * @param Type $var Description
     *
     * @return array
     **/
    public function getL10nKeys()
    {
        return $this->get($this->service)->getL10nKeys('album');
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
            if (is_array($item->information)
                && !array_key_exists('thumbnail', $item->information)
                || empty($item->information['thumbnail'])
            ) {
                continue;
            }

            $photo = $em->find('Photo', $item->information['thumbnail']);

            $extra[] = \Onm\StringUtils::convertToUtf8($photo);
        }

        return $extra;
    }
}
