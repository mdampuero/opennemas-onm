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

class TagController extends Controller
{

    /**
     * Show a paginated list of backend tags.
     *
     * @return Response The response object.
     *
     * @Security("hasExtension('TAG_MANAGER')
     *     and hasPermission('TAG_ADMIN')")
     */
    public function listAction()
    {
        return $this->render('tag/list.tpl', ['locale' => $this->get('core.locale')->getLocale('frontend')]);
    }
}
