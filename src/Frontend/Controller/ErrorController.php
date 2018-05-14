<?php
/**
 * Defines the frontend error handler
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

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;
use Onm\Settings as s;

/**
 * Handlers errors in frontend
 *
 * @package Frontend_Controllers
 */
class ErrorController extends Controller
{
    /**
     * Shows the error page
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     */
    public function defaultAction(Request $request)
    {
        $error = $request->attributes->get('exception');
        $name  = basename(str_replace('\\', '/', $error->getClass()));

        switch ($name) {
            case 'AccessDeniedException':
                $this->get('application.log')
                    ->info('security.authorization.failure');

                return $this->getAccessDeniedResponse($request);

            case 'ContentNotMigratedException':
            case 'ResourceNotFoundException':
                $this->get('application.log')->info($name);

                return $this->getNotFoundResponse();

            case 'NotFoundHttpException':
                $this->get('application.log')->info($name);

                // Redirect to redirectors URLs without /
                $url = $this->generateUrl('frontend_redirect_content', [
                    'slug'  => mb_ereg_replace('^\/', '', $request->getRequestUri())
                ]);

                return new RedirectResponse($url, 301);

            default:
                $this->get('error.log')->error($error->getMessage(), $error->getTrace());
                return $this->getErrorResponse();
        }
    }

    /**
     * Generates a response when the error is caused by an unauthorized access
     * to a protected resolurce.
     *
     * @return Response The response object.
     */
    protected function getAccessDeniedResponse()
    {
        list($positions, $advertisements) =
            \Frontend\Controller\ArticlesController::getAds();

        return new Response($this->renderView('static_pages/403.tpl', [
            'ads_positions'  => $positions,
            'advertisements' => $advertisements,
        ]), 403);
    }

    /**
     * Generates a response when an unknown error happens.
     *
     * @return Response The response object.
     */
    protected function getErrorResponse()
    {
        $content = $this->renderView('static_pages/statics.tpl');

        return new Response($content, 500);
    }

    /**
     * Generates a response when the error is caused by a resource that could
     * not be found.
     *
     * @return Response The response object.
     */
    protected function getNotFoundResponse()
    {
        list($positions, $advertisements) =
            \Frontend\Controller\ArticlesController::getAds();

        // Setup templating cache layer
        $this->view->setConfig('articles');
        $cacheID = $this->view->getCacheId('error', 404);

        $content = $this->renderView('static_pages/404.tpl', [
            'ads_positions'  => $positions,
            'advertisements' => $advertisements,
            'cache_id'       => $cacheID,
        ]);

        return new Response($content, 404, [
            'x-cache-for' => '1d',
            'x-cacheable' => true,
            'x-instance'  => $this->get('core.instance')->internal_name,
            'x-tags'      => sprintf(
                'instance-%s,not-found',
                $this->get('core.instance')->internal_name
            )
        ]);
    }
}
