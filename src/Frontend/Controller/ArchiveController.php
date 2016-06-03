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
        # Avoid Bing bot to crawl archive page
        $isBingBot = \Onm\Utils\BotDetector::isSpecificBot($request->headers->get('user-agent'), 'bingbot');
        if ($isBingBot) {
            return new RedirectResponse('/');
        }

        $today = new \DateTime();
        $today->modify('-1 day');

        $categoryName  = $request->query->filter('category_name', 'home', FILTER_SANITIZE_STRING);
        $year  = $request->query->filter('year', $today->format('Y'), FILTER_SANITIZE_STRING);
        $month = $request->query->filter('month', $today->format('m'), FILTER_SANITIZE_STRING);
        $day   = $request->query->filter('day', $today->format('d'), FILTER_SANITIZE_STRING);
        $page  = $request->query->getDigits('page', 1);

        $this->view = new \Template(TEMPLATE_USER);
        $this->view->setConfig('newslibrary');

        $itemsPerPage = 20;
        $date = "{$year}-{$month}-{$day}";

        $cacheID = $this->view->generateCacheId($date, '', $page);
        if (($this->view->caching == 0)
           || (!$this->view->isCached('archive/archive.tpl', $cacheID))
        ) {
            $er = getService('entity_repository');
            $order = [ 'fk_content_type' => 'asc', 'starttime' => 'desc' ];
            $criteria = [
                'in_litter'       => [[ 'value' => 0 ]],
                'fk_content_type' => [[ 'value' => [1,4,7,9], 'operator' => 'IN' ]],
                'DATE(starttime)' => [[ 'value' => '"'.$date.'"', 'field' => true ]]
            ];

            if ($categoryName != 'home') {
                $criteria['category_name'] = [[ 'value' => $categoryName ]];
            }

            $contents = $er->findBy($criteria, $order, $itemsPerPage, $page);
            $total = $er->countBy($criteria);

            $library  = [];
            if (!empty($contents)) {
                $cr = getService('category_repository');
                foreach ($contents as $content) {
                    // Create category group
                    if (!isset($library[$content->category])) {
                        $library[$content->category] = $cr->find($content->category);
                    }
                    // Fetch video or image for article and opinions
                    if (!empty($content->fk_video)) {
                        $content->video = $er->find('Video', $content->fk_video);
                    } elseif (!empty($content->img1)) {
                        $content->image = $er->find('Photo', $content->img1);
                    }
                    // Add contents to category group
                    $library[$content->category]->contents[] = $content;
                }
            }

            // Pagination for block more videos
            $pagination = $this->get('paginator')->get([
                'boundary'    => false,
                'directional' => true,
                'maxLinks'    => 0,
                'epp'         => $itemsPerPage,
                'page'        => $page,
                'total'       => $total,
                'route'       => [
                    'name'   => 'frontend_archive_content',
                    'params' => [
                        'day'   => $day,
                        'month' => $month,
                        'year'  => $year,
                    ]
                ]
            ]);

            # Only allow user to see 2 pages of archive
            if ($page > 1) {
                $pagination = null;
            }

            $this->view->assign([
                'library'    => $library,
                'pagination' => $pagination,
            ]);
        }

        $ads = $this->getAds();
        $this->view->assign('advertisements', $ads);

        return $this->render(
            'archive/archive.tpl',
            array(
                'cache_id'        => $cacheID,
                'newslibraryDate' => $date,
                'actual_category' => 'archive',
                'x-tags'          => 'archive-page,'.$date.','.$page.','.$categoryName,
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
        $positionManager = getService('core.theme')->getAdsPositionManager();
        $positions = $positionManager->getAdsPositionsForGroup('article_inner', array(7, 9));

        return \Advertisement::findForPositionIdsAndCategory($positions, $category);
    }
}
