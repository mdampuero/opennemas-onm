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

use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Common\Core\Controller\Controller;

class ArchiveController extends Controller
{
    /**
     * Get news library from content table in database.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function archiveAction(Request $request)
    {
        $today = new \DateTime();
        $today->modify('-1 day');

        $year         = $request->query->filter('year', $today->format('Y'), FILTER_SANITIZE_STRING);
        $month        = $request->query->filter('month', $today->format('m'), FILTER_SANITIZE_STRING);
        $day          = $request->query->filter('day', $today->format('d'), FILTER_SANITIZE_STRING);
        $categorySlug = $request->query->filter('category_slug', null, FILTER_SANITIZE_STRING);
        $page         = $request->query->getDigits('page', 1);
        $date         = "{$year}-{$month}-{$day}";
        $itemsPerPage = 20;

        if (!empty($categorySlug)) {
            try {
                $category = $this->get('api.service.category')
                    ->getItemBySlug($categorySlug);
            } catch (\Exception $e) {
                throw new ResourceNotFoundException();
            }
        }

        // Setup templating cache layer
        $this->view->setConfig('newslibrary');
        $cacheID = $this->view->getCacheId('archive', $date, $page);

        if (($this->view->getCaching() === 0)
           || (!$this->view->isCached('archive/archive.tpl', $cacheID))
        ) {
            $er    = $this->get('entity_repository');
            $order = [ 'fk_content_type' => 'asc', 'starttime' => 'desc' ];

            $criteria = [];

            if (!empty($category)) {
                $criteria['category_id'] = [[ 'value' => $category->id ]];
            }

            $criteria = array_merge($criteria, [
                'in_litter'       => [[ 'value' => 0 ]],
                'content_status'  => [[ 'value' => 1 ]],
                'fk_content_type' => [[ 'value' => [1, 4, 7, 9], 'operator' => 'IN' ]],
                'starttime ' => [
                    [
                        'value' => "'$date' AND ('$date' + INTERVAL 1 DAY)",
                        'field' => true,
                        'operator' => 'BETWEEN'
                    ]
                ],
                'starttime'              => [
                    'union' => 'OR',
                    [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                    [ 'value' => date('Y-m-d H:i:s'), 'operator' => '<=' ],
                ],
                'endtime'                => [
                    'union' => 'OR',
                    [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                    [ 'value' => date('Y-m-d H:i:s'), 'operator' => '>' ],
                ]
            ]);

            $contents = $er->findBy($criteria, $order, $itemsPerPage, $page);
            $total    = $er->countBy($criteria);
            $library  = [];

            foreach ($contents as $content) {
                // Create category group
                if (!isset($library[$content->category_id])
                    && !empty($content->category_id)
                ) {
                    $library[$content->category_id] = $this
                        ->get('api.service.category')
                        ->getItem($content->category_id);

                    $library[$content->category_id]->contents = [];
                }

                // Add contents to category group
                $library[$content->category_id]->contents[] = $content;
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

        $this->getAds();

        return $this->render('archive/archive.tpl', [
            'cache_id'        => $cacheID,
            'newslibraryDate' => $date,
            'x-tags'          => 'archive-page,' . $date . ',' . $page . ',' . $categorySlug,
            'x-cacheable'     => true,
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
        $categoryName = $request->query->filter('category_slug', 'home', FILTER_SANITIZE_STRING);
        $path         = "{$year}/{$month}/{$day}";
        $html         = '';
        $url          = "/archive/content/{$path}/";

        $file = $this->getParameter('core.paths.public')
            . $this->get('core.instance')->getMediaShortPath()
            . "/library/$path/$categoryName.html";

        if (file_exists($file) && is_readable($file)) {
            $html = file_get_contents($file);
        } else {
            return new RedirectResponse($url, 301);
        }

        if (empty($html)) {
            return new RedirectResponse($url, 301);
        }

        return new Response($html, 200, [
            'x-tags'      => "archive-digital,{$categoryName},{$year}-{$month}-{$day}",
            'x-cacheable' => true,
        ]);
    }

    /**
     * Loads the list of positions and advertisements on renderer service.
     */
    public function getAds()
    {
        $positionManager = $this->get('core.helper.advertisement');
        $positions       = $positionManager->getPositionsForGroup('article_inner', [ 7, 9 ]);
        $advertisements  = $this->get('advertisement_repository')
            ->findByPositionsAndCategory($positions);

        $this->get('frontend.renderer.advertisement')
            ->setPositions($positions)
            ->setAdvertisements($advertisements);
    }
}
