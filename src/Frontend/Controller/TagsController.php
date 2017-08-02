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

use Common\Core\Annotation\BotDetector;
use Common\Core\Controller\Controller;
use Onm\Settings as s;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * Displays a list of tags or a list of contents by tag.
 */
class TagsController extends Controller
{
    /**
     * Displays a list of tags.
     *
     * @return Response The response object.
     *
     * @BotDetector(bot="bingbot", route="frontend_frontpage")
     */
    public function indexAction()
    {
        if (!$this->get('core.security')
            ->hasExtension('es.openhost.module.tagsIndex')
        ) {
            throw new ResourceNotFoundException();
        }

        $cacheId = $this->view->getCacheId('frontpage', 'tag', 'tag-index');
        $tags    = [ '#' => [], '*' => [] ];

        $this->view->setConfig('frontpages');

        if ($this->view->getCaching() === 0
            || !$this->view->isCached('tag/index.tpl', $cacheId)
        ) {
            $fm      = $this->get('data.manager.filter');
            $t       = $this->get('core.manager.tag')->findAll();
            $letters = range('a', 'z');

            foreach ($t as $tag) {
                if (is_numeric($tag[0])) {
                    $tags['#'][] = $tag;
                    continue;
                }

                $normalized = $fm->filter('normalize', $tag);

                if (in_array($normalized[0], $letters)) {
                    $tags[$normalized[0]][] = $tag;
                    continue;
                }

                $tags['*'][] = $tag;
            }
        }

        list($positions, $advertisements) = $this->getInnerAds();

        return $this->render('tag/index.tpl', [
            'tags'           => $tags,
            'ads_positions'  => $positions,
            'advertisements' => $advertisements,
            'cache_id'       => $cacheId,
            'x-tags'         => 'tag-page,tag-index',
        ]);
    }

    /**
     * Shows a paginated list of contents for a given tag name.
     *
     * @return Response The response object.
     *
     * @BotDetector(bot="bingbot", route="frontend_frontpage")
     */
    public function tagsAction(Request $request)
    {
        $tagName = strip_tags($request->query->filter('tag_name', '', FILTER_SANITIZE_STRING));
        $tagName = \Onm\StringUtils::normalize($tagName);
        $page    = $request->query->getDigits('page', 1);

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
            $epp = $this->get('setting_repository')->get('items_in_blog', 8);

            $criteria = [
                'content_status'  => [ [ 'value' => 1 ] ],
                'in_litter'       => [ [ 'value' => 0 ] ],
                'fk_content_type' => [
                    [ 'value' => 1 ],
                    // [ 'value' => 4 ],
                    // [ 'value' => 7 ],
                    // [ 'value' => 9 ],
                    'union' => 'OR'
                ],
                'metadata' => [
                    [ 'value' => '%' . $tag . '%', 'operator' => 'LIKE' ]
                ]
            ];

            $em       = $this->get('entity_repository');
            $contents = $em->findBy($criteria, 'starttime DESC', $epp, $page);
            $total    = count($contents) + 1;

            // TODO: review this piece of CRAP
            $filteredContents = [];
            $tag              = strtolower($tag);
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
                        $image = $em->find('Photo', $item->img1);
                        if (is_object($image) && !is_null($image->id)) {
                            $item->img1_path = $image->path_file . $image->name;
                            $item->img1      = $image;
                        }
                    }

                    if ($item->fk_content_type == 7) {
                        $image           = $em->find('Photo', $item->cover_id);
                        $item->img1_path = $image->path_file . $image->name;
                        $item->img1      = $image;
                        $item->summary   = $item->subtitle;
                        $item->subtitle  = '';
                    }

                    if ($item->fk_content_type == 9) {
                        $item->obj_video = $item;
                        $item->summary   = $item->description;
                    }

                    if (isset($item->fk_video) && ($item->fk_video > 0)) {
                        $item->video = $em->find('Video', $item->fk_video2);
                    }

                    // Add item to final array
                    $filteredContents[] = $item;
                }
            }

            $this->view->assign('contents', $filteredContents);

            $pagination = $this->get('paginator')->get([
                'directional' => true,
                'epp'         => $epp,
                'maxLinks'    => 0,
                'page'        => $page,
                'total'       => $total + 1,
                'route'       => [
                    'name'   => 'tag_frontpage',
                    'params' => [ 'tag_name' => $tagName ]
                ]
            ]);

            $this->view->assign([ 'pagination' => $pagination ]);
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
     * Returns a list of advertisement positions and advertisements.
     *
     * @param string $category The category id.
     *
     * @return array A list of advertisement positions and advertisements.
     */
    public static function getInnerAds($category = 'home')
    {
        $category       = !isset($category) || ($category == 'home') ? 0 : $category;
        $positions      = [ 7, 9, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 191, 192, 193 ];
        $advertisements = getService('advertisement_repository')
            ->findByPositionsAndCategory($positions, $category);

        return [ $positions, $advertisements ];
    }
}
