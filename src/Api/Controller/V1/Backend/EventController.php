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

use Api\Controller\V1\ApiController;

class EventController extends ApiController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'es.openhost.module.events';

    /**
     * The route name to generate URL from when creating a new item.
     *
     * @var string
     */
    protected $getItemRoute = 'api_v1_backend_event_show';

    /**
     * The API service name.
     *
     * @var string
     */
    protected $service = 'api.service.content';

    /**
     * Returns the content id
     *
     * @param Content $item the item
     *
     * @return integer
     **/
    public function getItemId($item)
    {
        return $item->pk_content;
    }

    /**
     * Returns a list of extra data
     *
     * @return array
     **/
    protected function getExtraData($items = null)
    {
        $security   = $this->get('core.security');
        $converter  = $this->get('orm.manager')->getConverter('Category');
        $categories = $this->get('orm.manager')
            ->getRepository('Category')
            ->findBy('internal_category = 1 and fk_content_category=0');

        $categories = array_filter($categories, function ($category) use ($security) {
            return $security->hasCategory($category->pk_content_category);
        });

        $extra = [
            'categories'       => $converter->responsify($categories),
            'related_contents' => $this->getRelatedContents($items),
            'tags'             => $this->getTagIds($items),
        ];

        return array_merge($extra, $this->getLocaleData('frontend'));
    }

    /**
     * Returns the list of photos linked to the article.
     *
     * @param Content $content The content.
     *
     * @return array The list of photos linked to the content.
     */
    protected function getRelatedContents($content)
    {
        $em    = $this->get('entity_repository');
        $extra = [];

        if (is_object($content)) {
            $content = [ $content ];
        }

        foreach ($content as $element) {
            foreach ($element->relations as $relation) {
                $photo = $em->find('Photo', $relation['pk_content2']);

                if (!empty($photo)) {
                    $extra[$relation['pk_content2']] = \Onm\StringUtils::convertToUtf8($photo);
                }
            }
        }

        return $extra;
    }

    /**
     * Returns the list of tag ids for a list of items or a individual item
     *
     * @param array|Content $items One Content object or a list of Content objects
     *
     * @return array
     **/
    public function getTagIds($items = null)
    {
        if (empty($items)) {
            return [];
        }

        $tagIds = [];
        if (is_array($items)) {
            foreach ($items as $item) {
                $tagIds = array_merge($tagIds, $item->tag_ids);
            }
        } else {
            $tagIds = $items->tag_ids;
        }

        return $this->get('api.service.tag')
            ->getListByIdsKeyMapped($tagIds)['items'];
    }
}
