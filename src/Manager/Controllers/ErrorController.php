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
namespace Manager\Controllers;

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
        $error = $request->attributes->get('exception');

        if ($this->container->hasParameter('environment')) {
            $environment = $this->container->getParameter('environment');
        }

        $name = join('', array_slice(explode('\\', get_class($error)), -1));

        $errorID = strtoupper(INSTANCE_UNIQUE_NAME.'_'.uniqid());

        switch ($name) {
            case 'ResourceNotFoundException':
            case 'NotFoundHttpException':
                $trace = $error->getTrace();
                $path = $request->getRequestUri();

                $errorMessage = sprintf('Oups! We can\'t find anything at "%s".', $path);
                error_log('File not found: '.$path.'ERROR_ID: '.$errorID);
                if ($this->request->isXmlHttpRequest()) {
                    $content = $errorMesage;
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
                error_log('Unknown error. ERROR_ID: '.$errorID);

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
