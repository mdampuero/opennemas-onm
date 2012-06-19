<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Backend\Controllers;

use Onm\Framework\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Onm\Message as m;
/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 * @author
 **/
class ErrorController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     * @author
     **/
    public function init()
    {
        $this->view = new \TemplateAdmin(TEMPLATE_ADMIN);
    }

    /**
     * Description of the action
     *
     * @return void
     **/
    public function defaultAction()
    {
        global $error;
        $error = unserialize($this->request->get('error'));

        if ($this->container->hasParameter('environment')) {
            $environment = $this->container->getParameter('environment');
        }

        $name = join('', array_slice(explode('\\', get_class($error)), -1));

        $errorID = strtoupper(INSTANCE_UNIQUE_NAME.'_'.uniqid());

        switch ($name) {
            case 'ResourceNotFoundException':
                $trace = $error->getTrace();
                $path = $trace[0]['args'][0];

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
                            'backtrace'     => array_reverse($error->getTrace()),
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
                        'backtrace'     => array_reverse($error->getTrace()),
                    )
                );

                break;
        }
        return new Response($content, 500);
    }

} // END class ErrorController