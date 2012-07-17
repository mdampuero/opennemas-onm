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

use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;

/**
 * Handles all the request for Welcome actions
 *
 * @package Backend_Controllers
 * @author
 **/
class WelcomeController extends Controller
{

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function init()
    {
        $this->view = new \TemplateAdmin(TEMPLATE_ADMIN);
    }
    /**
     * Handles the default action
     *
     * @return void
     **/
    public function defaultAction()
    {
        // throw new \Exception('Something really bad happened. A little bird died!');

        $_SESSION['desde'] = 'index_portada';

        $feeds = array (
            array('name' => 'El pais', 'url'=> 'http://www.elpais.com/rss/feed.html?feedId=1022'),
            array('name' => '20 minutos', 'url'=> 'http://20minutos.feedsportal.com/c/32489/f/478284/index.rss'),
            array('name' => 'Publico.es', 'url'=> 'http://www.publico.es/rss/'),
            array('name' => 'El mundo', 'url'=> 'http://elmundo.feedsportal.com/elmundo/rss/portada.xml'),
        );

        return $this->render('welcome/index.tpl', array(
            'feeds' => $feeds
        ));

    }
} // END class Welcome
