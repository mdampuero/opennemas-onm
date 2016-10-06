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
namespace Frontend\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;

/**
 * Handles the actions for the redirectors
 *
 * @package Frontend_Controllers
 **/
class RedirectorsController extends Controller
{
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
        $type       = $request->query->filter('content_type', null, FILTER_SANITIZE_STRING);

        if ($slug === 'none') {
            if (!empty($type)) {
                $newContentID  = \ContentManager::getOriginalIDForContentTypeAndID($type, $contentId);
            } else {
                list($type, $newContentID) = \ContentManager::getOriginalIdAndContentTypeFromID($contentId);
            }
        } else {
            if (!empty($type)) {
                $newContentID  = \ContentManager::getOriginalIdFromSlugAndType($slug, $type);
            } else {
                list($type, $newContentID) = \ContentManager::getOriginalIdAndContentTypeFromSlug($slug);
            }
        }

        if (($type == 'article') || ($type == 'TopSecret') || ($type == 'Fauna')) {
            $content = $this->get('entity_repository')->find('Article', $newContentID);

            if (!is_null($content)) {
                $content->category_name = $content->catName;
            }
        } elseif ($type == 'opinion') {
            $content = $this->get('opinion_repository')->find('Opinion', $newContentID);
        } elseif ($type === 'photo-inline' || $type === 'photo') {
            $content = $this->get('entity_repository')->find('Photo', $newContentID);
        } elseif ($type === 'category') {
            $content = $this->get('category_repository')->find($newContentID);
            $content->content_type_name = 'category';
            $content->uri = $this->generateUrl(
                'category_frontpage',
                [ 'category_name' => $content->name ],
                true
            );
        } elseif ($type === 'attachment') {
            $content = new \Attachment($newContentID);
        } else {
            $content = new \Content($newContentID);
        }

        if (!isset($content) || is_null($content->id)) {
            throw new \Symfony\Component\Routing\Exception\ResourceNotFoundException();
        }

        $url =  SITE_URL . $content->uri;
        if ($content->content_type_name === 'photo') {
            $url = SITE_URL . '/media/' . $this->get('core.instance')->internal_name
                . '/images' . $content->path_img;
        } elseif ($content->content_type_name == 'category') {
            $url = $content->uri;
        }

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
        $contentType = $request->query->filter('content_type', null, FILTER_SANITIZE_STRING);
        $slug        = $request->query->filter('slug', 'none', FILTER_SANITIZE_STRING);
        $contentId   = $request->query->filter('content_id', null, FILTER_SANITIZE_STRING);

        if ($slug == 'none') {
            $newContentID  = \ContentManager::getOriginalIDForContentTypeAndID($contentType, $contentId);
        } else {
            list($type, $newContentID) = \ContentManager::getOriginalIdAndContentTypeFromSlug($slug);
            // Unused var $type
            unset($type);
        }

        $category = new \ContentCategory($newContentID);

        if (!isset($category) || is_null($category->pk_content_category)) {
            throw new \Symfony\Component\Routing\Exception\ResourceNotFoundException();
        }

        $url = SITE_URL . \Uri::generate('section', array('id' => $category->name));

        return new RedirectResponse($url, 301);
    }
}
