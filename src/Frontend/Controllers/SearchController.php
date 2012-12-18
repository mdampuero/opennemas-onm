<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Frontend\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for searches
 *
 * @package Backend_Controllers
 **/
class SearchController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        $this->view = new \Template(TEMPLATE_USER);
    }

    /**
     * Displays the search results with the google algorithm
     *
     * @return Response the response object
     **/
    public function googleAction(Request $request)
    {
        require_once APP_PATH.'/../public/controllers/index_advertisement.php';

        return $this->render('search/search.tpl');
    }

    /**
     * Displays the search results with the internal algorithm
     *
     * @return Response the response object
     **/
    public function internalAction($request)
    {
        // TODO: Implement
        return new Response('Not implemented', 501);
    }
}
