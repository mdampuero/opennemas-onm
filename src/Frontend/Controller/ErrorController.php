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
use Symfony\Component\HttpKernel\Exception\GoneHttpException;

class ErrorController extends Controller
{
    /**
     * {@inheritdoc}
     */
    protected $groups = [
        'default' => 'article_inner'
    ];

    /**
     * {@inheritdoc}
     */
    protected $positions = [
        'article_inner' => [ 7 ]
    ];

    /**
     * Displays an error page based on the current error.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function defaultAction(Request $request)
    {
        $exception = $request->attributes->get('exception');
        $class     = new \ReflectionClass($exception->getClass());
        switch ($class->getShortName()) {
            case 'AccessDeniedException':
                return $this->getAccessDeniedResponse();

            case 'ConnectionException':
            case 'FatalThrowableError':
            case 'SmartyException':
            case 'BadRequestHttpException':
                return $this->getFatalErrorResponse(
                    $class->getShortName(),
                    $exception->getMessage()
                );

            case 'ResourceNotFoundException':
                $url = $this->container->get('core.redirector')
                    ->getUrl(preg_replace('/^\//', '', $request->getRequestUri()));

                if (!empty($url)) {
                        return $this->container->get('core.redirector')
                            ->getResponse($request, $url);
                }

                // no break

            case 'ContentNotMigratedException':
            case 'NotFoundHttpException':
                return $this->getNotFoundResponse($class->getShortName());
            case 'GoneHttpException':
                return $this->getGoneResponse($class->getShortName());
            default:
                return $this->getErrorResponse(
                    $class->getShortName(),
                    $exception->getMessage()
                );
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
        $this->get('application.log')->info('security.authorization.failure');

        return new Response($this->get('core.template.frontend')
            ->render('static_pages/403.tpl'), 403);
    }

    /**
     * Generates a response when an unknown error happens.
     *
     * @param string $class The short class name.
     * @param string $error The error message.
     *
     * @return Response The response object.
     */
    protected function getErrorResponse($class, $message)
    {
        $this->get('error.log')->error($class . ': ' . $message);

        return new Response($this->get('core.template.frontend')
            ->render('static_pages/statics.tpl'), 500);
    }

    /**
     * Generates a response when the error can not be handled properly.
     *
     * This will return a 500 error page without any information about the
     * error. It should be used only when the error can not be reported to
     * the user for some reason.
     *
     * @param string $class   The short class name.
     * @param string $message The error message.
     *
     * @return Response The response object.
     */
    protected function getFatalErrorResponse($class, $message)
    {
        $message = 'failure: ' . $message;

        if ($class === 'ConnectionException') {
            $message = 'database.connection.' . $message;
        }

        $this->get('error.log')->error($message);

        // Remove assets from bag from a previous run
        $this->get('core.service.assetic.asset_bag')->reset();

        return new Response(file_get_contents(
            $this->getParameter('core.paths.public') . '/500.html'
        ), 500);
    }

    /**
     * Generates a response when the error is caused by a resource that could
     * not be found.
     *
     * @param string $class The short class name.
     *
     * @return Response The response object.
     */
    protected function getNotFoundResponse($class)
    {
        $this->get('application.log')->info($class);

        $this->view->setConfig('articles');

        $content = $this->get('core.template.frontend')
            ->render('static_pages/404.tpl', [
                'cache_id' => $this->view->getCacheId('error', 404),
            ]);

        return new Response($content, 404, [
            'x-cacheable' => true,
            'x-tags'      => 'not-found',
        ]);
    }

    /**
     * Generates a response when the error is caused by a resource that could
     * not be found.
     *
     * @param string $class The short class name.
     *
     * @return Response The response object.
     */
    protected function getGoneResponse($class)
    {
        $this->get('application.log')->info($class);

        $this->view->setConfig('articles');

        $content = $this->get('core.template.frontend')
            ->render('static_pages/410.tpl', [
                'cache_id' => $this->view->getCacheId('error', 410),
            ]);

        return new Response($content, 410, [
            'x-cacheable' => true,
            'x-tags'      => 'not-found',
        ]);
    }
}
