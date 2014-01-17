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
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for advertisements
 *
 * @package Frontend_Controllers
 **/
class StaticPagesController extends Controller
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
     * Displays the static page given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function showAction(Request $request)
    {
        $slug = $request->query->filter('slug', null, FILTER_SANITIZE_STRING);

        $page = \StaticPage::getPageBySlug($slug);

        // if static page doesn't exist redirect to 404 error page.
        if (is_null($page) || (!$page->available)) {
            throw new ResourceNotFoundException();
        }

        $ads = $this->getAds();
        $this->view->assign('advertisements', $ads);

        return $this->render(
            'static_pages/statics.tpl',
            array(
                'page'               => $page,
                'content'            => $page,
                'actual_category'    => $page->slug,
                'category_real_name' => $page->title,
                'content_id'         => $page->id
            )
        );
    }

    /**
     * Returns all the advertisements for an static page
     *
     * @return void
     **/
    public static function getAds()
    {
        $category = 0;

        // Get static_pages positions
        $positionManager = getService('instance_manager')->current_instance->theme->getAdsPositionManager();
        $positions = $positionManager->getAdsPositionsForGroup(null, array(1, 2, 103, 105, 7, 9, 10));

        return \Advertisement::findForPositionIdsAndCategory($positions, $category);
    }
}
