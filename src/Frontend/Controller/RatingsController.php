<?php
/**
 * Handles the actions for ratings system
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
 * Handles the actions for ratings system
 *
 * @package Frontend_Controllers
 **/
class RatingsController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        $this->view = new \Template(TEMPLATE_USER);
    }

    /**
     * Registers a vote for a content given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function voteAction(Request $request)
    {
        // Retrieve data
        $ip        = getRealIp();
        $ipFrom    = $request->query->filter('i', null, FILTER_SANITIZE_STRING);
        $voteValue = $request->query->filter('v', null, FILTER_VALIDATE_INT);
        $page      = $request->query->filter('p', null, FILTER_SANITIZE_STRING);
        $contentId = $request->query->filter('a', null, FILTER_SANITIZE_STRING);

        // WEIRD SECUTIRY CHECK: Check if the remote address matches the passed in.
        if ($ip != $ipFrom) {
            return new Response(_('Problem with IP verification!'), 400);
        }

        // Check if the content to vote exists
        $content = new \Content($contentId);

        if (is_null($content->id)) {
            return new Response(_("Content not available"), 404);
        }

        // Register the vote
        $rating = new \Rating($content->id);
        if ($rating->update($voteValue, $ip)) {
            $output = $rating->render($page, 'result', 1);
        } else {
            $output = _("You have voted this new previously.");
        }

        // Return the response
        return new Response($output, 200);
    }
}
