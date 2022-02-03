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

class StaticPageController extends ContentController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'STATIC_PAGES_MANAGER';

    /**
     * The route name to generate URL from when creating a new item.
     *
     * @var string
     */
    protected $getItemRoute = 'api_v1_backend_static_page_get_item';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'create' => 'STATIC_PAGE_CREATE',
        'delete' => 'STATIC_PAGE_DELETE',
        'patch'  => 'STATIC_PAGE_UPDATE',
        'update' => 'STATIC_PAGE_UPDATE',
        'list'   => 'STATIC_PAGE_ADMIN',
        'save'   => 'STATIC_PAGE_CREATE',
        'show'   => 'STATIC_PAGE_UPDATE',
    ];

    protected $module = 'staticPage';

    /**
     * The API service name.
     *
     * @var string
     */
    protected $service = 'api.service.content';

    /**
     * Loads extra data related to the given contents.
     *
     * @param array $items The items array
     *
     * @return array Array of extra data.
     */
    protected function getExtraData($items = null)
    {
        $extra = parent::getExtraData($items);
        return array_merge([
            'formSettings'  => [
                'name'             => $this->module,
                'expansibleFields' => $this->getFormSettings($this->module)
            ]
        ], $extra);
    }
}
