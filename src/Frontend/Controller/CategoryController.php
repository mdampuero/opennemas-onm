<?php
/**
 * Contains the class Frontend\Controller\CategoryController
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

use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;

/**
 * Shows a paginated page for contents that share a property
 *
 * @package Backend_Controllers
 **/
class CategoryController extends Controller
{
    /**
     * Description of the action
     *
     * @return Response the response object
     * @throws \Symfony\Component\Routing\Exception\ResourceNotFoundException if the category is not available
     **/
    public function categoryAction(Request $request)
    {
        $categoryName = $request->query->filter('category_name', '', FILTER_SANITIZE_STRING);
        $page         = $request->query->getDigits('page', 1);

        $this->view = new \Template(TEMPLATE_USER);
        $this->view->setConfig('frontpages');

        $categoryManager = $this->get('category_repository');
        $category = $categoryManager->findOneBy(
            [ 'name' => [ [ 'value' => $categoryName ] ] ],
            [ 'name' => 'ASC' ]
        );

        if (empty($category)) {
            throw new \Symfony\Component\Routing\Exception\ResourceNotFoundException();
        }

        $cacheId = "category|$categoryName|$page";
        if (!$this->view->isCached('blog/blog.tpl', $cacheId)) {
            $sm = $this->get('setting_repository');
            $itemsPerPage = $sm->get('items_in_blog', 8);

            $em = $this->get('entity_repository');
            $filters = [
                'category_name'     => [ [ 'value' => $category->name ] ],
                'content_status'    => [ [ 'value' => 1 ] ],
                'fk_content_type'   => [ [ 'value' => [1,7,9], 'operator' => 'IN' ] ],
                'in_litter'         => [ [ 'value' => 0 ] ],
                'starttime'         => [
                    'union' => 'OR',
                    [ 'value' => '0000-00-00 00:00:00' ],
                    [ 'value' => date('Y-m-d H:i:s'), 'operator' => '<=' ],
                ]
            ];

            $order = [ 'starttime' => 'DESC' ];

            $articles = $em->findBy($filters, $order, $itemsPerPage, $page);

            $imageIdsList = [];
            foreach ($articles as &$content) {
                if (isset($content->img1) && !empty($content->img1)) {
                    $imageIdsList []= $content->img1;
                } elseif (!empty($content->fk_video)) {
                    $content->video = $em->find('Video', $content->fk_video);
                }
            }

            // Fetch images
            $imageIdsList = array_unique($imageIdsList);
            if (count($imageIdsList) > 0) {
                $imageList = $em->findBy(
                    [
                        'content_type_name' => [ [ 'value' => 'photo' ] ],
                        'pk_content'        => [ [ 'value' => $imageIdsList, 'operator' => 'IN' ] ]
                    ]
                );
            } else {
                $imageList = [];
            }

            // Overloading information for contents
            foreach ($articles as &$content) {
                // Load category related information
                $content->category_name  = $content->loadCategoryName($content->id);
                $content->category_title = $content->loadCategoryTitle($content->id);
                $content->author         = $this->get('user_repository')->find($content->fk_author);

                // Get number comments for a content
                if ($content->with_comment == 1) {
                    $content->num_comments = $content->getProperty('num_comments');
                }

                // Load attached and related contents from array
                $content->loadFrontpageImageFromHydratedArray($imageList)
                        ->loadAttachedVideo()
                        ->loadRelatedContents($categoryName);
            }

            $total = count($articles)+1;

            $pagination = $this->get('paginator')->get([
                'directional' => true,
                'epp'         => $itemsPerPage,
                'maxLinks'    => 0,
                'page'        => $page,
                'total'       => $total,
                'route'       => [
                    'name'   => 'category_frontpage',
                    'params' => [ 'category_name' => $categoryName ]
                ]
            ]);

            # Only allow user to see 2 pages
            if ($page > 1) {
                $pagination = null;
            }

            $this->view->assign(
                [
                    'articles'              => $articles,
                    'category'              => $category,
                    'pagination'            => $pagination,
                    'actual_category_title' => $category->title
                ]
            );
        }

        return $this->render(
            'blog/blog.tpl',
            [
                'cache_id'        => $cacheId,
                'actual_category' => $categoryName,
                'advertisements'  => $this->getInnerAds($category->id),
                'x-tags'          => 'category-frontpage,'.$categoryName.','.$page
            ]
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

        $this->view = new \Template(TEMPLATE_USER);
        $this->view->setConfig('frontpages');

        // Get sync params
        $wsUrl = '';
        $syncParams = $this->get('setting_repository')->get('sync_params');
        if ($syncParams) {
            foreach ($syncParams as $siteUrl => $values) {
                if (in_array($categoryName, $values['categories'])) {
                    $wsUrl = $siteUrl;
                }
            }
        }

        if (empty($wsUrl)) {
            throw new \Symfony\Component\Routing\Exception\ResourceNotFoundException();
        }

        $ccm = \ContentCategoryManager::get_instance();
        $cm = new \ContentManager();
        $cacheId = "sync|category|$categoryName|$page";
        if (!$this->view->isCached('blog/blog.tpl', $cacheId)) {
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
                    'actual_category_title' => $ccm->getTitle($categoryName),
                    'actual_category'       => $categoryName
                )
            );
        }

        //$this->getInnerAds();
        $wsActualCategoryId = $cm->getUrlContent($wsUrl.'/ws/categories/id/'.$categoryName);
        $ads = unserialize($cm->getUrlContent($wsUrl.'/ws/ads/article/'.$wsActualCategoryId, true));

        return $this->render(
            'blog/blog.tpl',
            array(
                'cache_id'       => $cacheId,
                'advertisements' => $ads,
                'x-tags'         => 'ext-category,'.$categoryName.','.$page
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

        // Get article_inner positions
        $positionManager = getService('instance_manager')->current_instance->theme->getAdsPositionManager();
        $positions = $positionManager->getAdsPositionsForGroup('article_inner', array(7, 9));

        return \Advertisement::findForPositionIdsAndCategory($positions, $category);
    }
}
