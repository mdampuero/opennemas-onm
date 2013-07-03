<?php
/**
 * Handles the actions for the redirectors
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
namespace Frontend\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for the redirectors
 *
 * @package Frontend_Controllers
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
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function contentAction(Request $request)
    {
        $contentId  = $request->query->filter('content_id', null, FILTER_SANITIZE_STRING);
        $slug       = $request->query->filter('slug', 'none', FILTER_SANITIZE_STRING);
        $oldVersion = $request->query->filter('version', null, FILTER_SANITIZE_STRING);


        if ($slug === 'none') {
            list($type, $newContentID) = getOriginalIdAndContentTypeFromID($contentId);
        } else {

            list($type, $newContentID) = getOriginalIdAndContentTypeFromSlug($slug);
        }

        if ($oldVersion == 'editmaker') {
            $newContentID = \Content::resolveID($newContentID);
        }

        $er = $this->get('entity_repository');

        if (($type == 'article') || ($type == 'TopSecret') || ($type == 'Fauna')) {
            $content = new \Article($newContentID);
            $content->category_name = $content->catName;
        } elseif ($type == 'opinion') {
            $content = new \Opinion($newContentID);
        }
        if (!isset($content) || is_null($content->id)) {
            throw new \Symfony\Component\Routing\Exception\ResourceNotFoundException();
        }
        $url =  SITE_URL . $content->uri;

        return new RedirectResponse($url, 301);
    }

    /**
     * Redirects to a category frontpage
     *
     * @param Request $request the request object
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

        return new RedirectResponse($url, 301);
    }
}
