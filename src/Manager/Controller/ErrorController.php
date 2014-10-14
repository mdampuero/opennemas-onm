<?php
/**
 * Handles the errors throughn in manager
 *
 * @package Manager_Controllers
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Manager\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;

/**
 * Handles the errors throughn in manager
 *
 * @package Manager_Controllers
 **/
class ErrorController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        $this->view = new \TemplateManager(TEMPLATE_MANAGER);
    }

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
        $error = $request->attributes->get('exception');

        $exceptionName = $error->getClass();

        if (defined('INSTANCE_UNIQUE_NAME')) {
            $errorID = strtoupper(INSTANCE_UNIQUE_NAME.'_'.uniqid());
        } else {
            $errorID = strtoupper('ONM_FRAMEWORK_'.uniqid());
        }

        $preview = \Backend\Controller\ErrorController::highlightSource($error->getFile(), $error->getLine(), 7);

        $this->view->assign('preview', $preview);

        switch ($exceptionName) {
            case 'Symfony\Component\HttpKernel\Exception\NotFoundHttpException':

                $path = $request->getRequestUri();
                $errorMessage = sprintf('Oups! We can\'t find anything at "%s".', $path);
                error_log('File not found: '.$path.'ERROR_ID: '.$errorID);
                if ($this->request->isXmlHttpRequest()) {
                    $content = $errorMessage;
                } else {
                    $content = $this->renderView(
                        'error/404.tpl',
                        array(
                            'error_message' => $errorMessage,
                            'error'         => $error,
                            'error_id'      => $errorID,
                            'environment'   => $environment,
                            'backtrace'     => $error->getTrace(),
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

                $content = $this->renderView(
                    'error/404.tpl',
                    array(
                        'error_message' => $errorMessage,
                        'error'         => $error,
                        'error_id'      => $errorID,
                        'environment'   => $environment,
                        'backtrace'     => $error->getTrace(),
                    )
                );

                break;
        }

        return new Response($content, 500);
    }
}
