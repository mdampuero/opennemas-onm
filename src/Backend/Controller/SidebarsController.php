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

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;

class SidebarsController extends Controller
{
    /**
     * Handles the default action.
     *
     * @param Request $request The request object.
     */
    public function listAction()
    {
        return $this->render('sidebar/list.tpl');
    }
}
