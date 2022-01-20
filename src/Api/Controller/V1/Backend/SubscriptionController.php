<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Api\Controller\V1\Backend;

use Api\Controller\V1\ApiController;

/**
 * Displays, saves, modifies and removes subscriptions.
 */
class SubscriptionController extends ApiController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'CONTENT_SUBSCRIPTIONS';

    /**
     * {@inheritdoc}
     */
    protected $getItemRoute = 'api_v1_backend_subscription_get_item';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'create' => 'SUBSCRIPTION_CREATE',
        'delete' => 'SUBSCRIPTION_DELETE',
        'list'   => 'SUBSCRIPTION_ADMIN',
        'patch'  => 'SUBSCRIPTION_UPDATE',
        'save'   => 'SUBSCRIPTION_CREATE',
        'show'   => 'SUBSCRIPTION_UPDATE',
        'update' => 'SUBSCRIPTION_UPDATE',
    ];

    protected $propertyName = 'article';

    protected $translations = [
        [
            'name' => 'visibility',
            'title' => 'Visibility'
        ],
        [
            'name' => 'request',
            'title' => 'Requests'
        ]
    ];

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.subscription';

    /**
     * {@inheritdoc}
     */
    protected function getExtraData($items = null)
    {
        return [
            'modules' => $this->get('core.helper.permission')->getByModule(),
            'formSettings'  => [
                'name'             => $this->propertyName,
                'expansibleFields' => $this->translateFields($this->translations)
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getItemId($item)
    {
        return $item->pk_user_group;
    }
}
