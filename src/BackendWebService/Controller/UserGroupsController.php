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
        $em  = $this->get('orm.manager');
        $msg = $this->get('core.messenger');

        $userGroup = $em->getRepository('UserGroup')->find($id);

        $em->remove($userGroup);
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
        $ids = $request->request->get('ids', []);
        $msg = $this->get('core.messenger');

        if (!is_array($ids) || empty($ids)) {
            $msg->add(_('Bad request'), 'error', 400);
            return new JsonResponse($msg->getMessages(), $msg->getCode());
        }

        $em  = $this->get('orm.manager');
        $oql = sprintf('pk_user_group in [%s]', implode(',', $ids));

        $userGroups = $em->getRepository('UserGroup')->findBy($oql);

        $deleted = 0;
        foreach ($userGroups as $userGroup) {
            try {
                $em->remove($userGroup);
                $deleted++;
            } catch (\Exception $e) {
                $msg->add($e->getMessage(), 'error');
            }
        }

        if ($deleted > 0) {
            $msg->add(
                sprintf(_('%s user groups deleted successfully'), $deleted),
                'success'
            );
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
        $oql = $request->query->get('oql', '');

        // TODO: Remove the pk_user_group condition when implementing ticket ONM-1660
        if (!$this->get('core.security')->hasRole('ROLE_MASTER')) {
            $oql = $this->get('orm.oql.fixer')->fix($oql)->addCondition('pk_user_group != 4')->getOql();
        }

        $repository = $this->get('orm.manager')->getRepository('UserGroup');
        $converter  = $this->get('orm.manager')->getConverter('UserGroup');

        $total      = $repository->countBy($oql);
        $userGroups = $repository->findBy($oql);

        $userGroups = array_map(function ($a) use ($converter) {
            return $converter->responsify($a->getData());
        }, $userGroups);

        return new JsonResponse([
            'results' => $userGroups,
            'total'   => $total,
        ]);
    }
}
