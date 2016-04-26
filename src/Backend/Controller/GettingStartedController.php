<?php

namespace Backend\Controller;

use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;

/**
 * Handles requests for Getting Started.
 */
class GettingStartedController extends Controller
{
    /**
     * Finish the wizard and deletes the user's token.
     *
     * @return Response The response object.
     */
    public function finishWizardAction()
    {
        $user = $this->getUser();
        $user->updateUserToken($user->id, null);

        return $this->redirect($this->generateUrl('admin_welcome'));
    }

    /**
     * Shows the getting started page.
     *
     * @param  Request  $request The request object.
     *
     * @return Response The response object.
     */
    public function gettingStartedAction(Request $request)
    {
        $session = $request->getSession();
        $session->set(
            '_security.backend.target_path',
            $this->generateUrl('admin_login_callback')
        );

        $params = array();

        $instance = $this->get('instance');
        $database  = $instance->getDatabaseName();
        $namespace = $this->get('cache')->getNamespace();

        $user = $this->get('onm_user_provider')->loadUserByUsername(
            $this->getUser()->getUsername()
        );

        $this->get('orm.manager')->getConnection('instance')->selectDatabase($database);
        $this->get('cache')->setNamespace($namespace);

        if ($user->getMeta('facebook_id')) {
            $params['facebook'] = true;
        }

        if ($user->getMeta('twitter_id')) {
            $params['twitter'] = true;
        }

        $params['user'] = $this->getUser();
        $params['master'] = $this->getUser()->isMaster();

        $params['billing'] = [];

        if (!empty($instance->metas)
            && array_key_exists('billing', $instance->metas)
        ) {
            $params['billing'] = $instance->metas['billing'];
        }

        return $this->render('gstarted/getting_started.tpl', $params);
    }
}
