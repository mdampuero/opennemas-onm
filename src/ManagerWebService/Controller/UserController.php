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

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Onm\Framework\Controller\Controller;

class UserController extends Controller
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

        $um    = $this->get('user_repository');
        $users = $um->findBy($criteria, $orderBy, $epp, $page);
        $total = $um->countBy($criteria);

        $userGroups = $this->get('usergroup_repository')->findBy();

        $groups = array();
        foreach ($userGroups as $group) {
            $groups[$group->id] = $group;
        }

        return new JsonResponse(
            array(
                'epp'      => $epp,
                'template' => array(
                    'groups' => $groups
                ),
                'page'     => $page,
                'results'  => $users,
                'total'    => $total,
            )
        );
    }

    /**
     * Returns an user as JSON.
     *
     * @param integer $id The user id.
     *
     * @return JsonResponse The response object.
     */
    public function showAction($id)
    {
        $user = $this->get('user_repository')->find($id);

        return new JsonResponse(
            array('data' => $user)
        );
    }

    /**
     * Returns the current user as JSON.
     *
     * @return JsonResponse The response object.
     */
    public function showMeAction()
    {
        $id = $this->getUser()->id;

        $user = $this->get('user_repository')->find($id);

        return new JsonResponse(
            array('data' => $user)
        );
    }
}
