<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Frontend\Controller;

use Common\Core\Component\Exception\ContentNotMigratedException;
use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * Redirects unofficial URLs to real contents.
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

        $translation = $this->get('core.redirector')
            ->getTranslation($slug, $type, $id);

        // Redirect content migrated to another domain
        if (!empty($translation)
            && array_key_exists('domain', $translation)
            && !empty($translation['domain'])
        ) {
            return $this->redirect($translation['domain'] . $this->generateUrl(
                'frontend_redirect_content',
                [ 'content_id' => $id ]
            ));
        }

        $content = $this->getContent($translation);

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
     * Redirects the article given an external link as query parameter or
     * request attribute.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function externalLinkAction(Request $request)
    {
        $url = $request->query->filter('to', '', FILTER_VALIDATE_URL);

        if ($request->attributes->has('to')) {
            $url = $request->attributes->filter('to', '', FILTER_VALIDATE_URL);
        }

        if (empty($url)) {
            throw new ResourceNotFoundException();
        }

        return $this->redirect($url);
    }

    /**
     * Returns an article by id.
     *
     * @param integer $id The article id.
     *
     * @return Article The article.
     */
    protected function getArticle($id)
    {
        $content = $this->get('entity_repository')->find('Article', $id);

        if (!empty($content)) {
            $content->category_name = $content->catName;
        }

        return $content;
    }

    /**
     * Returns an attachment by id.
     *
     * @param integer $id The attachment id.
     *
     * @return Attachment The attachment.
     */
    protected function getAttachment($id)
    {
        return $this->get('entity_repository')->find('Attachment', $id);
    }

    /**
     * Returns an category by id.
     *
     * @param integer $id The category id.
     *
     * @return Category The category.
     */
    protected function getCategory($id)
    {
        $content      = $this->get('category_repository')->find($id);
        $content->uri = mb_ereg_replace('^/', '', $this->generateUrl(
            'category_frontpage',
            [ 'category_name' => $content->name ]
        ));

        return $content;
    }

    /**
     * Returns an comment by id.
     *
     * @param integer $id The comment id.
     *
     * @return Comment The comment.
     */
    protected function getComment($id)
    {
        $comment = new \Comment($id);

        if (empty($comment->content_id)) {
            return null;
        }

        return new \Content($comment->content_id);
    }

    /**
     * Returns a content from a translation value.
     *
     * @param array $translation The translation value.
     *
     * @return Content The content.
     */
    protected function getContent($translation)
    {
        if (empty($translation)) {
            return null;
        }

        $fixTypes = [ 'photo-inline' => 'photo' ];

        if (array_key_exists($translation['type'], $fixTypes)) {
            $translation['type'] = $fixTypes[$translation['type']];
        }

        $method = 'get' . \classify($translation['type']);

        if (method_exists($this, $method)) {
            return $this->{$method}($translation['pk_content']);
        }

        return $this->get('entity_repository')
            ->find($translation['type'], $translation['pk_content']);
    }

    /**
     * Returns an opinion by id.
     *
     * @param integer $id The opinion id.
     *
     * @return Opinion The opinion.
     */
    protected function getOpinion($id)
    {
        return $this->get('opinion_repository')->find('Opinion', $id);
    }

    /**
     * Returns an photo by id.
     *
     * @param integer $id The photo id.
     *
     * @return Photo The photo.
     */
    protected function getPhoto($id)
    {
        $content = $this->get('entity_repository')->find('Photo', $id);

        $content->uri = '/media/'
            . $this->get('core.instance')->internal_name
            . '/images' . $content->path_img;

        return $content;
    }

    /**
     * Returns a response when a content was not found basing on a setting from
     * the instance.
     *
     * @param string $type The content type.
     *
     * @return RedirectResponse The redirection response object to frontpages
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
}
