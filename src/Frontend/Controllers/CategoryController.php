<?php
/**
 * Contains the class Frontend\Controllers\CategoryController
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
namespace Frontend\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Shows a paginated page for contents that share a property
 *
 * @package Backend_Controllers
 **/
class CategoryController extends Controller
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
     * Description of the action
     *
     * @return Response the response object
     **/
    public function categoryAction(Request $request)
    {
        $categoryName = $request->query->filter('category_name', '', FILTER_SANITIZE_STRING);
        $page         = $request->query->getDigits('page', 1);

        $categoryManager = $this->get('category_repository');
        $category = $categoryManager->findBy(array('name' => $categoryName));

        if (empty($category)) {
            throw new \Symfony\Component\Routing\Exception\ResourceNotFoundException();
        }
        $category = $category[0];

        $this->view->setConfig('frontpages');

        $cacheId = "category|$categoryName|$page";
        if (!$this->view->isCached('blog/blog.tpl', $cacheId)) {

            $itemsPerPage = s::get('items_in_blog');
            if (empty($itemsPerPage )) {
                $itemsPerPage = 8;
            }

            $cm      = new \ContentManager();
            list($countArticles, $articles)= $cm->getCountAndSlice(
                'Article',
                (int) $category->pk_content_category,
                'in_litter != 1 AND contents.available=1',
                'ORDER BY created DESC, available ASC',
                $page,
                $itemsPerPage
            );

            $imageIdsList = array();
            foreach ($articles as $content) {
                if (isset($content->img1)) {
                    $imageIdsList []= $content->img1;
                }
            }
            $imageIdsList = array_unique($imageIdsList);

            if (count($imageIdsList) > 0) {
                $imageList = $cm->find('Photo', 'pk_content IN ('. implode(',', $imageIdsList) .')');
            } else {
                $imageList = array();
            }

            // Overloading information for contents
            foreach ($articles as &$content) {

                // Load category related information
                $content->category_name  = $content->loadCategoryName($content->id);
                $content->category_title = $content->loadCategoryTitle($content->id);
                $content->author         = new \User($content->fk_author);

                // Load attached and related contents from array
                $content->loadFrontpageImageFromHydratedArray($imageList)
                        ->loadAttachedVideo()
                        ->loadRelatedContents($categoryName);
            }

            $total = count($articles)+1;

            $pagination = \Onm\Pager\SimplePager::getPagerUrl(
                array(
                    'page'  => $page,
                    'items' => $itemsPerPage,
                    'total' => $total,
                    'url'   => $this->generateUrl(
                        'category_frontpage',
                        array(
                            'category_name' => $categoryName,
                        )
                    )
                )
            );

            $this->view->assign(
                array(
                    'articles'              => $articles,
                    'category'              => $category,
                    'pagination'            => $pagination,
                    'actual_category_title' => $category->title,
                )
            );
        }

        $ads = $this->getInnerAds($category->id);
        $this->view->assign('advertisements', $ads);

        return $this->render(
            'blog/blog.tpl',
            array(
                'cache_id' => $cacheId
            )
        );
    }


    /**
     * Action for synchronized blog frontpage
     *
     * @return Response the response object
     **/
    public function extCategoryAction(Request $request)
    {
        $categoryName = $request->query->filter('category_name', '', FILTER_SANITIZE_STRING);
        $page         = $request->query->getDigits('page', 1);

        $this->view->setConfig('frontpages');

        // Get sync params
        $wsUrl = '';
        $syncParams = s::get('sync_params');
        foreach ($syncParams as $siteUrl => $categoriesToSync) {
            foreach ($categoriesToSync as $value) {
                if (preg_match('/'.$categoryName.'/i', $value)) {
                    $wsUrl = $siteUrl;
                }
            }
        }

        if (empty($wsUrl)) {
            throw new \Symfony\Component\Routing\Exception\ResourceNotFoundException();
        }

        $cm = new \ContentManager();
        $cacheId = "sync|category|$categoryName|$page";
        if (!$this->view->isCached('blog/blog.tpl', $cacheId)) {
            $ccm = \ContentCategoryManager::get_instance();
            // Get category object
            $category = unserialize(
                $cm->getUrlContent(
                    $wsUrl.'/ws/categories/object/'.$categoryName,
                    true
                )
            );

            // Get all contents for this frontpage
            list($pagination, $articles) = unserialize(
                utf8_decode(
                    $cm->getUrlContent(
                        $wsUrl.'/ws/frontpages/allcontentblog/'.$categoryName.'/'.$page,
                        true
                    )
                )
            );

            $this->view->assign(
                array(
                    'articles'              => $articles,
                    'category'              => $category,
                    'pagination'            => $pagination,
                    'actual_category_title' => $ccm->get_title($categoryName),
                )
            );
        }

        //$this->getInnerAds();
        $wsActualCategoryId = $cm->getUrlContent($wsUrl.'/ws/categories/id/'.$categoryName);
        $ads = unserialize($cm->getUrlContent($wsUrl.'/ws/ads/frontpage/'.$wsActualCategoryId, true));
        $this->view->assign('advertisements', $ads);

        return $this->render(
            'blog/blog.tpl',
            array(
                'cache_id' => $cacheId
            )
        );
    }

    /**
     * Fetches advertisements for article inner
     *
     * @param string category the category identifier
     *
     * @return void
     **/
    public static function getInnerAds($category = 'home')
    {
        $category = (!isset($category) || ($category=='home'))? 0: $category;

        $positions = array(7, 9, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 191, 192, 193);

        return \Advertisement::findForPositionIdsAndCategory($positions, $category);
    }
}
