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

use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * Displays frontpages.
 */
class FrontpagesController extends Controller
{
    /**
     * Shows the frontpage.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     *
     * @throws ResourceNotFoundException If the frontpage doesn't exist.
     */
    public function showAction(Request $request)
    {
        $categoryName  = $request->query->filter('category', 'home', FILTER_SANITIZE_STRING);
        $page          = $request->query->get('page', 1);
        $categoryId    = 0;
        $categoryTitle = 0;
        $category      = null;

        if ($categoryName !== 'home') {
            $category = $this->get('category_repository')->findOneBy([
                'name' => [ [ 'value' => $categoryName ] ]
            ]);

            if (!empty($category)) {
                $categoryId    = $category->id;
                $categoryTitle = $category->title;
            }
        }

        $fvs = $this->get('api.service.frontpage_version');

        list($contentPositions, $contents, $invalidationDt, $lastSaved) =
            $fvs->getPublicFrontpageData($categoryId);

        // Setup templating cache layer
        $this->view->setConfig('frontpages');

        $systemDate = new \DateTime(null, new \DateTimeZone('UTC'));
        $lifetime   = $invalidationDt->getTimestamp() - $systemDate->getTimestamp();

        if (!empty($invalidationDt)) {
            if ($lifetime < $this->view->getCacheLifetime()) {
                $this->view->setCacheLifetime($lifetime);
            }
        }

        $cacheId = $this->view->getCacheId('frontpage', $categoryName, $lastSaved, $page);

        if ($this->view->getCaching() === 0
            || !$this->view->isCached('frontpage/frontpage.tpl', $cacheId)
        ) {
            // If no home category name
            if ($categoryName !== 'home' && (empty($category))) {
                throw new ResourceNotFoundException();
            }

            $this->view->assign([
                'actual_category_id'    => $categoryId,
                'actual_category_title' => $categoryTitle,
                'category_data'         => $category,
                'time'                  => $systemDate->getTimestamp()
            ]);

            $ids        = array_keys($contents);
            $relatedIds = [];

            // Get photo and video ids
            foreach ($contents as $content) {
                if (isset($content->img1) && !empty($content->img1)) {
                    $relatedIds[] = $content->img1;
                }

                if (isset($content->fk_video) && !empty($content->fk_video)) {
                    $relatedIds[] = $content->fk_video;
                }
            }

            // Get related content ids
            $relatedMap = $this->get('related_contents')
                ->getRelatedContents($ids, $categoryId);

            foreach ($relatedMap as $id => $ids) {
                $relatedIds = array_merge($relatedIds, $ids);
            }

            $relatedIds = array_unique($relatedIds);
            $date       = date('Y-m-d H:i:s');

            if (!empty($relatedIds)) {
                $data = $this->get('entity_repository')->findBy([
                    'pk_content' => [ [ 'value' => $relatedIds, 'operator' => 'in' ] ],
                    'starttime' => [
                        'union' => 'OR',
                        [ 'value' => null, 'operator' => 'is', 'field' => true ],
                        [ 'value' => '0000-00-00 00:00:00' ],
                        [ 'value' => $date, 'operator' => '<=' ],
                    ],
                    'endtime' => [
                        'union' => 'OR',
                        [ 'value' => null, 'operator' => 'is', 'field' => true ],
                        [ 'value' => '0000-00-00 00:00:00' ],
                        [ 'value' => $date, 'operator' => '>' ],
                    ]
                ]);

                $related = [];
                foreach ($data as $content) {
                    $related[(string) $content->pk_content] = $content;
                }
            }

            // Overloading information for contents
            $tagsIds = [];
            foreach ($contents as &$content) {
                $tagsIds = array_merge($content->tag_ids, $tagsIds);
                if (isset($content->img1) && !empty($content->img1)
                    && !is_object($content->img1)
                    && array_key_exists($content->img1, $related)
                ) {
                    $content->img1      = $related[$content->img1];
                    $content->img1_path = $content->img1->path_file
                        . $content->img1->name;
                }

                if (isset($content->fk_video) && !empty($content->fk_video)
                    && array_key_exists($content->fk_video, $related)
                ) {
                    $content->obj_video = $related[$content->fk_video];
                }

                if (array_key_exists($content->pk_content, $relatedMap)) {
                    $content->related_contents = [];

                    $keys = $relatedMap[$content->pk_content];

                    foreach ($keys as $key) {
                        if (array_key_exists($key, $related)) {
                            $content->related_contents[] = $related[$key];
                        }
                    }
                }
            }

            $layout = $this->get('setting_repository')->get('frontpage_layout_' . $categoryId, 'default');
            if (empty($layout)) {
                $layout = 'default';
            }

            $layoutFile = 'layouts/' . $layout . '.tpl';

            $this->view->assign('column', $contents);
            $this->view->assign('layoutFile', $layoutFile);
            $this->view->assign('contentPositionByPos', $contentPositions);
            $this->view->assign(
                'tags',
                $this->get('api.service.tag')
                    ->getListByIdsKeyMapped(array_unique($tagsIds))['items']
            );
        }

        list($adsPositions, $advertisements) = $this->getAds($categoryId, $contents);

        $invalidationDt->setTimeZone($this->get('core.locale')->getTimeZone());

        return $this->render('frontpage/frontpage.tpl', [
            'advertisements'  => $advertisements,
            'ads_positions'   => $adsPositions,
            'cache_id'        => $cacheId,
            'category_name'   => $categoryName,
            'actual_category' => $categoryName,
            'page'            => $page,
            'x-tags'          => 'frontpage-page,' . $categoryName,
            'x-cache-for'     => $invalidationDt->format('Y-m-d H:i:s')
        ]);
    }

