<?php
/**
 * Playground where to test new functions
 *
 * @package Frontend_Controllers
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Frontend\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Playground where to test new functions
 *
 * @package Frontend_Controllers
 **/
class PlaygroundController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        $this->view = new \TemplateAdmin(TEMPLATE_ADMIN);
    }

    /**
     * Dispatches the actions through the rest of methods in this class
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
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
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function session(Request $request)
    {
        $this->get('session')->getFlashBag()->add(
            'notice',
            'Your changes were saved!'
        );

        foreach ($this->get('session')->getFlashBag()->get('notice', array()) as $message) {
            echo "<div class='flash-notice'>$message</div>";
        }
    }
}
