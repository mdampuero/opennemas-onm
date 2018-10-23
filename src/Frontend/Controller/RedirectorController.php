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
class RedirectorController extends Controller
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
        $format   = $request->query->filter('format', null, FILTER_SANITIZE_STRING);
        $fragment = '';
        $content  = null;

        if (empty($id) && empty($slug)) {
            throw new ResourceNotFoundException();
        }

        $translation = $this->get('core.redirector')
            ->getUrl($slug, $type, $id);

        // Redirect content migrated to another domain
        if (!empty($translation) && $translation->type === 2) {
            return $this->redirect(
                (preg_match('/http(s)?:\/\//', $translation->target) ? '' : '/')
                . $translation->target
            );
        }

        $content = $this->get('core.redirector')->getContent($translation);

        if (empty($content) || is_null($content->id)) {
            return $this->redirectNotMigratedContent($type);
        }

        if ($type === 'comment') {
            $fragment = '#comentarios';
        }

        $url = $this->get('core.helper.url_generator')->generate($content);

        if ($format === 'amp' && $content->content_type_name === 'article') {
            $url = str_replace('.html', '.amp.html', $url);
        }

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
        $redirection = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('redirection');

        if (empty($redirection)) {
            throw new ContentNotMigratedException();
        }

        $router = $this->get('router');
        $route  = preg_replace('/_+/', '_', 'frontend_' . $type . '_frontpage');
        $url    = $router->generate('frontend_frontpage');

        if (!in_array($type, $ignored)
            && $router->getRouteCollection()->get($route)
        ) {
            $url = $router->generate($route);
        }

        return new RedirectResponse($url, 301);
    }
}
