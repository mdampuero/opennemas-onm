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
    protected $getItemRoute = 'api_v1_backend_url_show';

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
            'content_types' => \ContentManager::getContentTypes()
        ];
    }
}
