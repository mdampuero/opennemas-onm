<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Api\Controller\V1\Frontend;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Common\Core\Controller\Controller;

/**
 * Lists and displays advertisements.
 */
class ArticleController extends Controller
{
    /**
     * List articles for requested tag.
     *
     * @return JsonResponse The list of articles.
     */
    public function showArticlesByTagAction(Request $request, $tagName)
    {
        $tagName = $this->get('data.manager.filter')
            ->set($request->query->filter('tagName', '', FILTER_SANITIZE_STRING))
            ->filter('tags', [ 'exclude' => [ '.', '-', '#' ] ])
            ->get();
        $page    = $request->query->getDigits('page', 1);

        $tag = preg_replace('/[^a-z0-9]/', '_', $tagName);
        $epp = $this->get('setting_repository')->get('items_in_blog', 10);
        $epp = (is_null($epp) || $epp <= 0) ? 10 : $epp;

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

        $results     = [];
        $articlesIds = [];
        $photoIds    = [];
        $albumIds    = [];
        $tag         = strtolower($tag);
        foreach ($contents as &$item) {
            $arrayMetadatas = explode(',', $item->metadata);

            foreach ($arrayMetadatas as &$word) {
                $word = strtolower(trim($word));
                $word = \Onm\StringUtils::normalize($word);
                $word = preg_replace('/[^a-z0-9]/', '_', $word);
            }

            if (in_array($tag, $arrayMetadatas)) {
                if (isset($item->img1) && ($item->img1 > 0)) {
                    $photoIds[] = $item->img1;
                }

                if (isset($item->params)
                    && array_key_exists('withGallery', $item->params)
                    && ($item->params['withGallery'] !== '')
                ) {
                    $albumIds[] = $item->params['withGallery'];
                }

                if (isset($item->params)
                    && array_key_exists('withGalleryHome', $item->params)
                    && ($item->params['withGalleryHome'] !== '')
                ) {
                    $albumIds[] = $item->params['withGalleryHome'];
                }

                if (isset($item->params)
                    && array_key_exists('withGalleryInt', $item->params)
                    && ($item->params['withGalleryInt'] !== '')
                ) {
                    $albumIds[] = $item->params['withGalleryInt'];
                }

                // Add item to final array
                $results[]     = $item;
                $articlesIds[] = $item->pk_article;
            }
        }

        $extra    = ['photo' => [], 'album' => []];
        $contents = [];
        if (count($photoIds) > 0) {
            $photoIds = array_unique($photoIds);
            foreach ($photoIds as $photoId) {
                $contents[] = ['photo', $photoId];
            }
        }

        if (count($albumIds) > 0) {
            $albumIds = array_unique($albumIds);
            foreach ($albumIds as $albumId) {
                $contents[] = ['album', $albumId];
            }
        }

        $contents = $em->findMulti($contents);

        foreach ($contents as $content) {
            if ($content->content_type_name === 'photo') {
                $extra['photo'][$content->pk_photo] = $content;
                continue;
            }
            $extra['album'][$content->pk_album] = $content;
        }

        $results = \Onm\StringUtils::convertToUtf8($results);

        $contentsId = array_merge($articlesIds, $photoIds, $albumIds);
        $instance   = $this->get('core.instance');

        $headers = [
            'x-cache-for'  => '1d',
            'x-cacheable'  => true,
            'x-instance'   => $instance->internal_name,
            'x-tags'       => $this->getItemTags($contentsId, $tagName, $instance),
        ];

        $response = [
            'elements_per_page' => $epp,
            'page'              => $page,
            'results'           => $results,
            'total'             => $total,
            'extra'             => $extra
        ];

        return new JsonResponse($response, 200, $headers);
    }


    /**
     * Returns the list of tags basing on an advertisement.
     *
     * @param Advertisement $advertisement The advertisement object.
     *
     * @return string The list of tags.
     */
    protected function getItemTags($contentsId, $tag, $instance)
    {
        $tags     = [
            'instance-' . $instance->internal_name,
            'tag-page',
            'tag-' . $tag
        ];

        return implode(',', array_merge($tags, $contentsId));
    }
}
