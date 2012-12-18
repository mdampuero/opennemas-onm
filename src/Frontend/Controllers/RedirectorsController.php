<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Frontend\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for the redirectors
 *
 * @package Backend_Controllers
 **/
class RedirectorsController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
    }

    /**
     * Handles the redirections for all the contents.
     *
     * @param int content_id the content to detect
     *
     * @return Response the response object
     **/
    public function contentAction(Request $request)
    {
        $contentId   = $request->query->filter('content_id', null, FILTER_SANITIZE_STRING);

        list($type, $newContentID) = getOriginalIdAndContentTypeFromID($contentId);

        if ($type == 'article') {
            $content = new \Article($newContentID);
            $content->category_name = $content->catName;

            $url .=  $content->uri;
        } elseif ($type == 'opinion') {
            $content = new \Opinion($newContentID);
        }
        $url =  SITE_URL . $content->uri;

        return new RedirectResponse($url);
    }

    /**
     * Redirects to a category frontpage
     *
     * @return RedirectResponse the redirection to the proper url
     **/
    public function categoryAction(Request $request)
    {
        $contentType   = $request->query->filter('content_type', null, FILTER_SANITIZE_STRING);
        $contentId     = $request->query->filter('content_id', null, FILTER_SANITIZE_STRING);

        $newContentID  = getOriginalIDForContentTypeAndID($contentType, $contentId);

        $cc = new \ContentCategory($newContentID);

        $url = SITE_URL . \Uri::generate('section', array('id' => $cc->name));

        return new RedirectResponse($url);
    }
}
