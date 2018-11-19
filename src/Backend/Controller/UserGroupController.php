<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backend\Controller;

class UserGroupController extends BackendController
{
    /**
     * The extension name required by this controller.
     *
     * @var string
     */
    protected $extension = 'USER_GROUP_MANAGER';

    /**
     * The list of permissions for every action.
     *
     * @var type
     */
    protected $permissions = [
        'create' => 'GROUP_CREATE',
        'list'   => 'GROUP_ADMIN',
        'show'   => 'GROUP_UPDATE'
    ];

    /**
     * The resource name.
     *
     * @var string
     */
    protected $resource = 'user-group';
}
