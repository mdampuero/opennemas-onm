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
{
    /**
     * Shows the getting started page.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     */
    public function gettingStartedAction(Request $request)
    {
        $session = $request->getSession();
        $session->set('login_callback', 'popup');

        $params = array();

        $user = $this->get('user_repository')->find($this->getUser()->id);

        if ($user->getMeta('facebook_id')) {
            $params['facebook'] = true;
        }

        if ($user->getMeta('twitter_id')) {
            $params['twitter'] = true;
        }

        return $this->render('gstarted/getting_started.tpl', $params);
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

    /**
     * Finish the wizard and deletes the user's token.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     */
    public function finishWizardAction(Request $request)
    {
        $user = $this->getUser();
        $user->updateUserToken($user->id, null);

        return $this->redirect($this->generateUrl('admin_welcome'));
    }
}
