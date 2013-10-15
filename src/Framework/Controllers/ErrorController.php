<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Framework\Controllers;

use Onm\Framework\Controller\Controller,
    Symfony\Component\Routing\Exception\ResourceNotFoundException,
    Onm\Message as m;

/**
 * Handles all the request for Welcome actions
 *
 * @package Backend_Controllers
 * @author
 **/
class ErrorController extends Controller
{

    /**
     * Common actions for all the actions
     *
     * @return void
     * @author
     **/
    public function init()
    {
    }

    // TODO: find a way to render a simple file with smarty without initializing
    // all the
    /**
     * Shows the login form
     *
     * @return string the response string
     **/
    public function defaultAction()
    {
        global $error;
        $error = unserialize($this->request->get('error'));
        // var_dump($error);die();

        if ($this->container->hasParameter('environment')
            && $this->container->getParameter('environment') == 'development'
        ) {
            // print("<html><body>".$error->xdebug_message."</body></html>");die();

            $name = join('', array_slice(explode('\\', get_class($error)), -1));
            switch ($name) {
                case 'ResourceNotFoundException':
                    $errorMessage = 'Resource path not found';

                    break;

                default:
                    $errorMessage = '';
                    break;
            }

            return include __DIR__."/../Views/ErrorController/default.php";
        }
        header('HTTP/1.0 404 Not Found');

        return include __DIR__."/../Views/ErrorController/default-production.php";
    }

}
