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
     * The route name to generate URL from when creating a new item.
     *
     * @var string
     */
    protected $getItemRoute = 'api_v1_backend_url_show';

    /**
     * The API service name.
     *
     * @var string
     */
    protected $service = 'api.service.url';
}
