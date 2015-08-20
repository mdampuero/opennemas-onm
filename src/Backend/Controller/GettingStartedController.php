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
use Symfony\Component\HttpFoundation\JsonResponse;
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
        $session->set(
            '_security.backend.target_path',
            $this->generateUrl('admin_login_callback')
        );

        $params = array();

        $database = $this->get('instance_manager')->current_instance->getDatabaseName();
        $namespace = $this->get('cache')->getNamespace();

        $user = $this->get('onm_user_provider')->loadUserByUsername(
            $this->getUser()->getUsername()
        );

        $this->get('dbal_connection')->selectDatabase($database);
        $this->get('cache')->setNamespace($namespace);

        if ($user->getMeta('facebook_id')) {
            $params['facebook'] = true;
        }

        if ($user->getMeta('twitter_id')) {
            $params['twitter'] = true;
        }

        $params['user'] = $this->getUser();
        $params['master'] = $this->getUser()->isMaster();

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
        $user = $this->getUser();

        if ($user->isMaster()) {
            $GLOBALS['application']->conn->selectDatabase('onm-instances');
        }

        if ($request->get('accept') && $request->get('accept') === 'true') {
            $date = new \DateTime(null, new \DateTimeZone('UTC'));

            $newMeta = array('terms_accepted' => $date->format('Y-m-d H:i:s'));
            $user->setMeta($newMeta);

            $user->meta = array_merge($user->meta, $newMeta);
        } else {
            $user->deleteMetaKey($user->id, 'terms_accepted');
        }

        return new JsonResponse();
    }

    /**
     * Finish the wizard and deletes the user's token.
     *
     * @return Response          The response object.
     */
    public function finishWizardAction()
    {
        $user = $this->getUser();
        $user->updateUserToken($user->id, null);

        return $this->redirect($this->generateUrl('admin_welcome'));
    }
}
