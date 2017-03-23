<?php

namespace Backend\Controller;

use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;

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
        $this->get('orm.manager')->persist($user, $user->getOrigin());

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
        $params  = [ 'user' => $this->get('core.user') ];

        if ($params['user']->facebook_id) {
            $params['facebook'] = true;
        }

        if ($params['user']->twitter_id) {
            $params['twitter'] = true;
        }

        return $this->render('gstarted/getting_started.tpl', $params);
    }
}
