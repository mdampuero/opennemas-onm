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

use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ErrorController extends Controller
{
    /**
     * Displays an error page basing on the current error.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function defaultAction(Request $request)
    {
        $error = $request->attributes->get('exception');
        $class = new \ReflectionClass($error->getClass());

        switch ($class->getShortName()) {
            case 'AccessDeniedException':
                $this->get('application.log')
                    ->info('security.authorization.failure');

                return $this->getAccessDeniedResponse();

            case 'ConnectionException':
                $this->get('error.log')->error(
                    'database.connection.failure: ' . $error->getMessage()
                );

                return $this->getConnectionExceptionResponse();

            case 'ContentNotMigratedException':
            case 'ResourceNotFoundException':
            case 'NotFoundHttpException':
                $this->get('application.log')->info($class->getShortName());

                return $this->getNotFoundResponse();

            default:
                $this->get('error.log')->error($error->getMessage());
                return $this->getErrorResponse();
        }
    }

    /**
     * Generates a response when the error is caused by an unauthorized access
     * to a protected resource.
     *
     * @return Response The response object.
     */
    protected function getAccessDeniedResponse()
    {
        list($positions, $advertisements) = ArticlesController::getAds();

        return new Response($this->renderView('static_pages/403.tpl', [
            'ads_positions'  => $positions,
            'advertisements' => $advertisements,
        ]), 403);
    }

    /**
     * Generates a response when the error is caused by a broken database
     * connection.
     *
     * If database connection fails the rendered template has to grant that no
     * other connection attempts will be executed. Because of this, the template
     * engine used has to be configured with backend theme.
     *
     * @return Response The response object.
     */
    protected function getConnectionExceptionResponse()
    {
        return new Response(
            $this->get('core.template.admin')->fetch('error/500.tpl'),
            500
        );
    }

    /**
     * Generates a response when an unknown error happens.
     *
     * @return Response The response object.
     */
    protected function getErrorResponse()
    {
        return new Response($this->renderView('static_pages/statics.tpl'), 500);
    }

    /**
     * Generates a response when the error is caused by a resource that could
     * not be found.
     *
     * @return Response The response object.
     */
    protected function getNotFoundResponse()
    {
        list($positions, $advertisements) = ArticlesController::getAds();

        $this->view->setConfig('articles');

        $content = $this->renderView('static_pages/404.tpl', [
            'ads_positions'  => $positions,
            'advertisements' => $advertisements,
            'cache_id'       => $this->view->getCacheId('error', 404),
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
