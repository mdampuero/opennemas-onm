<?php

/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Manager\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;

/**
 * Playground where to test new functions
 */
class PlaygroundController extends Controller
{
    /**
     * Dispatches the actions through the rest of methods in this class
     *
     * @param Request $request the request object
     *
     * @return Response the response object
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
     * Dummy action to edit on-line and fast-test features, find errors, etc.
     *
     * @param Request $request The request object.
     */
    public function play(Request $request)
    {
        // Your code here...
    }
}
