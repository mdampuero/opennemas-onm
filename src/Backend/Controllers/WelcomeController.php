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
        $this->get('session')->setFlash(
            'notice',
            'Your changes were saved!'
        );


        $feeds = array (
            array('name' => 'El pais', 'url'=> 'http://www.elpais.com/rss/feed.html?feedId=1022'),
            array('name' => '20 minutos', 'url'=> 'http://20minutos.feedsportal.com/c/32489/f/478284/index.rss'),
            array('name' => 'Publico.es', 'url'=> 'http://www.publico.es/rss/'),
            array('name' => 'El mundo', 'url'=> 'http://elmundo.feedsportal.com/elmundo/rss/portada.xml'),
        );

        return $this->render(
            'welcome/index.tpl',
            array(
                'feeds' => $feeds
            )
        );

    }
}
