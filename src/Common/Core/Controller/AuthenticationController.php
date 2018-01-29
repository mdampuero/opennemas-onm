<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Controller;

use Common\Core\Controller\Controller;
use Common\Core\Annotation\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Handles the actions for the user authentication in frontend.
 */
class AuthenticationController extends Controller
{
    /**
     * Checks if the current user is authenticated.
     *
     * @return Response The response object.
     */
    public function authenticatedAction()
    {
        if (!empty($this->get('core.user'))) {
            return new Response('', 200);
        }

        return new Response('', 401);
    }

    /**
     * Displays a template to auto-close a pop-up.
     *
     * @return Response The response object.
     *
     * @Template(name="core.template.admin")
     */
    public function completeAction()
    {
        return $this->render('authentication/complete.tpl');
    }

    /**
     * Displays a button to connect with a social network account.
     *
     * @param Request $request  The request object.
     * @param string  $resource The social network resource (facebook or
     *                          twitter).
     *
     * @return Response The response object.
     *
     * @Template(name="core.template.admin")
     */
    public function loginSocialAction(Request $request, $resource)
    {
        return $this->render('authentication/social.tpl', [
            'target'   => $request->get('target'),
            'resource' => $resource,
        ]);
    }
}
