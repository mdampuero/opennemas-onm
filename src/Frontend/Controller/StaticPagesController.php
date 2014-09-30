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
     * Displays the static page given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function showAction(Request $request)
    {
        $slug = $request->query->filter('slug', null, FILTER_SANITIZE_STRING);

        $content = \StaticPage::getPageBySlug($slug);

        // if static page doesn't exist redirect to 404 error page.
        if (is_null($content) || (!$content->content_status)) {
            throw new ResourceNotFoundException();
        }

        $ads = $this->getAds();

        $this->view = new \Template(TEMPLATE_USER);
        return $this->render(
            'static_pages/statics.tpl',
            array(
                'page'               => $content,
                'content'            => $content,
                'actual_category'    => $content->slug,
                'category_real_name' => $content->title,
                'content_id'         => $content->id,
                'advertisements'     => $ads,
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
        $positions = $positionManager->getAdsPositionsForGroup('article_inner', array(1, 2, 5, 6, 7, 9));

        return \Advertisement::findForPositionIdsAndCategory($positions, $category);
    }
}
