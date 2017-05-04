<?php
/**
 * Handles the actions for advertisements
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

use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;
use Onm\Settings as s;

/**
 * Handles the actions for advertisements
 *
 * @package Frontend_Controllers
 *
 * TODO: REMOVE ME PLEASE! This class is a deprecated in favor of StaticpageController.php
 */
class StaticPagesController extends Controller
{
    /**
     * Displays the static page given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function showAction(Request $request)
    {
        $slug = $request->query->filter('slug', null, FILTER_SANITIZE_STRING);

        $content = getService('entity_repository')->findOneBy([
            'slug'              => [[ 'value' => $slug ]],
            'content_type_name' => [[ 'value' => 'static_page' ]]
        ]);

        // if static page doesn't exist redirect to 404 error page.
        if (is_null($content) || (!$content->content_status)) {
            throw new ResourceNotFoundException();
        }

        // Setup templating cache layer
        $this->view->setConfig('article-inner');
        $cacheID = $this->view->getCacheId('content', $content->id);

        list($positions, $advertisements) = $this->getAds();

        return $this->render('static_pages/statics.tpl', [
            'actual_category'    => $content->slug,
            'ads_positions'      => $positions,
            'advertisements'     => $advertisements,
            'category_real_name' => $content->title,
            'content'            => $content,
            'content_id'         => $content->id,
            'page'               => $content,
            'cache_id'           => $cacheID,
            'x-tags'             => 'static-page,'.$content->id,
        ]);
    }

    /**
     * Returns all the advertisements for an static page
     *
     * @return void
     **/
    public static function getAds()
    {
        $positionManager = getService('core.manager.advertisement');
        $positions       = $positionManager->getPositionsForGroup('article_inner', [1, 2, 5, 6, 7, 9]);
        $advertisements  = \Advertisement::findForPositionIdsAndCategory($positions, 0);

        return [ $positions, $advertisements ];
    }
}
