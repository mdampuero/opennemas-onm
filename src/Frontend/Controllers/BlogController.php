<?php
/**
 * Contains the class Frontend\Controllers\BlogController
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
class BlogController extends Controller
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

        $this->view->setConfig('frontpages');

        $cacheId = "blog|$categoryName|$page";
        if (!$this->view->isCached('blog/blog.tpl', $cacheId)) {
            $cm = new \ContentManager();
            $categoryManager = $this->get('category_repository');
            $category = $categoryManager->findBy(array('name' => $categoryName));

            if (empty($category)) {
                throw new \Symfony\Component\Routing\Exception\ResourceNotFoundException();
            }
            $category = $category[0];

            $itemsPerPage = 8;

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

            $pagination = \Onm\Pager\SimplePager::getPagerUrl(
                array(
                    'page'  => $page,
                    'items' => $itemsPerPage,
                    'total' => $countArticles,
                    'url'   => $this->generateUrl(
                        'blog_category',
                        array(
                            'category_name' => $categoryName,
                        )
                    )
                )
            );

            $this->view->assign(
                array(
                    'articles'   => $articles,
                    'category'   => $category,
                    'pagination' => $pagination,
                )
            );
        }

        $this->getInnerAds($category->id);

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

        $cacheId = "sync|blog|$categoryName|$page";
        if (!$this->view->isCached('blog/blog.tpl', $cacheId)) {
            $cm = new \ContentManager();

            // Get category object
            $category = unserialize(
                $cm->getUrlContent(
                    $wsUrl.'/ws/categories/object/'.$categoryName,
                    true
                )
            );

            // Get all contents for this frontpage
            list($pagination, $articles) = unserialize(
                $cm->getUrlContent(
                    $wsUrl.'/ws/frontpages/allcontentblog/'.$categoryName.'/'.$page,
                    true
                )
            );

            $this->view->assign(
                array(
                    'articles'   => $articles,
                    'category'   => $category,
                    'pagination' => $pagination,
                )
            );
        }

        $this->getInnerAds($category->id);

        return $this->render(
            'blog/blog.tpl',
            array(
                'cache_id' => $cacheId
            )
        );
    }
    /**
     * Description of the action
     *
     * @return Response the response object
     **/
    public function tagsAction(Request $request)
    {
        $tagName = $request->query->filter('tag_name', '', FILTER_SANITIZE_STRING);
        $page    = $request->query->getDigits('page', 1);

        $cacheId = "tag|$tagName|$page";
        if (!$this->view->isCached('blog/tag.tpl', $cacheId)) {
            $tagName = $GLOBALS['aplication']->conn->qstr($tagName);
            $tagSearchSQL = "AND metadata LIKE '%$tagName%'";

            $itemsPerPage = s::get('items_per_page');

            $cm      = new \ContentManager();
            list($countArticles, $articles)= $cm->getCountAndSlice(
                'Article',
                null,
                'in_litter != 1 AND contents.available=1 '.$tagSearchSQL,
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

                // Load attached and related contents from array
                $content->loadFrontpageImageFromHydratedArray($imageList)
                        ->loadAttachedVideo()
                        ->loadRelatedContents($categoryName);
            }

            $pagination = \Onm\Pager\SimplePager::getPagerUrl(
                array(
                    'page'  => $page,
                    'items' => $itemsPerPage,
                    'total' => $countArticles,
                    'url'   => $this->generateUrl(
                        'blog_category',
                        array(
                            'category_name' => $categoryName,
                        )
                    )
                )
            );

            $this->view->assign(
                array(
                    'articles'   => $articles,
                    'category'   => $category,
                    'pagination' => $pagination,
                )
            );

            $this->getInnerAds();
        }

        return $this->render(
            'blog/tag.tpl',
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

        $positions = array(101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 191, 192, 193);

        $advertisement = \Advertisement::getInstance();
        $banners = $advertisement->getAdvertisements($positions, $category);

        if (count($banners<=0)) {
            $cm = new \ContentManager();
            $banners = $cm->getInTime($banners);
            //$advertisement->renderMultiple($banners, &$tpl);
            $advertisement->renderMultiple($banners, $advertisement);
        }
        // Get intersticial banner,1,2,9,10
        $intersticial = $advertisement->getIntersticial(150, $category);
        if (!empty($intersticial)) {
            $advertisement->renderMultiple(array($intersticial), $advertisement);
        }
    }
}
