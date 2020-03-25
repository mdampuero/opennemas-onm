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

use Common\Core\Annotation\Security;
use Common\Core\Controller\Controller;
use Common\Model\Entity\Content;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Handles the actions for static pages.
 */
class StaticPageController extends BackendController
{
    /**
     * The extension name required by this controller.
     *
     * @var string
     */
    protected $extension = 'STATIC_PAGES_MANAGER';

    /**
     * The list of permissions for every action.
     *
     * @var type
     */
    protected $permissions = [
        'create' => 'STATIC_PAGE_CREATE',
        'update' => 'STATIC_PAGE_UPDATE',
        'list'   => 'STATIC_PAGE_ADMIN',
        'show'   => 'STATIC_PAGE_UPDATE',
    ];

    /**
     * The resource name.
     *
     * @var string
     */
    protected $resource = 'static_pages';
}
