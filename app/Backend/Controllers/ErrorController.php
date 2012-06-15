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
        // var_dump($error);die();

        if ($this->container->hasParameter('environment')) {
            $environment = $this->container->getParameter('environment');
        }

        $name = join('', array_slice(explode('\\', get_class($error)), -1));

        switch ($name) {
            case 'ResourceNotFoundException':
                $errorMessage = 'Resource path not found';
                $content = $this->renderView(
                    'error/404.tpl',
                    array(
                        'error' => $error,
                        'environment' => $environment,
                        'backtrace' => array_reverse($error->getTrace()),
                    )
                );
                break;

            default:
                $content = '';
                break;
        }
        return new Response($content, 500);
    }

} // END class ErrorController