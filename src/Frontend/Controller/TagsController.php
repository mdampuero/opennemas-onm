<?php
/**
 * Contains the class Frontend\Controller\TagsController
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
        // Load config
        $this->view->setConfig('frontpages');

        $tagName = strip_tags($request->query->filter('tag_name', '', FILTER_SANITIZE_STRING));
        $page    = $request->query->getDigits('page', 1);

        $cacheId = "tag|$tagName|$page";
        if (!$this->view->isCached('blog/tag.tpl', $cacheId)) {
            $tag = preg_replace('/[^a-z0-9]/', '_', $tagName);
            $tagSearch = $GLOBALS['application']->conn->qstr("%{$tag}%");
            $itemsPerPage = s::get('items_in_blog');
            if (empty($itemsPerPage)) {
                $itemsPerPage = 8;
            }

            $searchCriteria =  " metadata LIKE {$tagSearch}  AND fk_content_type IN (1, 4, 7, 9) "
                ." AND available=1 AND in_litter=0";

            $er = $this->get('entity_repository');
            $contents = $er->findBy($searchCriteria, 'starttime DESC');

            $filteredContents = array();
            $tag = strtolower($tag);
            foreach ($contents as &$item) {
                $arrayMetadatas = explode(',', $item->metadata);

                foreach ($arrayMetadatas as &$word) {
                    $word = strtolower(trim($word));
                    $word = \StringUtils::normalize($word);
                }

                if (in_array($tag, $arrayMetadatas)) {
                    $item = $item->get($item->id);
                    if (isset($item->img1) && ($item->img1 > 0)) {
                        $image = new \Photo($item->img1);
                        $item->img1_path = $image->path_file.$image->name;
                        $item->img1 = $image;
                    }

                    if ($item->fk_content_type == 7) {
                        $image = new \Photo($item->cover_id);
                        $item->img1_path = $image->path_file.$image->name;
                        $item->img1 = $image;
                        $item->summary = $item->subtitle;
                        $item->subtitle= '';
                    }

                    if ($item->fk_content_type == 9) {
                        $item->obj_video = $item;
                        $item->summary = $item->description;
                    }

                    if (isset($item->fk_video) && ($item->fk_video > 0)) {
                        $item->video = new \Video($item->fk_video2);
                    }

                    // Add item to final array
                    $filteredContents[] = $item;
                }
            }

            $totalContents = count($filteredContents);
            $filteredContents = array_slice($filteredContents, ($page-1)*$itemsPerPage, $itemsPerPage);

            $pagination = \Onm\Pager\SimplePager::getPagerUrl(
                array(
                    'page'  => $page,
                    'items' => $itemsPerPage,
                    'total' => $totalContents,
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
                    'contents'   => $filteredContents,
                    'tagName'    => $tagName,
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
