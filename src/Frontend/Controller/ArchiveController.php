<?php
/**
 * Defines the frontend controller for the content archives
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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for newslibrary
 *
 * @package Frontend_Controllers
 **/
class ArchiveController extends Controller
{
    /**
     * Get news library from content table in database
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function archiveAction(Request $request)
    {
        $today = new \DateTime();
        $today->modify('-1 day');

        $year  = $request->query->filter('year', $today->format('Y'), FILTER_SANITIZE_STRING);
        $month = $request->query->filter('month', $today->format('m'), FILTER_SANITIZE_STRING);
        $day   = $request->query->filter('day', $today->format('d'), FILTER_SANITIZE_STRING);
        $page  = $request->query->getDigits('page', 1);

        $view = new \Template(TEMPLATE_USER);
        $view->setConfig('newslibrary');

        $date = "{$year}-{$month}-{$day}";

        $cacheID = $this->view->generateCacheId($date, '', $page);
        if (($this->view->caching == 0)
           || (!$this->view->isCached('archive/archive.tpl', $cacheID))
        ) {
            $cm = new \ContentManager();
            $this->ccm = new \ContentCategoryManager();
            $allCategories = $this->ccm->categories;

            $library  = array();
            $contents = $cm->getContentsForLibrary($date);

            if (!empty($contents)) {
                foreach ($contents as $content) {
                    $categoryID = $content->category;
                    if (!isset($library[$categoryID])) {
                        $library[$categoryID] = new \stdClass();
                    }
                    $library[$categoryID]->id         = $categoryID;
                    $library[$categoryID]->title      = $allCategories[$categoryID]->title;
                    $library[$categoryID]->contents[] = $content;
                }
            }

            $this->view->assign('library', $library);
        }

        $ads = $this->getAds();
        $this->view->assign('advertisements', $ads);

        return $this->render(
            'archive/archive.tpl',
            array(
                'cache_id' => $cacheID,
                'newslibraryDate' => $date,
                'actual_category' => 'archive',
            )
        );
    }

    /**
     * Get newslibrary from content table in database
     *
     * @return Response the response object
     **/
    public function archiveCategoryAction(Request $request)
    {
        $today = new \DateTime();
        $today->modify('-1 day');
        $year  = $request->query->filter('year', $today->format('Y'), FILTER_SANITIZE_STRING);
        $month = $request->query->filter('month', $today->format('m'), FILTER_SANITIZE_STRING);
        $day   = $request->query->filter('day', $today->format('d'), FILTER_SANITIZE_STRING);
        $page          = $request->query->getDigits('page', 1);
        $categoryName  = $request->query->filter('category_name', 'home', FILTER_SANITIZE_STRING);

        $this->ccm = new \ContentCategoryManager();
        if (empty($categoryName) || ($categoryName == 'home')) {
            $categoryID = 0;
        } else {
            $categoryID = $this->ccm->get_id($categoryName);
        }

        $date = "{$year}-{$month}-{$day}";

        $this->view = new \Template(TEMPLATE_USER);
        $this->view->setConfig('newslibrary');

        $cacheID = $this->view->generateCacheId($date.'_'.$categoryName, '', $page);
        if (($this->view->caching == 0)
           || (!$this->view->isCached('archive/archive.tpl', $cacheID))
        ) {

            $cm = new \ContentManager();

            $library  = array();
            $library[$categoryID] = new \stdClass();
            $contents = $cm->getContentsForLibrary($date, $categoryID);

            if (!empty($contents)) {
                foreach ($contents as $content) {
                    $library[$categoryID]->id         = $categoryID;
                    $library[$categoryID]->title      = $categoryName;
                    $library[$categoryID]->contents[] = $content;
                }
            }

            $this->view->assign('library', $library);
        }

        $ads = $this->getAds();
        $this->view->assign('advertisements', $ads);

        return $this->render(
            'archive/archive.tpl',
            array(
                'cache_id' => $cacheID,
                'category_name'   => $categoryName,
                'actual_category' => 'archive',
                'newslibraryDate' => $date,
            )
        );
    }

    /**
     * Get frontpage version from file
     *
     * "/archive/content/yyyy/mm/dd"
     * "/archive/content/yyyy/mm/dd/category.html"
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function digitalFrontpageAction(Request $request)
    {
        $today = new \DateTime();
        $today->modify('-1 day');
        $year  = $request->query->filter('year', $today->format('Y'), FILTER_SANITIZE_STRING);
        $month = $request->query->filter('month', $today->format('m'), FILTER_SANITIZE_STRING);
        $day   = $request->query->filter('day', $today->format('d'), FILTER_SANITIZE_STRING);
        $categoryName  = $request->query->filter('category_name', 'home', FILTER_SANITIZE_STRING);

        $path = "{$year}/{$month}/{$day}";
        $html = '';
        $file = MEDIA_PATH."/library/{$path}/{$categoryName}.html";
        $url = "/archive/content/{$path}/";
        if (file_exists($file) && is_readable($file)) {
            $html = file_get_contents(SITE_URL.INSTANCE_MEDIA."library/{$path}/{$categoryName}.html");
        } else {
            return new RedirectResponse($url, 301);
            //throw new \Symfony\Component\Routing\Exception\ResourceNotFoundException();
        }

        if (empty($html)) {
            return new RedirectResponse($url, 301);
        }

        return new Response($html);
    }

    /**
     * Returns the advertisements for the archive template
     *
     * @return array the list of advertisement objects
     **/
    public function getAds()
    {
        $category = 0;

        // Get letter positions
        $positionManager = getService('instance_manager')->current_instance->theme->getAdsPositionManager();
        $positions = $positionManager->getAdsPositionsForGroup('article_inner', array(7, 9));

        return \Advertisement::findForPositionIdsAndCategory($positions, $category);
    }
}
