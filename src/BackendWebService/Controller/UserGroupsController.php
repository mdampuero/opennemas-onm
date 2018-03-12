<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace BackendWebService\Controller;

use Common\Core\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UserGroupsController extends Controller
{
    /**
     * Deletes an user group.
     *
     * @param integer $id The user group id.
     *
     * @return JsonResponse The response object.
     */
    public function deleteAction($id)
    {
        $msg = $this->get('core.messenger');

        $this->get('api.service.user_group')->deleteItem($id);
        $msg->add(_('User group deleted successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Deletes the selected user groups.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function deleteSelectedAction(Request $request)
    {
        $ids     = $request->request->get('ids', []);
        $msg     = $this->get('core.messenger');
        $deleted = $this->get('api.service.user_group')->deleteList($ids);

        if ($deleted > 0) {
            $msg->add(
                sprintf(_('%s user groups deleted successfully'), $deleted),
                'success'
            );
        }

        if ($deleted !== count($ids)) {
            $msg->add(sprintf(
                _('%s user groups could not be deleted successfully'),
                count($ids) - $deleted
            ), 'error');
        }


        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Returns the list of user groups.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function listAction(Request $request)
    {
        $ugs = $this->get('api.service.user_group');
        $oql = $request->query->get('oql', '');

        // TODO: Remove the pk_user_group condition when implementing ticket ONM-1660
        if (!$this->get('core.security')->hasRole('ROLE_MASTER')) {
            $oql = $this->get('orm.oql.fixer')->fix($oql)
                ->addCondition('pk_user_group != 4')->getOql();
        }

        $response = $ugs->getList($oql);

        $response['results'] = $ugs->responsify($response['results']);

        return new JsonResponse($response);
    }
}
