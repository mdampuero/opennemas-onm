<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Api\Controller\V1\Backend;

use Api\Controller\V1\ApiController;

/**
 * Displays, saves, modifies and removes menues.
 */
class MenuController extends ApiController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'USER_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected $getItemRoute = 'api_v1_backend_menu_get_item';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'create' => 'MENU_CREATE',
        'delete' => 'MENU_DELETE',
        'list'   => 'MENU_ADMIN',
        'patch'  => 'MENU_UPDATE',
        'save'   => 'MENU_CREATE',
        'show'   => 'MENU_UPDATE',
        'update' => 'MENU_UPDATE',
    ];

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.menu';
}
