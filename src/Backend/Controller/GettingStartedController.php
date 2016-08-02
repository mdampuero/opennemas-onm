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

        $user = $this->get('core.user');

        if ($user->facebook_id) {
            $params['facebook'] = true;
        }

        if ($user->twitter_id) {
            $params['twitter'] = true;
        }

        $params['user'] = $this->getUser();

        return $this->render('gstarted/getting_started.tpl', $params);
    }
}
