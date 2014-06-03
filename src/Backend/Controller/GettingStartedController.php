<?php
/**
 * Handles all the request for Welcome actions
 *
 * @package Backend_Controllers
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Backend\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;

/**
 * Handles all the request for Welcome actions
 *
 * @package Backend_Controllers
 **/
class GettingStartedController extends Controller
{r
    /**
     * Shows the getting started page.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     */
    public function gettingStartedAction(Request $request)
    {
        $token = $request->get('token');

        if (!$token && !$this->getUser()) {
            $request->getSession()->getFlashBag()->add('error', _('Invalid token'));
            return $this->redirect($this->generateUrl('admin_login_form'));
        }

        if (!$this->getUser()) {
            $user = $this->get('user_repository')->findBy(
                array(
                    'token' => array(array('value' => $token))
                ),
                array('token' => 'asc'),
                1,
                1
            );

            if (!$user) {
                $request->getSession()->getFlashBag()->add('error', _('Invalid token'));
                return $this->redirect($this->generateUrl('admin_login_form'));
            }

            $user = array_pop($user);
            $token = new UsernamePasswordToken($user, null, 'backend', $user->getRoles());

            $securityContext = $this->get('security.context');
            $securityContext->setToken($token);

            $request = $this->getRequest();
            $session = $request->getSession();
            $session->set('_security_backend', serialize($token));
        }

        return $this->render(
            'gstarted/getting_started.tpl',
            array(
            )
        );
    }

    /**
     * Accept the terms of use for the current user.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     */
    public function acceptTermsAction(Request $request)
    {
        if ($request->get('accept')) {
            $date = new \DateTime(null, new \DateTimeZone('UTC'));
            $user = $this->getUser();
            $newMeta = array('terms_accepted' => $date->format('Y-m-d H:i:s'));
            $user->setMeta($newMeta);

            $user->meta = array_merge($user->meta, $newMeta);

            return new Response('OK');
        }

        return new Response('Not accepted', 412);
    }

    public function finishWizardAction(Request $request)
    {
        $user = $this->getUser();
        $user->updateUserToken($user->id, null);

        return $this->redirect($this->generateUrl('admin_welcome'));
    }
}
