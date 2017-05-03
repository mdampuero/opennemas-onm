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
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Common\Core\Controller\Controller;

/**
 * Shows a paginated page for contents that share a property
 *
 * @package Backend_Controllers
 **/
class CategoryController extends Controller
{
    /**
     * Shows the latest contents in a category given its name and page number
     *
     * @return Response the response object
     * @throws \Symfony\Component\Routing\Exception\ResourceNotFoundException if the category is not available
     **/
    public function categoryAction(Request $request)
    {
        $categoryName = $request->query->filter('category_name', '', FILTER_SANITIZE_STRING);
        $page         = $request->query->getDigits('page', 1);
        $itemsPerPage = $this->get('setting_repository')->get('items_in_blog', 8);

        if ($page > 1) {
            $page = 2;
        }

        $categoryManager = $this->get('category_repository');
        $category = $categoryManager->findOneBy(
            [ 'name' => [ [ 'value' => $categoryName ] ] ],
            [ 'name' => 'ASC' ]
        );

        if (empty($category)) {
            throw new ResourceNotFoundException();
        }

        $em = $this->get('entity_repository');
        $order = [ 'starttime' => 'DESC' ];
        $filters = [
            'category_name'     => [ [ 'value' => $category->name ] ],
            'content_status'    => [ [ 'value' => 1 ] ],
            'fk_content_type'   => [ [ 'value' => [1,7,9], 'operator' => 'IN' ] ],
            'in_litter'         => [ [ 'value' => 0 ] ],
        ];

        $articles = $em->findBy($filters, $order, $itemsPerPage, $page);
        $total = count($articles)+1;

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

        $cm = new \ContentManager();
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
                    $imageIdsList []= $content->img1;
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
                $content->category_name  = $content->loadCategoryName($content->id);
                $content->category_title = $content->loadCategoryTitle($content->id);
                $content->author         = $this->get('user_repository')->find($content->fk_author);

                // Get number comments for a content
                if ($content->with_comment == 1) {
                    $content->num_comments = $content->getMetadata('num_comments');
                }

                // Load attached and related contents from array
                $content->loadFrontpageImageFromHydratedArray($imageList)
                        ->loadAttachedVideo()
                        ->loadRelatedContents($categoryName);
            }

            $pagination = $this->get('paginator')->get([
                'directional' => true,
                'epp'         => $itemsPerPage,
                'maxLinks'    => 0,
                'page'        => $page,
                'total'       => $total+1,
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
            'x-tags'          => 'category-frontpage,'.$categoryName.','.$page,
        ]);
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

        // Get sync params
        $wsUrl = '';
        $syncParams = $this->get('setting_repository')->get('sync_params');
        if ($syncParams) {
            foreach ($syncParams as $siteUrl => $values) {
                if (is_array($values['categories']) && in_array($categoryName, $values['categories'])) {
                    $wsUrl = $siteUrl;
                }
            }
        }

        if (empty($wsUrl)) {
            throw new ResourceNotFoundException();
        }

        $cm  = new \ContentManager();

        // Setup templating cache layer
        $this->view->setConfig('frontpages');
        $cacheId = $this->view->getCacheId('sync', 'frontpage', 'category', $categoryName, $page);

        if ($this->view->getCaching() === 0
            || !$this->view->isCached('blog/blog.tpl', $cacheId)
        ) {
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

            $this->view->assign([
                'articles'              => $articles,
                'category'              => $category,
                'pagination'            => $pagination,
                'actual_category_title' => $ccm->getTitle($categoryName),
                'actual_category'       => $categoryName
            ]);
        }

        list($positions, $advertisements) = $this->getInnerAds();

        return $this->render('blog/blog.tpl', [
            'ads_positions'  => $positions,
            'advertisements' => $advertisements,
            'cache_id'       => $cacheId,
            'x-cache-for'    => '+3 hour',
            'x-tags'         => 'ext-category,'.$categoryName.','.$page,
        ]);
    }

    /**
     * Fetches advertisements for article inner
     *
     * @param string category the category identifier
     *
     * @return void
     **/
    public function getInnerAds($category = 'home')
    {
        $category = (!isset($category) || ($category=='home'))? 0: $category;

        // Get article_inner positions
        $positionManager = $this->get('core.manager.advertisement');
        $positions       = $positionManager->getPositionsForGroup('article_inner', [ 7, 9 ]);
        $advertisements  = \Advertisement::findForPositionIdsAndCategory($positions, $category);

        return [ $positions, $advertisements ];
    }
}
