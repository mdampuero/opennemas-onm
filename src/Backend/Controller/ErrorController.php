<?php
/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Backend\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;

/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 **/
class ErrorController extends Controller
{
    /**
     * Displays an error
     *
     * @param Request $request the request object
     *
     * @return void
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

        $this->view = new \TemplateAdmin();

        $preview = self::highlightSource($error->getFile(), $error->getLine(), 7);

        $this->view->assign('preview', $preview);

        switch ($exceptionName) {
            case 'Onm\Exception\InstanceNotRegisteredException':
                $trace = $error->getTrace();

                $errorMessage = _('Instance not found');
                if ($this->request->isXmlHttpRequest()) {
                    $content = $errorMessage;
                } else {
                    $content = $this->renderView(
                        'error/instance_not_found.tpl',
                        array(
                            'server'      => $request->server,
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

            case 'Onm\Instance\NotActivatedException':
                $trace = $error->getTrace();

                $errorMessage = _('Instance not activated');
                if ($this->request->isXmlHttpRequest()) {
                    $content = $errorMesage;
                } else {
                    $content = $this->renderView(
                        'error/instance_not_activated.tpl',
                        array(
                            'server'        => $request->server,
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

            case 'ResourceNotFoundException':
            case 'Symfony\Component\HttpKernel\Exception\NotFoundHttpException':
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
                            'environment'   => $environment,
                            'backtrace'     => $error->getTrace(),
                        )
                    );
                }

                return new Response($content, 404);
                break;

            case 'Onm\Security\Exception\AccessDeniedException':
            case 'Symfony\Component\Security\Core\Exception\AccessDeniedException':
                $errorMessage = _('You are not allowed to perform this action.');

                if ($this->request->isXmlHttpRequest()) {
                    $content = $errorMesage;
                } else {
                    $content = $this->renderView(
                        'error/404.tpl',
                        array(
                            'error_message' => $errorMessage,
                            'error'         => $error,
                            'environment'   => $environment,
                            'backtrace'     => $error->getTrace(),
                        )
                    );
                }

                return new Response($content, 401);

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

    /**
     * Returns an exceprt HTML with the content of the file highlighting
     * the line that produces the error.
     *
     * @param string $fileName  The name of the file where is the error
     * @param string $lineNumber  The line inside the file where is the error
     * @param int    $showLines The number of lines to show before and after the error line
     *
     * @return strin The HTML with the highlighted source.
     **/
    public static function highlightSource($fileName, $lineNumber, $showLines)
    {

        $lines = htmlspecialchars(file_get_contents($fileName));
        $lines = nl2br($lines);
        $lines = explode("<br />", $lines);

        $offset = max(0, $lineNumber - ceil($showLines / 2));

        $lines = array_slice($lines, $offset, $showLines);

        $html = '';
        foreach ($lines as $line) {
            $offset++;
            $line = preg_replace("@\s@", "&nbsp;", $line);

            if ($offset == $lineNumber) {
                $line = '<em class="lineno highlighted">'
                        . sprintf('%4d', $offset) . ' </em>' . $line . '<br/>';
                $html .= '<div class="code highlighted">'
                        . $line . '</div>';
            } else {
                $line = '<em class="lineno">'
                        . sprintf('%4d', $offset) . ' </em>' . $line . '<br/>';
                $html .= $line;
            }
        }

        return $html;
    }
}
