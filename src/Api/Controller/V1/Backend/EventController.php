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

    protected $propertyName = 'event';

    protected $translations = [
        [
            'name' => 'author',
            'title' => 'Author'
        ],
        [
            'name' => 'category',
            'title' => 'Category'
        ],
        [
            'name' => 'tags',
            'title' => 'Tags'
        ],
        [
            'name' => 'slug',
            'title' => 'Slug'
        ],
        [
            'name' => 'schedule',
            'title' => 'Schedule'
        ],
        [
            'name' => 'when',
            'title' => 'Event date'
        ],
        [
            'name' => 'where',
            'title' => 'Event location'
        ],
        [
            'name' => 'external_website',
            'title' => 'External website'
        ],
        [
            'name' => 'featuredFrontpage',
            'title' => 'Featured in frontpage'
        ],
        [
            'name' => 'featuredInner',
            'title' => 'Featured in inner'
        ],
    ];

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
            'tags'       => $this->getTags($items),
            'formSettings'  => [
                'name'             => $this->propertyName,
                'expansibleFields' => $this->translateFields($this->translations)
            ]
        ]);
    }
}
