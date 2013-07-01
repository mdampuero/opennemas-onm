<?php
/**
 * Handles all the request for Welcome actions
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
namespace Backend\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;

/**
 * Handles all the request for Welcome actions
 *
 * @package Backend_Controllers
 **/
class WelcomeController extends Controller
{
    /**
     * Initializes the welcome controller
     *
     * @return void
     **/
    public function init()
    {
    }

    /**
     * Handles the default action
     *
     * @param Request $request the request object
     *
     * @return void
     **/
    public function defaultAction(Request $request)
    {
        // $this->dispatchEvent('content.update', array('content' => new \Article()));
        // $instances = $this->get('db_conn')->Execute('SELECT count(*) FROM instances');
        //
        // $this->get('request')->getSession()->getFlashBag()->add(
        //     'notice',
        //     'Your changes were saved!'
        // );
        return $this->render('welcome/index.tpl');
    }
}
