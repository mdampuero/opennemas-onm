<?php
/**
 * Defines the frontend error handler
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

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;
use Onm\Settings as s;

/**
 * Handlers errors in frontend
 *
 * @package Frontend_Controllers
 **/
class ErrorController extends Controller
{
    /**
     * Shows the error page
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function defaultAction(Request $request)
    {
        if ($this->container->hasParameter('environment')) {
            $environment = $this->container->getParameter('environment');
        }

        // Fetch error information
        $error = $request->attributes->get('exception');
        $name = join('', array_slice(explode('\\', $error->getClass()), -1));
        if (!defined('INSTANCE_UNIQUE_NAME')) {
            define('INSTANCE_UNIQUE_NAME', 'unknown-instance');
        }

        $errorID = strtoupper(INSTANCE_UNIQUE_NAME.'_'.uniqid());

        $requestAddress = $request->getSchemeAndHttpHost().$request->getRequestUri();
        switch ($name) {
            case 'ContentNotMigratedException':
            case 'ResourceNotFoundException':
            case 'NotFoundHttpException':
                $path = $request->getRequestUri();

                // Redirect to redirectors URLs without /
                if ($name === 'NotFoundHttpException') {
                    $url = $this->generateUrl('frontend_redirect_content', [
                    'slug'  => mb_ereg_replace('^\/', '', $path)
                    ]);

                    return new RedirectResponse($url, 301);
                }

                $page = new \stdClass();

                error_log('Frontend page not found error at: '.$requestAddress);

                // Dummy content while testing this feature
                $page->title   = _('Unable to find the page you are looking for.');
                $page->content = 'Whoups!';

                $errorMessage = sprintf('Oups! We can\'t find anything at "%s".', $path);
                if ($this->request->isXmlHttpRequest()) {
                    $content = $errorMessage;
                } else {
                    list($positions, $advertisements) =
                        \Frontend\Controller\ArticlesController::getAds();

                    // Load config
                    $this->view->setConfig('articles');

                    $cacheID = $this->view->generateCacheId('error', null, 404);
                    $content = $this->renderView(
                        'static_pages/404.tpl',
                        [
                            'cache_id'           => $cacheID,
                            'category_real_name' => $page->title,
                            'page'               => $page,
                            'advertisements'     => $advertisements,
                            'ads_positions'      => $positions,
                            'x-tags'             => 'not-found',
                            'x-cache-for'        => '+1 day'
                        ]
                    );
                }

                return new Response($content, 404);
                break;
            default:
                // Change this handle to a more generic error template
                $errorMessage = _('Oups! Seems that we had an unknown problem while trying to run your request.');

                if ($environment == 'development') {
                    $errorMessage = $error->getMessage();
                }

                error_log('Frontend unknown error at '.$requestAddress.' '.$error->getMessage().' '.json_encode($error->getTrace()));

                // Dummy content while testing this feature
                $page = new \stdClass();
                $page->title   = $errorMessage;
                $page->content = 'Whoups!';

                $content = $this->renderView(
                    'static_pages/statics.tpl',
                    [
                        'category_real_name' => $page->title,
                        'page'               => $page,
                        'error_message'      => $errorMessage,
                        'error'              => $error,
                        'error_id'           => $errorID,
                        'environment'        => $environment,
                        'backtrace'          => $error->getTrace(),
                    ]
                );

                return new Response($content, 500);

                break;
        }
    }
}
