<?php
/**
 * Contains the class Frontend\Controllers\TagsController
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
class TagsController extends Controller
{

    /**
     * Description of the action
     *
     * @return Response the response object
     **/
    public function tagsAction(Request $request)
    {
        $this->view = new \Template(TEMPLATE_USER);

        $tagName = $request->query->filter('tag_name', '', FILTER_SANITIZE_STRING);
        $page    = $request->query->getDigits('page', 1);

        $cacheId = "tag|$tagName|$page";
        if (!$this->view->isCached('blog/tag.tpl', $cacheId)) {
            $tagSearch = $GLOBALS['application']->conn->qstr("%{$tagName}%");

            $itemsPerPage = s::get('items_in_blog');
            if (empty($itemsPerPage )) {
                $itemsPerPage = 8;
            }

            $cm      = new \ContentManager();
            list($countArticles, $articles)= $cm->getCountAndSlice(
                'Article',
                null,
                "in_litter != 1 AND contents.available=1 AND metadata LIKE {$tagSearch}",
                ' ORDER BY created DESC, available ASC',
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
                        ->loadRelatedContents($tagName);
            }

            $total = count($articles)+1;

            $pagination = \Onm\Pager\SimplePager::getPagerUrl(
                array(
                    'page'  => $page,
                    'items' => $itemsPerPage,
                    'total' => $total,
                    'url'   => $this->generateUrl(
                        'tag_frontpage',
                        array(
                            'tag_name' => $tagName,
                        )
                    )
                )
            );

            $this->view->assign(
                array(
                    'contents'   => $articles,
                    'tagName'   => $tagName,
                    'pagination' => $pagination,
                )
            );

            $ads = $this->getInnerAds();
            $this->view->assign('advertisements', $ads);
        }

        return $this->render(
            'frontpage/tags.tpl',
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
