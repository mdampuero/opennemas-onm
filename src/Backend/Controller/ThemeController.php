<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backend\Controller;

use Common\Core\Controller\Controller;
use Common\Core\Annotation\Security;

class ThemeController extends Controller
{
    /**
     * Displays the list of themes.
     *
     * @return Response The response object.
     *
     * @Security("hasPermission('ADMIN')")
     */
    public function listAction()
    {
        return $this->render('theme/list.tpl');
    }
}
