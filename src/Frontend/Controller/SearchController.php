<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Frontend\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;

/**
 * Handles the actions for searches
 *
 * @package Frontend_Controllers
 **/
class SearchController extends Controller
{
    /**
     * Displays the search results with the google algorithm.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function googleAction()
    {
        list($positions, $advertisements) =
            \Frontend\Controller\ArticlesController::getAds();

        return $this->render('search/search.tpl', [
            'ads_positions'  => $positions,
            'advertisements' => $advertisements,
            'x-tags'         => 'google-search'
        ]);
    }

    /**
     * Displays the search results with the internal algorithm.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function internalAction()
    {
        // TODO: Implement
        return new Response('Not implemented', 501);
    }
}
