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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class EventController extends ContentApiController
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

        if (empty($content)) {
            return $extra;
        }

        if (is_object($content)) {
            $content = [ $content ];
        }

        foreach ($content as $element) {
            if (!is_array($element->related_contents)) {
                continue;
            }

            foreach ($element->related_contents as $relation) {
                if ($relation['relationship'] !== 'cover') {
                    continue;
                }

                $photo = $em->find('Photo', $relation['pk_content2']);

                $extra[$relation['pk_content2']] = \Onm\StringUtils::convertToUtf8($photo);
            }
        }

        return $extra;
    }
}
