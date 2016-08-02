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
        $user = $this->get('core.user');

        $user->token = null;
        $this->get('orm.manager')->persist($user);

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

        $instance = $this->get('core.instance');
        $database  = $instance->getDatabaseName();
        $namespace = $this->get('cache')->getNamespace();

        try {
            $user = $this->get('orm.manager')->getRepository('User', 'instance')
                ->find($this->getUser()->id);
        } catch (\Exception $e) {
            if (empty($user)) {
                $user = $this->get('orm.manager')->getRepository('User', 'manager')
                    ->find($this->getUser()->id);
            }
        }

        $this->get('orm.manager')->getConnection('instance')->selectDatabase($database);
        $this->get('cache')->setNamespace($namespace);

        if ($user->facebook_id) {
            $params['facebook'] = true;
        }

        if ($user->twitter_id) {
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