    /**
     * Displays an external frontpage.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function extShowAction(Request $request)
    {
        // Fetch HTTP variables
        $categoryName = $request->query->filter('category', 'home', FILTER_SANITIZE_STRING);

        // Get sync params
        $wsUrl = $this->get('core.helper.instance_sync')->getSyncUrl($categoryName);
        if (empty($wsUrl)) {
            throw new ResourceNotFoundException();
        }

        // Get category id correspondence
        $cm                 = new \ContentManager;
        $wsActualCategoryId = $cm->getUrlContent($wsUrl . '/ws/categories/id/' . $categoryName);

        // Setup templating cache layer
        $this->view->setConfig('frontpages');
        $cacheID = $this->view->getCacheId('sync', 'frontpage', $categoryName);

        if ($this->view->getCaching() === 0
            || !$this->view->isCached('frontpage/frontpage.tpl', $cacheID)
        ) {
            $ccm = \ContentCategoryManager::get_instance();

            // Check if category exists
            $existsCategory = $cm->getUrlContent($wsUrl . '/ws/categories/exist/' . $categoryName);

            // If no home category name
            if ($categoryName != 'home') {
                // Redirect to home page if the desired category doesn't exist
                if (empty($categoryName) || !$existsCategory) {
                    throw new ResourceNotFoundException();
                }
            }

            $actualCategory = (empty($subcategory_name)) ? $categoryName : $subcategory_name;
            $this->view->assign([
                'category_name'         => $categoryName,
                'actual_category'       => $actualCategory,
                'actual_category_id'    => $wsActualCategoryId,
                'actual_category_title' => $ccm->getTitle($categoryName),
            ]);

            // Get all contents for this frontpage
            $allContentsInHomepage = $cm->getUrlContent(
                $wsUrl . '/ws/frontpages/allcontent/' . $categoryName,
                true
            );

            $this->view->assign('column', unserialize(utf8_decode(htmlspecialchars_decode($allContentsInHomepage))));

            // Fetch layout for categories
            $layout = $cm->getUrlContent($wsUrl . '/ws/categories/layout/' . $categoryName, true);
            if (!$layout) {
                $layout = 'default';
            }

            $layoutFile = 'layouts/' . $layout . '.tpl';

            $this->view->assign('layoutFile', $layoutFile);
        }

        $ads = unserialize($cm->getUrlContent($wsUrl . '/ws/ads/frontpage/' . $wsActualCategoryId, true));

        return $this->render('frontpage/frontpage.tpl', [
            'advertisements' => $ads,
            'cache_id'       => $cacheID,
            'x-tags'         => 'frontpage-page,frontpage-page-external,' . $categoryName,
            'x-cache-for'    => '+3 hour',
        ]);
    }

    /**
     * Gets advertisements for the frontpage.
     *
     * @param string $category The category name.
     * @param array  $contents The list of contents that are in the frontpage.
     *
     * @return array The list of advertisement objects.
     *
     * TODO: Make this function non-static
     */
    public static function getAds($category, $contents)
    {
        $category = (!isset($category) || ($category == 'home')) ? 0 : $category;

        // TODO: Use $this->get when the function changes to non-static
        $positions        = getService('core.helper.advertisement')
            ->getPositionsForGroup('frontpage');
        $positionsToFetch = $positions;

        // We have to remove the floating ads from the positions because
        // we will add them later from the $contents array
        unset($positionsToFetch[array_search(37, $positionsToFetch)]);

        $advertisements = getService('advertisement_repository')
            ->findByPositionsAndCategory($positionsToFetch, $category);

        // Get all the ads and add them to the advertisements list
        if (is_array($contents)) {
            foreach ($contents as $content) {
                if ($content->content_type_name == 'advertisement'
                    && $content->content_status == 1
                ) {
                    $advertisements[] = $content;
                }
            }
        }

        return [ $positions, $advertisements ];
    }
}
