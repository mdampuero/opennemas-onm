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

use Common\Core\Controller\Controller;
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
            $fm = $this->get('data.manager.filter');
            $t  = $this->get('api.service.tag')
                ->getTagsAssociatedCertainContentsTypes([1]);

            $letters = range('a', 'z');

            foreach ($t as $tag) {
                if (is_numeric($tag->name[0])) {
                    $tags['#'][] = $tag;
                    continue;
                }

                $normalized = $fm->set($tag->name)->filter('normalize')->get();

                if (in_array($normalized[0], $letters)) {
                    $tags[$normalized[0]][] = $tag;
                    continue;
                }

                $tags['*'][] = $tag;
            }
            ksort($tags);
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
     */
    public function tagsAction(Request $request, $slug)
    {
        $page = $request->query->getDigits('page', 1);
        $page = $page > 1 ? 2 : 1;

        $slug = $this->get('data.manager.filter')
            ->set($slug)
            ->filter('slug')
            ->get();

        $cacheId = $this->view->getCacheId('frontpage', 'tag', $slug, $page);

        $this->view->setConfig('frontpages');

        if (empty($this->view->getCaching())
            || !$this->view->isCached('frontpage/tags.tpl', $cacheId)
        ) {
            $epp    = $this->get('orm.manager')
                ->getDataSet('Settings', 'instance')
                ->get('items_in_blog', 10);
            $epp    = empty($epp) ? 10 : $epp;
            $locale = $this->get('core.locale')->getRequestLocale();
            $tags   = $this->get('api.service.tag')
                ->getList(sprintf('language_id = "%s" and slug = "%s"', $locale, $slug));

            $contents = [];
            $total    = 1;

            if ($tags['total'] > 0) {
                $ids = array_map(function ($a) {
                    return $a->id;
                }, $tags['items']);

                $criteria = [
                    'fk_content_type' => [
                        [ 'value' => 1 ],
                        // [ 'value' => 4 ],
                        // [ 'value' => 7 ],
                        // [ 'value' => 9 ],
                        'union' => 'OR'
                    ],
                    'exists' => 'EXISTS(SELECT 1 FROM contents_tags' .
                        ' WHERE contents_tags.content_id = contents.pk_content AND' .
                        ' contents_tags.tag_id IN (' . implode(',', $ids) . '))',
                    'content_status'    => [ [ 'value' => 1 ] ],
                    'in_litter'         => [ [ 'value' => 0 ] ],
                    'starttime'         => [
                        'union' => 'OR',
                        [ 'value' => '0000-00-00 00:00:00' ],
                        [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                        [ 'value' => date('Y-m-d H:i:s'), 'operator' => '<=' ],
                    ],
                    'endtime'           => [
                        'union' => 'OR',
                        [ 'value' => '0000-00-00 00:00:00' ],
                        [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                        [ 'value' => date('Y-m-d H:i:s'), 'operator' => '>' ],
                    ]
                ];

                $em       = $this->get('entity_repository');
                $contents = $em->findBy($criteria, 'starttime DESC', $epp, $page);
                $total    = count($contents) + 1;
            }

            // TODO: review this piece of CRAP
            foreach ($contents as &$item) {
                if (isset($item->img1) && ($item->img1 > 0)) {
                    $image = $em->find('Photo', $item->img1);
                    if (is_object($image) && !is_null($image->id)) {
                        $item->img1_path = $image->path_file . $image->name;
                        $item->img1      = $image;
                    }
                } elseif ($item->fk_content_type == 7) {
                    $image           = $em->find('Photo', $item->cover_id);
                    $item->img1_path = $image->path_file . $image->name;
                    $item->img1      = $image;
                } elseif ($item->fk_content_type == 9) {
                    $item->obj_video = $item;
                    $item->summary   = $item->description;
                }

                if (isset($item->fk_video) && ($item->fk_video > 0)) {
                    $item->video = $em->find('Video', $item->fk_video2);
                }
            }

            $this->view->assign('contents', $contents);

            $pagination = $this->get('paginator')->get([
                'directional' => true,
                'epp'         => $epp,
                'maxLinks'    => 0,
                'page'        => $page,
                'total'       => $total,
                'route'       => [
                    'name'   => 'frontend_tag_frontpage',
                    'params' => [ 'slug' => $slug ]
                ]
            ]);

            $this->view->assign([ 'pagination' => $pagination ]);
        }

        list($positions, $advertisements) = $this->getInnerAds();

        return $this->render('frontpage/tags.tpl', [
            'ads_positions'  => $positions,
            'advertisements' => $advertisements,
            'cache_id'       => $cacheId,
            'tagName'        => (empty($tag)) ? $slug : $tag->name,
            'x-tags'         => 'tag-page,' . $slug,
        ]);
    }

    /**
     * Returns a list of advertisement positions and advertisements.
     *
     * @param string $category The category id.
     *
     * @return array A list of advertisement positions and advertisements.
     */
    public function getInnerAds($category = 'home')
    {
        $category = !isset($category) || ($category == 'home') ? 0 : $category;

        // Get article_inner and category_frontpage positions
        $positionManager = $this->get('core.helper.advertisement');
        $positions       = array_merge(
            $positionManager->getPositionsForGroup('category_frontpage'),
            $positionManager->getPositionsForGroup('article_inner', [ 7, 9 ])
        );

        $advertisements = getService('advertisement_repository')
            ->findByPositionsAndCategory($positions, $category);

        return [ $positions, $advertisements ];
    }
}
