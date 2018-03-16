<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BackendWebService\Controller;

use Common\Core\Annotation\Security;
use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Intl\Intl;

class UserController extends Controller
{
    /**
     * Deletes a user.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasExtension('USER_MANAGER')
     *     and hasPermission('USER_DELETE')")
     */
    public function deleteAction($id)
    {
        $em  = $this->get('orm.manager');
        $msg = $this->get('core.messenger');

        $user = $em->getRepository('User', 'instance')->find($id);

        $em->remove($user);
        $msg->add(_('User deleted successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Deletes multiple users at once give them ids
     *
     * @param  Request      $request     The request object.
     * @return JsonResponse              The response object.
     *
     * @Security("hasExtension('USER_MANAGER')
     *     and hasPermission('USER_DELETE')")
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
        $oql = sprintf('id in [%s]', implode(',', $ids));

        $users = $em->getRepository('User', 'instance')->findBy($oql);

        $deleted = 0;
        foreach ($users as $user) {
            try {
                $em->remove($user);
                $deleted++;
            } catch (\Exception $e) {
                $msg->add($e->getMessage(), 'error');
            }
        }

        if ($deleted > 0) {
            $msg->add(
                sprintf(_('%s users deleted successfully'), $deleted),
                'success'
            );
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Updated the users activation status.
     *
     * @param  Request      $request The request object.
     * @return JsonResponse          The response object.
     *
     * @Security("hasExtension('USER_MANAGER')
     *     and hasPermission('USER_UPDATE')")
     */
    public function patchAction(Request $request, $id)
    {
        $em   = $this->get('orm.manager');
        $msg  = $this->get('core.messenger');
        $data = $em->getConverter('User')
            ->objectify($request->request->all());

        $user = $em->getRepository('User')->find($id);
        $user->merge($data);

        // TODO: Remove after check and update database schema
        $user->url = empty($user->url) ? ' ' : $user->url;
        $user->bio = empty($user->bio) ? ' ' : $user->bio;

        $em->persist($user);

        $msg->add(_('User saved successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Updated the users activation status.
     *
     * @param  Request      $request The request object.
     * @return JsonResponse          The response object.
     *
     * @Security("hasExtension('USER_MANAGER')
     *     and hasPermission('USER_UPDATE')")
     */
    public function patchSelectedAction(Request $request)
    {
        $params = $request->request->all();
        $em     = $this->get('orm.manager');
        $msg    = $this->get('core.messenger');
        $oql    = sprintf('id in [%s]', implode(',', $params[ 'ids' ]));

        unset($params['ids']);

        $data    = $em->getConverter('User')->objectify($params);
        $users   = $em->getRepository('User')->findBy($oql);
        $updated = 0;

        foreach ($users as $user) {
            try {
                $user->merge($data);

                // TODO: Remove after check and update database schema
                $user->url = empty($user->url) ? ' ' : $user->url;
                $user->bio = empty($user->bio) ? ' ' : $user->bio;

                $em->persist($user);
                $updated++;
            } catch (\Exception $e) {
                $msg->add($e->getMessage(), 'error', 409);
            }
        }

        if ($updated > 0) {
            $msg->add(
                sprintf(_('%s users saved successfully'), $updated),
                'success'
            );
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }
}
