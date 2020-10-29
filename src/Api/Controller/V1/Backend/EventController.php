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

use Api\Exception\GetItemException;
use Api\Controller\V1\ApiController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class EventController extends ContentController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'es.openhost.module.events';

    /**
     * {@inheritdoc}
     */
    protected $getItemRoute = 'api_v1_backend_event_get_item';

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.content';

    /**
     * {@inheritDoc}
     */
    protected function getExtraData($items = null)
    {
        return array_merge(parent::getExtraData($items), [
            'categories' => $this->getCategories($items),
            'tags'       => $this->getTags($items)
        ]);
    }

    /**
     * {@inheritdoc}
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

        foreach ($content as $element) {
            if (!is_array($element->related_contents)) {
                continue;
            }

            foreach ($element->related_contents as $relation) {
                if (!preg_match('/featured_.*/', $relation['type'])) {
                    continue;
                }
                try {
                    $photo   = $service->getItem($relation['target_id']);
                    $extra[$relation['target_id']] = $service->responsify($photo);
                } catch (GetItemException $e) {
                }
            }
        }

        return $extra;
    }
}
