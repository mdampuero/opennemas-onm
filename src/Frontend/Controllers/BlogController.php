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

        $cacheId = "blog|$categoryName|$page";
        if (!$this->view->isCached('blog/index.tpl', $cacheId)) {

            $cm = new \ContentManager();
            $categoryManager = $this->get('category_repository');
            $category = $categoryManager->findBy(array('name' => $categoryName));

            if (empty($category)) {
                throw new \Symfony\Component\Routing\Exception\ResourceNotFoundException();
            }
            $category = $category[0];

            $itemsPerPage = s::get('items_per_page');

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
            var_dump($pagination);die();


            $this->view->assign(
                array(
                    'articles'   => $articles,
                    'category'   => $category,
                    'pagination' => $pagination,
                )
            );
        }

        return $this->render(
            'blog/tag.tpl',
            array(
                'cache_id' => $cacheId
            )
        );
    }
}
