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

class UrlController extends ApiController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = null;

    /**
     * {@inheritdoc}
     */
    protected $getItemRoute = 'api_v1_backend_url_get_item';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'create' => 'MASTER',
        'delete' => 'MASTER',
        'list'   => 'MASTER',
        'patch'  => 'MASTER',
        'save'   => 'MASTER',
        'update' => 'MASTER'
    ];

    protected $module = 'url';

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.url';

    /**
     * {@inheritdoc}
     */
    protected function getExtraData($items = null)
    {
        return [
            'content_types' => array_merge(
                \ContentManager::getContentTypes(),
                [
                    ['name' => 'category', 'title' => _('Category')],
                    ['name' => 'tag', 'title' => _('Tag')],
                    ['name' => 'user', 'title' => _('User')],
                ]
            ),
                'formSettings'  => [
                    'name'             => $this->module,
                    'expansibleFields' => $this->getFormSettings($this->module)
                ]
        ];
    }
}
