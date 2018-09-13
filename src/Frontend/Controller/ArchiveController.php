<?php
/**
 * Defines the frontend controller for the content archives
 *
 * @package Frontend_Controllers
 */
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Frontend\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Common\Core\Controller\Controller;

/**
 * Handles the actions for newslibrary
 *
 * @package Frontend_Controllers
 */
class ArchiveController extends Controller
{
    /**
     * Get news library from content table in database
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @BotDetector(bot="bingbot", route="frontend_frontpage")
     */
    public function archiveAction(Request $request)
    {
        $today = new \DateTime();
        $today->modify('-1 day');

        $year         = $request->query->filter('year', $today->format('Y'), FILTER_SANITIZE_STRING);
        $month        = $request->query->filter('month', $today->format('m'), FILTER_SANITIZE_STRING);
        $day          = $request->query->filter('day', $today->format('d'), FILTER_SANITIZE_STRING);
        $categoryName = $request->query->filter('category_name', 'home', FILTER_SANITIZE_STRING);
        $page         = $request->query->getDigits('page', 1);
        $date         = "{$year}-{$month}-{$day}";
        $itemsPerPage = 20;

        // Setup templating cache layer
        $this->view->setConfig('newslibrary');
        $cacheID = $this->view->getCacheId('archive', $date, $page);

        if (($this->view->getCaching() === 0)
           || (!$this->view->isCached('archive/archive.tpl', $cacheID))
        ) {
            $er       = $this->get('entity_repository');
            $order    = [ 'fk_content_type' => 'asc', 'starttime' => 'desc' ];
            $criteria = [
                'in_litter'       => [[ 'value' => 0 ]],
                'content_status'  => [[ 'value' => 1 ]],
                'fk_content_type' => [[ 'value' => [1, 4, 7, 9], 'operator' => 'IN' ]],
                'DATE(starttime)' => [[ 'value' => '"' . $date . '"', 'field' => true ]]
            ];

            if ($categoryName != 'home') {
                $criteria['category_name'] = [[ 'value' => $categoryName ]];
            }

            $contents = $er->findBy($criteria, $order, $itemsPerPage, $page);
            $total    = $er->countBy($criteria);
            $library  = [];

            $cr = $this->get('category_repository');
            foreach ($contents as $content) {
                // Create category group
                if (!isset($library[$content->category])) {
                    $library[$content->category]           = $cr->find($content->category);
                    $library[$content->category]->contents = [];
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

            // Only allow user to see 2 pages of archive
            if ($page > 1) {
                $pagination = null;
            }

            $this->view->assign([
                'library'    => $library,
                'pagination' => $pagination,
            ]);
        }

        list($positions, $advertisements) = $this->getAds();

        return $this->render('archive/archive.tpl', [
            'ads_positions'   => $positions,
            'advertisements'  => $advertisements,
            'cache_id'        => $cacheID,
            'newslibraryDate' => $date,
            'actual_category' => 'archive',
            'x-tags'          => 'archive-page,' . $date . ',' . $page . ',' . $categoryName,
        ]);
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
     */
    public function digitalFrontpageAction(Request $request)
    {
        $today = new \DateTime();
        $today->modify('-1 day');
        $year         = $request->query->filter('year', $today->format('Y'), FILTER_SANITIZE_STRING);
        $month        = $request->query->filter('month', $today->format('m'), FILTER_SANITIZE_STRING);
        $day          = $request->query->filter('day', $today->format('d'), FILTER_SANITIZE_STRING);
        $categoryName = $request->query->filter('category_name', 'home', FILTER_SANITIZE_STRING);
        $path         = "{$year}/{$month}/{$day}";
        $file         = MEDIA_PATH . "/library/{$path}/{$categoryName}.html";
        $url          = "/archive/content/{$path}/";

        if (file_exists($file) && is_readable($file)) {
            $html = file_get_contents($file);
        } else {
            return new RedirectResponse($url, 301);
        }

        if (empty($html)) {
            return new RedirectResponse($url, 301);
        }

        return new Response($html, 200, [
            'x-tags' => "archive-digital,{$categoryName},{$year}-{$month}-{$day}"
        ]);
    }

    /**
     * Returns the advertisements for the archive template
     *
     * @return array the list of advertisement objects
     */
    public function getAds()
    {
        $category = 0;

        // Get letter positions
        $positionManager = $this->get('core.helper.advertisement');
        $positions       = $positionManager->getPositionsForGroup('article_inner', [ 7, 9 ]);
        $advertisements  = $this->get('advertisement_repository')
            ->findByPositionsAndCategory($positions, $category);

        return [ $positions, $advertisements ];
    }
}
