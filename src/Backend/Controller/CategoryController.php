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

use Symfony\Component\HttpFoundation\Request;

class CategoryController extends BackendController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'CATEGORY_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'create'    => 'CATEGORY_CREATE',
        'configure' => 'CATEGORY_SETTINGS',
        'list'      => 'CATEGORY_ADMIN',
        'show'      => 'CATEGORY_UPDATE'
    ];

    /**
     * {@inheritdoc}
     */
    protected $resource = 'category';
}
