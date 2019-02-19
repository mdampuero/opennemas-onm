<?php
/**
 * Contains the class Frontend\Controller\CategoryController
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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Common\Core\Controller\Controller;

/**
 * Shows a paginated page for contents that share a property
 *
 * @package Backend_Controllers
 */
class CategoryController extends Controller
{
    /**
     * Shows the latest contents in a category given its name and page number
     *
     * @param \Symfony\Component\HttpFoundation\Request the request object
     *
     * @return \Symfony\Component\HttpFoundation\Response the response object
     * @throws \Symfony\Component\Routing\Exception\ResourceNotFoundException if the category is not available
     */
    public function categoryAction(Request $request)
    {
        $categoryName = $request->get('category_name', '', FILTER_SANITIZE_STRING);
        $page         = (int) $request->get('page', 1);
        $epp          = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('items_in_blog', 10);
        $epp          = (is_null($epp) || $epp <= 0) ? 10 : $epp;

        if ($page > 1) {
            $page = 2;
        }

        $categoryManager = $this->get('category_repository');
        $category        = $categoryManager->findOneBy(
            [ 'name' => [ [ 'value' => $categoryName ] ] ],
            [ 'name' => 'ASC' ]
        );

        if (empty($category)) {
            throw new ResourceNotFoundException();
        }

        $em      = $this->get('entity_repository');
        $order   = [ 'starttime' => 'DESC' ];
        $filters = [
            'pk_fk_content_category' => [ [ 'value' => $category->pk_content_category ] ],
            'fk_content_type'        => [ [ 'value' => [1, 7, 9], 'operator' => 'IN' ] ],
            'content_status'         => [ [ 'value' => 1 ] ],
            'in_litter'              => [[ 'value' => 1, 'operator' => '!=' ]],
            'starttime'              => [
                'union' => 'OR',
                [ 'value' => '0000-00-00 00:00:00' ],
                [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                [ 'value' => date('Y-m-d H:i:s'), 'operator' => '<=' ],
            ],
            'endtime'                => [
                'union' => 'OR',
                [ 'value' => '0000-00-00 00:00:00' ],
                [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                [ 'value' => date('Y-m-d H:i:s'), 'operator' => '>' ],
            ]
        ];

        $articles = $em->findBy($filters, $order, $epp, $page);
        $total    = count($articles) + 1;

        $starttime = \ContentManager::getEarlierStarttimeOfScheduledContents($articles);
        $endtime   = \ContentManager::getEarlierEndtimeOfScheduledContents($articles);
        $expires   = $starttime;

        if (!empty($endtime) && (empty($expires) || $endtime < $starttime)) {
            $expires = $endtime;
        }

        if (!empty($expires)) {
            $lifetime = strtotime($expires) - time();

            if ($lifetime < $this->view->getCacheLifetime()) {
                $this->view->setCacheLifetime($lifetime);
            }
        }

        $cm       = new \ContentManager();
        $articles = $cm->getInTime($articles);

        // Setup templating cache layer
        $this->view->setConfig('frontpages');
        $cacheId = $this->view->getCacheId('frontpage', 'category', $categoryName, $page);

        if ($this->view->getCaching() === 0
            || !$this->view->isCached('blog/blog.tpl', $cacheId)
        ) {
            $imageIdsList = [];
            foreach ($articles as &$content) {
                if (isset($content->img1) && !empty($content->img1)) {
                    $imageIdsList[] = $content->img1;
                } elseif (!empty($content->fk_video)) {
                    $content->video = $em->find('Video', $content->fk_video);
                }
            }

            // Fetch images
            $imageIdsList = array_unique($imageIdsList);
            if (count($imageIdsList) > 0) {
                $imageList = $em->findBy([
                    'content_type_name' => [ [ 'value' => 'photo' ] ],
                    'pk_content'        => [ [ 'value' => $imageIdsList, 'operator' => 'IN' ] ]
                ]);
            } else {
                $imageList = [];
            }

            // Overloading information for contents
            foreach ($articles as &$content) {
                // Load category related information
                $content->author = $this->get('user_repository')->find($content->fk_author);

                // Load attached and related contents from array
                $content->loadFrontpageImageFromHydratedArray($imageList)
                    ->loadAttachedVideo()
                    ->loadRelatedContents($categoryName);
            }

            $pagination = $this->get('paginator')->get([
                'directional' => true,
                'epp'         => $epp,
                'maxLinks'    => 0,
                'page'        => $page,
                'total'       => $total + 1,
                'route'       => [
                    'name'   => 'category_frontpage',
                    'params' => [ 'category_name' => $categoryName ]
                ]
            ]);

            $this->view->assign([
                'articles'              => $articles,
                'category'              => $category,
                'time'                  => time(),
                'pagination'            => $pagination,
                'page'                  => $page,
                'actual_category_title' => $category->title
            ]);
        }

        list($positions, $advertisements) = $this->getInnerAds($category->id);

        return $this->render('blog/blog.tpl', [
            'actual_category' => $categoryName,
            'ads_positions'   => $positions,
            'advertisements'  => $advertisements,
            'cache_id'        => $cacheId,
            'category_name'   => $categoryName,
            'x-cache-for'     => $expires,
            'x-tags'          => 'category-frontpage,' . $categoryName . ',' . $page,
        ]);
    }

    /**
     * Action for synchronized blog frontpage
     *
     * @param \Symfony\Component\HttpFoundation\Request the request object
     *
     * @return \Symfony\Component\HttpFoundation\Response the response object
     */
    public function extCategoryAction(Request $request)
    {
        $categoryName = $request->query->filter('category_name', '', FILTER_SANITIZE_STRING);
        $page         = $request->query->getDigits('page', 1);

        $wsUrl = $this->get('core.helper.instance_sync')
            ->getSyncUrl($categoryName);

        if (empty($wsUrl)) {
            throw new ResourceNotFoundException();
        }

        $this->view->setConfig('frontpages');

        $cacheId = $this->view->getCacheId('sync', 'frontpage', 'category', $categoryName, $page);

        if ($this->view->getCaching() === 0
            || !$this->view->isCached('blog/blog.tpl', $cacheId)
        ) {
            $cm = new \ContentManager();

            // Get category object
            $category = unserialize(
                $cm->getUrlContent(
                    $wsUrl . '/ws/categories/object/' . $categoryName,
                    true
                )
            );

            // Get all contents for this frontpage
            list($pagination, $articles) = unserialize(
                utf8_decode(
                    $cm->getUrlContent(
                        $wsUrl . '/ws/frontpages/allcontentblog/' . $categoryName . '/' . $page,
                        true
                    )
                )
            );

            $this->view->assign([
                'articles'              => $articles,
                'category'              => $category,
                'pagination'            => $pagination,
                'actual_category_title' => $category->title,
                'actual_category'       => $categoryName
            ]);
        }

        list($positions, $advertisements) = $this->getInnerAds();

        return $this->render('blog/blog.tpl', [
            'ads_positions'  => $positions,
            'advertisements' => $advertisements,
            'cache_id'       => $cacheId,
            'x-cache-for'    => '+3 hour',
            'x-tags'         => 'ext-category,' . $categoryName . ',' . $page,
        ]);
    }

    /**
     * Fetches advertisements for article inner
     *
     * @param string category the category identifier
     *
     * @return array
     */
    public function getInnerAds($category = 'home')
    {
        $category = (!isset($category) || ($category == 'home')) ? 0 : $category;

        // Get article_inner and category_frontpage positions
        $positionManager = $this->get('core.helper.advertisement');
        $positions       = array_merge(
            $positionManager->getPositionsForGroup('category_frontpage'),
            $positionManager->getPositionsForGroup('article_inner', [ 7, 9 ])
        );

        $advertisements = $this->get('advertisement_repository')
            ->findByPositionsAndCategory($positions, $category);

        return [ $positions, $advertisements ];
    }
}
