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

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
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
        $errorID = strtoupper(INSTANCE_UNIQUE_NAME.'_'.uniqid());

        $this->view = new \Template(TEMPLATE_USER);
        switch ($name) {
            case 'ResourceNotFoundException':
            case 'NotFoundHttpException':
                $path = $request->getRequestUri();

                $page = new \stdClass();

                // Dummy content while testing this feature
                $page->title   = _('No hemos podido encontrar la página que buscas.');
                $page->content = 'Whoups!';

                $errorMessage = sprintf('Oups! We can\'t find anything at "%s".', $path);
                // error_log('File not found: '.$path.'ERROR_ID: '.$errorID);
                if ($this->request->isXmlHttpRequest()) {
                    $content = $errorMessage;
                } else {
                    $content = $this->renderView(
                        'static_pages/404.tpl',
                        array(
                            'category_real_name' => $page->title,
                            'page'               => $page,
                        )
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

                error_log('Unknown error. ERROR_ID: '.$errorID.' - '.$error->getMessage());

                // Dummy content while testing this feature
                $page = new \stdClass();
                $page->title   = $errorMessage;
                $page->content = 'Whoups!';

                $content = $this->renderView(
                    'static_pages/statics.tpl',
                    array(
                        'category_real_name' => $page->title,
                        'page'               => $page,
                        'error_message' => $errorMessage,
                        'error'         => $error,
                        'error_id'      => $errorID,
                        'environment'   => $environment,
                        'backtrace'     => $error->getTrace(),
                    )
                );

                return new Response($content, 500);

                break;
        }
    }
}
