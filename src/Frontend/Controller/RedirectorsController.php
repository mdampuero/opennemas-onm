<?php
/**
 * Handles the actions for the redirectors
 *
 * @package Frontend_Controllers
 */
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Frontend\Controller;

use Common\Core\Component\Exception\ContentNotMigratedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Common\Core\Controller\Controller;
use Onm\Settings as s;

/**
 * Handles the actions for the redirectors
 *
 * @package Frontend_Controllers
 */
class RedirectorsController extends Controller
{
    /**
     * Handles the redirections for all the contents.
     *
     * @param Request $request The request object
     *
     * @return Response The response object
     */
    public function contentAction(Request $request)
    {
        $id       = $request->query->filter('content_id', null, FILTER_SANITIZE_STRING);
        $slug     = $request->query->filter('slug', null, FILTER_SANITIZE_STRING);
        $type     = $request->query->filter('content_type', null, FILTER_SANITIZE_STRING);
        $fragment = '';
        $content  = null;

        list($type, $id) = $this->getTranslation($slug, $type, $id);

        if (!empty($type) && !empty($id)) {
            $content = $this->getContent($type, $id);
        }

        if (empty($content) || is_null($content->id)) {
            return $this->redirectNotMigratedContent($type);
        }

        if ($content->content_type_name === 'comment') {
            $fragment = '#comentarios';
        }

        $url = SITE_URL . $content->uri;

        // TODO: Remove when URI target="_blank"' not included for external
        $url = str_replace('" target="_blank', '', $url);

        return new RedirectResponse($url . $fragment, 301);
    }

    /**
     * Redirects the article given its external link url
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     */
    public function externalLinkAction(Request $request)
    {
        $url = $request->query->filter('to', '', FILTER_VALIDATE_URL);

        if (empty($url)) {
            throw new \Symfony\Component\Routing\Exception\ResourceNotFoundException();
        }

        return $this->redirect($url);
    }

    /**
     * Returns a content from a content type and content id.
     *
     * @param string $type The content type.
     * @param string $id   The content id.
     *
     * @return Content The content.
     */
    protected function getContent($type, $id)
    {
        $fixTypes = [ 'photo-inline' => 'photo' ];
        $type     = array_key_exists($type, $fixTypes) ? $fixTypes[$type] : $type;

        switch ($type) {
            case 'article':
                $content = $this->get('entity_repository')->find('Article', $id);

                if (!is_null($content)) {
                    $content->category_name = $content->catName;
                }

                return $content;

            case 'attachment':
                return new \Attachment($id);

            case 'category':
                $content = $this->get('category_repository')->find($id);
                $content->content_type_name = 'category';
                $content->id = $content->pk_content_category;

                $content->uri = mb_ereg_replace('^/', '', $this->generateUrl(
                    'category_frontpage',
                    [ 'category_name' => $content->name ]
                ));

                return $content;

            case 'comment':
                $comment = new \Comment($id);

                if (empty($comment->content_id)) {
                    return null;
                }

                return new \Content($comment->content_id);

            case 'opinion':
                return $this->get('opinion_repository')->find('Opinion', $id);

            case 'photo':
                $content = $this->get('entity_repository')->find('Photo', $id);

                $content->uri = '/media/'
                    . $this->get('core.instance')->internal_name
                    . '/images' . $content->path_img;

                return $content;

            default:
                return new \Content($id);
        }
    }

    /**
     * Returns the type and id for contents basing on redirection parameters.
     *
     * @param string $slug The content slug.
     * @param string $type The content type.
     * @param string $id   The old content id.
     *
     * @return array An array with the type and content id.
     */
    protected function getTranslation($slug = null, $type = null, $id = null)
    {
        if (!empty($slug)) {
            if (!empty($type)) {
                $id  = \ContentManager::getOriginalIdFromSlugAndType($slug, $type);

                return [ $type, $id ];
            }

            return \ContentManager::getOriginalIdAndContentTypeFromSlug($slug);
        }

        if (!empty($type) && !(empty($id))) {
            $id  = \ContentManager::getOriginalIDForContentTypeAndID($type, $id);

            return [ $type, $id ];
        } else {
            return \ContentManager::getOriginalIdAndContentTypeFromID($id);
        }
    }

    /**
     * Returns a response when a content was not found basing on a setting from
     * the instance.
     *
     * @param string $type The content type.
     *
     * @return RedirectReponse The redirection response object to frontpages
     *                         when the instance has redirection to frontpages
     *                         enabled.
     *
     * @throws ContentNotMigratedException When instance has redirection to
     *                                     frontpages disabled.
     */
    protected function redirectNotMigratedContent($type)
    {
        $ignored     = [ 'article', 'category' ];
        $redirection = $this->get('setting_repository')->get('redirection');

        if (empty($redirection)) {
            throw new ContentNotMigratedException();
        }

        $router = $this->get('router');
        $route  = preg_replace('/_+/', '_', 'frontend_' . $type . '_frontpage');
        $url    = $this->get('router')->generate('frontend_frontpage');

        if (!in_array($type, $ignored)
            && $router->getRouteCollection()->get($route)
        ) {
            $url = $router->generate($route);
        }

        return new RedirectResponse($url, 301);
    }

    /**
     * Redirects to a category frontpage
     *
     * @param Request $request the request object
     *
     * @return RedirectResponse the redirection to the proper url
     */
    public function categoryAction(Request $request)
    {
        $contentType = $request->query->filter('content_type', null, FILTER_SANITIZE_STRING);
        $slug        = $request->query->filter('slug', null, FILTER_SANITIZE_STRING);
        $contentId   = $request->query->filter('content_id', null, FILTER_SANITIZE_STRING);

        if (empty($slug)) {
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
