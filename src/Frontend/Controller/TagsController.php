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

use Common\Core\Annotation\BotDetector;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;
use Onm\Settings as s;

/**
 * Shows a paginated page for contents that share a property
 *
 * @package Backend_Controllers
 **/
class TagsController extends Controller
{
    /**
     * Shows a paginated list of contents for a given tag name
     *
     * @return Response the response object
     *
     * @BotDetector(bot="bingbot", route="frontend_frontpage")
     */
    public function tagsAction(Request $request)
    {
        $tagName = strip_tags($request->query->filter('tag_name', '', FILTER_SANITIZE_STRING));
        $tagName = \Onm\StringUtils::normalize($tagName);
        $page    =  $request->query->getDigits('page', 1);

        if ($page > 1) {
            $page = 2;
        }

        // Setup templating cache layer
        $this->view->setConfig('frontpages');
        $cacheId = $this->view->getCacheId('frontpage', 'tag', $tagName, $page);

        if ($this->view->getCaching() === 0
            || !$this->view->isCached('frontpage/tags.tpl', $cacheId)
        ) {
            $tag = preg_replace('/[^a-z0-9]/', '_', $tagName);
            $itemsPerPage = $this->get('setting_repository')->get('items_in_blog');
            if (empty($itemsPerPage)) {
                $itemsPerPage = 8;
            }

            $criteria = array(
                'content_status'  => [['value' => 1]],
                'in_litter'       => [['value' => 0]],
                'fk_content_type' => [
                    ['value' => 1],
                    // ['value' => 4],
                    // ['value' => 7],
                    // ['value' => 9],
                    'union' => 'OR'
                ],
                'metadata' => array(array('value' => '%' . $tag . '%', 'operator' => 'LIKE'))
            );

            $er = $this->get('entity_repository');
            $contents = $er->findBy($criteria, 'starttime DESC', $itemsPerPage, $page);
            $total = count($contents)+1;

            // TODO: review this piece of CRAP
            $filteredContents = array();
            $tag = strtolower($tag);
            foreach ($contents as &$item) {
                $arrayMetadatas = explode(',', $item->metadata);

                foreach ($arrayMetadatas as &$word) {
                    $word = strtolower(trim($word));
                    $word = \Onm\StringUtils::normalize($word);
                    $word = preg_replace('/[^a-z0-9]/', '_', $word);
                }

                if (in_array($tag, $arrayMetadatas)) {
                    $item = $item->get($item->id);
                    if (isset($item->img1) && ($item->img1 > 0)) {
                        $image = $er->find('Photo', $item->img1);
                        if (is_object($image) && !is_null($image->id)) {
                            $item->img1_path = $image->path_file.$image->name;
                            $item->img1      = $image;
                        }
                    }

                    if ($item->fk_content_type == 7) {
                        $image           = $er->find('Photo', $item->cover_id);
                        $item->img1_path = $image->path_file.$image->name;
                        $item->img1      = $image;
                        $item->summary   = $item->subtitle;
                        $item->subtitle  = '';
                    }

                    if ($item->fk_content_type == 9) {
                        $item->obj_video = $item;
                        $item->summary   = $item->description;
                    }

                    if (isset($item->fk_video) && ($item->fk_video > 0)) {
                        $item->video = $er->find('Video', $item->fk_video2);
                    }

                    // Add item to final array
                    $filteredContents[] = $item;
                }
            }

            $this->view->assign('contents', $filteredContents);

            $pagination = $this->get('paginator')->get([
                'directional' => true,
                'epp'         => $itemsPerPage,
                'maxLinks'    => 0,
                'page'        => $page,
                'total'       => $total+1,
                'route'       => [
                    'name'   => 'tag_frontpage',
                    'params' => [ 'tag_name' => $tagName ]
                ]
            ]);

            $this->view->assign([ 'pagination' => $pagination, ]);
        }

        list($positions, $advertisements) = $this->getInnerAds();

        return $this->render('frontpage/tags.tpl', [
            'ads_positions'  => $positions,
            'advertisements' => $advertisements,
            'cache_id'       => $cacheId,
            'tagName'        => $tagName,
            'x-tags'         => 'tag-page,' . $tagName,
        ]);
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
        $category       = (!isset($category) || ($category=='home'))? 0: $category;
        $positions      = [7, 9, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 191, 192, 193];
        $advertisements = getService('advertisement_repository')
            ->findByPositionsAndCategory($positions, $category);

        return [ $positions, $advertisements ];
    }
}
