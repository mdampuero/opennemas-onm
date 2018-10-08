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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * Shows the form to create a new user.
     */
    public function createAction()
    {
        return $this->render('user/item.tpl');
    }

    /**
     * Disconnects from social account accounts.
     *
     * @param Request $request The request object.
     * @param integer $id      The user's id.
     *
     * @return Response The response object.
     */
    public function disconnectAction(Request $request, $id, $resource)
    {
        $user = $this->get('core.user');

        if (empty($user)) {
            return new Response();
        }

        unset($user->{$resource . '_id'});
        unset($user->{$resource . '_id'});
        unset($user->{$resource . '_email'});
        unset($user->{$resource . '_token'});
        unset($user->{$resource . '_realname'});

        $this->get('orm.manager')->persist($user);

        $this->get('core.dispatcher')->dispatch('social.disconnect', ['user' => $user]);

        return $this->redirect($this->generateUrl('backend_user_social', [
            'id'       => $id,
            'resource' => $resource,
            'style'    => $request->get('style')
        ]));
    }

    /**
     * Show a paginated list of backend users.
     *
     * @return Response The response object.
     *
     * @Security("hasExtension('USER_MANAGER')
     *     and hasPermission('USER_ADMIN')")
     */
    public function listAction()
    {
        return $this->render('user/list.tpl');
    }

    /**
     * Shows the user information given its id
     *
     * This action is not mapped with Security annotation because it's
     * used in edit profile action that should be available to all users with
     * or without having users module activated.
     *
     * @param integer $id The user id.
     *
     * @return Response The response object.
     */
    public function showAction($id)
    {
        return $this->render('user/item.tpl', [ 'id' => $id ]);
    }

    /**
     * Displays the facebook iframe to connect accounts.
     *
     * @param Request $request The request object.
     * @param integer $id      The user's id.
     *
     * @return Response The response object.
     */
    public function socialAction(Request $request, $id, $resource)
    {
        $template = 'user/social.tpl';

        try {
            $user = $this->get('orm.manager')->getRepository('User')->find($id);
        } catch (\Exception $e) {
            $user = $this->get('orm.manager')->getRepository('User', 'manager')
                ->find($id);
        }

        $session = $request->getSession();

        $session->set(
            '_security.backend.target_path',
            $this->generateUrl('core_authentication_complete')
        );

        if (!$user) {
            return new Response();
        }

        $resourceId = $user->{$resource . '_id'};

        $connected = false;
        if ($resourceId) {
            $connected = true;
        }

        $resourceName = 'Twitter';

        if ($resource == 'facebook') {
            $resourceName = 'Facebook';
        }

        if ($request->get('style') && $request->get('style') == 'orb') {
            $template = 'user/social_alt.tpl';
        }

        $this->get('core.dispatcher')->dispatch('social.connect', ['user' => $user]);

        return $this->render($template, [
            'current_user_id' => $user->id,
            'connected'       => $connected,
            'resource_id'     => $resourceId,
            'resource'        => $resource,
            'resource_name'   => $resourceName,
            'user'            => $user,
        ]);
    }
}
