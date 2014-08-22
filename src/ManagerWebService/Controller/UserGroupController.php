<?php

/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ManagerWebService\Controller;

use Onm\Framework\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UserGroupController extends Controller
{
    /**
     * Returns the list of users as JSON.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function listAction(Request $request)
    {
        $epp      = $request->request->getDigits('epp', 10);
        $page     = $request->request->getDigits('page', 1);
        $criteria = $request->request->filter('criteria') ? : array();
        $orderBy  = $request->request->filter('sort_by') ? : array();

        $um     = $this->get('usergroup_repository');
        $groups = $um->findBy($criteria, $orderBy, $epp, $page);
        $total  = $um->countBy($criteria);

        return new JsonResponse(
            array(
                'epp'     => $epp,
                'page'    => $page,
                'results' => $groups,
                'total'   => $total,
            )
        );
    }

    /**
     * Returns the data to create a new group.
     *
     * @return JsonResponse The response object.
     */
    public function newAction()
    {
        $privilege = new \Privilege();

        return new JsonResponse(
            array(
                'group'     => null,
                'template' => $this->templateParams()
            )
        );
    }

    /**
     * Returns a group as JSON.
     *
     * @param integer $id The group id.
     *
     * @return JsonResponse The response object.
     */
    public function showAction($id)
    {
        $group = $this->get('usergroup_repository')->find($id);

        return new JsonResponse(
            array(
                'group'    => $group,
                'template' => $this->templateParams()
            )
        );
    }

    /**
     * Returns a list of parameters for the template.
     *
     * @return array Array of template parameters.
     */
    private function templateParams()
    {
        $privilege = new \Privilege();

        return array('modules' => $privilege->getPrivilegesByModules());
    }
}
