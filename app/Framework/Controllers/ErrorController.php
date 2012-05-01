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
        require realpath(__DIR__."/../Views/ErrorController/default.php");
    }

} // END class Authentication