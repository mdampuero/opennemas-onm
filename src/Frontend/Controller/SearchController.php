<?php
/**
 * Handles the actions for searches
 *
 * @package Frontend_Controllers
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Frontend\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for searches
 *
 * @package Frontend_Controllers
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
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function googleAction()
    {
        \Frontend\Controller\FrontpagesController::getAds();

        return $this->render('search/search.tpl');
    }

    /**
     * Displays the search results with the internal algorithm
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function internalAction($request)
    {
        // TODO: Implement
        return new Response('Not implemented', 501);
    }
}
