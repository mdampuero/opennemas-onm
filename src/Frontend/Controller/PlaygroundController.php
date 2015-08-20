<?php

namespace Frontend\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;

class PlaygroundController extends Controller
{
    /**
     * Dispatches the actions through the rest of methods in this class.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function defaultAction(Request $request)
    {
        $action = $request->query->get('action', null);

        if (!is_null($action)) {
            $response = $this->{$action}($request);
            return new Response($response, 200);
        } else {
            return new Response('not valid action', 400);
        }
    }

    /**
     * Tests for session in container
     *
     * @return Response The response object
     */
    public function session()
    {
        $session = $this->get('session');

        $session->getFlashBag()->add( 'notice', 'Your changes were saved!');

        foreach ($session->getFlashBag()->get('notice', array()) as $message) {
            echo "<div class='flash-notice'>$message</div>";
        }
    }

    /**
     * Displays the base/playground.tpl content.
     *
     * @return Response The response object.
     */
    public function template()
    {
        return $this->render('playground.tpl');
    }
}
