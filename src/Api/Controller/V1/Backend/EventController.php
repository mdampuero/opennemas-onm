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
    protected $permissions = [
        'create' => 'EVENT_CREATE',
        'delete' => 'EVENT_DELETE',
        'patch'  => 'EVENT_UPDATE',
        'update' => 'EVENT_UPDATE',
        'list'   => 'EVENT_ADMIN',
        'save'   => 'EVENT_CREATE',
        'show'   => 'EVENT_UPDATE',
    ];

    protected $module = 'event';

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.content';

    /**
     * {@inheritDoc}
     */
    protected function getExtraData($items = null)
    {
        $categories = $this->get('api.service.category')->responsify(
            $this->get('api.service.category')->getList()['items']
        );

        return array_merge(parent::getExtraData($items), [
            'categories' => $categories,
            'tags'       => $this->getTags($items),
            'formSettings'  => [
                'name'             => $this->module,
                'expansibleFields' => $this->getFormSettings($this->module)
            ]
        ]);
    }
}
