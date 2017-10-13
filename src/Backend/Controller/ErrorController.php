<?php
/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 */
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backend\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;
use Onm\Settings as s;

/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 */
class ErrorController extends Controller
{
    /**
     * Displays an error
     *
     * @param Request $request the request object
     *
     * @return void
     */
    public function defaultAction(Request $request)
    {
        if ($this->container->hasParameter('environment')) {
            $environment = $this->container->getParameter('environment');
        }

        $error = $request->attributes->get('exception');

        $exceptionName = $error->getClass();

        if (defined('INSTANCE_UNIQUE_NAME')) {
            $errorID = strtoupper(INSTANCE_UNIQUE_NAME . '_' . uniqid());
        } else {
            $errorID = strtoupper('ONM_FRAMEWORK_' . uniqid());
        }

        if (!defined('CURRENT_LANGUAGE')) {
            define('CURRENT_LANGUAGE', 'en_US');
        }

        $this->view = $this->get('onm_templating')->getBackendTemplate();

        $logMessage = '';

        $requestAddress = $request->getSchemeAndHttpHost() . $request->getRequestUri();
        switch ($exceptionName) {
            case 'Common\Core\Component\Exception\InstanceNotRegisteredException':
                $trace = $error->getTrace();

                $logMessage = 'Backend instance not registered error at '
                    . $requestAddress . ' ' . $error->getMessage() . ' ' . json_encode($error->getTrace());

                $errorMessage = _('Instance not found');
                $content      = $errorMessage;

                if (!$this->request->isXmlHttpRequest()) {
                    $content = $this->renderView('error/instance_not_found.tpl', [
                        'server'      => $request->server,
                        'error_message' => $errorMessage,
                        'error'         => $error,
                        'error_id'      => $errorID,
                        'environment'   => $environment,
                        'backtrace'     => $error->getTrace(),
                    ]);
                }

                $response = new Response($content, 404);
                break;
            case 'Common\Core\Component\Exception\InstanceNotActivatedException':
                $trace = $error->getTrace();

                $logMessage = 'Backend instance not activated error at '
                    . $requestAddress . ' ' . $error->getMessage() . ' ' . json_encode($error->getTrace());

                $errorMessage = _('Instance not activated');
                $content      = $errorMessage;

                if (!$this->request->isXmlHttpRequest()) {
                    $content = $this->renderView('error/instance_not_activated.tpl', [
                        'server'        => $request->server,
                        'error_message' => $errorMessage,
                        'error'         => $error,
                        'error_id'      => $errorID,
                        'environment'   => $environment,
                        'backtrace'     => $error->getTrace(),
                    ]);
                }

                $response = new Response($content, 404);
                break;
            case 'ResourceNotFoundException':
            case 'Symfony\Component\HttpKernel\Exception\NotFoundHttpException':
                $trace = $error->getTrace();
                $path  = $request->getRequestUri();

                $logMessage = 'Backend page not found at: ' . $requestAddress;

                $errorMessage = sprintf('Oups! We can\'t find anything at "%s".', $path);
                $content      = $errorMesage;

                if (!$this->request->isXmlHttpRequest()) {
                    $content = $this->renderView('error/404.tpl', [
                        'error_message' => $errorMessage,
                        'error'         => $error,
                        'environment'   => $environment,
                        'backtrace'     => $error->getTrace(),
                    ]);
                }

                $response = new Response($content, 404);
                break;
            case 'Symfony\Component\Security\Core\Exception\AccessDeniedException':
                $errorMessage = _('You are not allowed to perform this action.');

                $content = $errorMessage;
                if (!$this->request->isXmlHttpRequest()) {
                    $content = $this->renderView('error/404.tpl', [
                        'error_message' => $errorMessage,
                        'error'         => $error,
                        'environment'   => $environment,
                        'backtrace'     => $error->getTrace(),
                    ]);
                }

                $response = new Response($content, 401);
                break;
            default:
                // Change this handle to a more generic error template
                $errorMessage = _('Oups! Seems that we had an unknown problem'
                    . ' while trying to run your request.');

                if ($environment == 'development') {
                    $errorMessage = $error->getMessage();
                }

                $logMessage = 'ERROR_ID: ' . $errorID . ' - '
                    . $error->getMessage() . " " . json_encode($error->getTrace());

                $content = $this->renderView('error/404.tpl', [
                    'error_message' => $errorMessage,
                    'error'         => $error,
                    'error_id'      => $errorID,
                    'environment'   => $environment,
                    'backtrace'     => $error->getTrace(),
                ]);

                $response = new Response($content, 500);
                break;
        }

        if (!empty($logMessage)) {
            error_log($logMessage);
        }

        return $response;
    }
}
