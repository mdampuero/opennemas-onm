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

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ContentOldController extends ContentController
{
    /**
     * The API service name.
     *
     * @var string
     */
    protected $service = 'api.service.content_old';

    /**
     * Returns a list of extra data
     *
     * @return array
     **/
    protected function getExtraData($items = null)
    {
        return [
            'related_contents' => $this->getRelatedContents($items),
            'tags'             => $this->getTagsFromItems($items),
            'keys'             => $this->getL10nKeys(),
            'authors'          => $this->getAuthors(),
            'locale'           => $this->get('core.helper.locale')->getConfiguration(),
            'template_vars'    => [
                'media_dir' => $this->get('core.instance')->getMediaShortPath() . '/images',
            ],
        ];
    }

    /**
     * Returns the list of l10n keys
     * @param Type $var Description
     *
     * @return array
     **/
    public function getL10nKeys()
    {
        return $this->get($this->service)->getL10nKeys();
    }

    /**
     * Returns the list of contents related with items.
     *
     * @param Content $content The content.
     *
     * @return array The list of photos linked to the content.
     */
    protected function getRelatedContents($items)
    {
        return [];
    }

    /**
     * Returns the list of tag ids for a list of items or a individual item
     *
     * @param array|Content $items One Content object or a list of Content objects
     *
     * @return array
     **/
    private function getTagsFromItems($items = null)
    {
        if (empty($items)) {
            return [];
        }

        if (is_object($items)) {
            $items = [ $items ];
        }

        $tagIds = [];
        if (is_array($items)) {
            foreach ($items as $item) {
                $tagIds = array_merge($tagIds, $item->tag_ids);
            }
        }

        return $this->get('api.service.tag')
            ->getListByIdsKeyMapped($tagIds)['items'];
    }

    /**
     * Returns the lit of authors
     *
     * @return array the list of authors
     **/
    public function getAuthors()
    {
        $us = $this->get('api.service.author');

        $response = $us->getList('order by name asc');
        $authors  = $this->get('data.manager.filter')
            ->set($response['items'])
            ->filter('mapify', [ 'key' => 'id'])
            ->get();

        return $us->responsify($authors);
    }
}
