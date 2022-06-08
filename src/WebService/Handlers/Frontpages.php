<?php
/**
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WebService\Handlers;

use Luracast\Restler\RestException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * Handles REST actions for frontpages.
 *
 * @package WebService
 */
class Frontpages
{
    /*
     * @url GET /frontpages/allcontentblog/:category_slug/:page
     */
    public function allContentBlog($categoryName, $page = 1)
    {
        try {
            $category = getService('api.service.category')
                ->getItemBySlug($categoryName);
        } catch (\Exception $e) {
            throw new ResourceNotFoundException();
        }

        $epp = getService('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('items_per_page', 10);

        $order   = [ 'starttime' => 'DESC' ];
        $filters = [
            'join' => [
                [
                    'type'                => 'INNER',
                    'table'               => 'content_category',
                    'contents.pk_content' => [
                        [ 'value' => 'content_category.content_id', 'field' => true ]
                    ]
                ]
            ],
            'content_type_name' => [[ 'value' => 'article' ]],
            'content_status'    => [[ 'value' => 1 ]],
            'in_litter'         => [[ 'value' => 1, 'operator' => '!=' ]],
            'category_id'       => [ [ 'value' => $category->id ] ],
            'starttime'         => [
                'union' => 'OR',
                [ 'value'  => null, 'operator' => 'IS', 'field' => true ],
                [ 'value' => gmdate('Y-m-d H:i:s'), 'operator' => '<=' ],
            ]
        ];

        $er       = getService('entity_repository');
        $articles = $er->findBy($filters, $order, $epp, $page);
        $total    = $er->countBy($filters);

        $this->hydrateContents($articles);

        $related = $this->getRelated($articles);

        // Set pagination
        $pagination = getService('paginator')->get([
            'page'  => $page,
            'epp'   => $epp,
            'total' => $total,
            'route' => [
                'name'   => 'categ_sync_frontpage',
                'params' => [
                    'category_slug' => $categoryName,
                ]
            ]
        ]);

        return utf8_encode(serialize([ $pagination->links, $articles, $related ]));
    }

    /**
     * Adds special information to contents in order to work properly in the
     * target instance.
     *
     * @param array $contents The list of contents.
     *
     * @return array The list of contents with the special information.
     */
    protected function hydrateContents($contents)
    {
        foreach ($contents as &$content) {
            try {
                $author = getService('api.service.author')
                    ->getItem($content->fk_author);

                $content->agency = !empty($author) ? $author->name : $content->agency;

                // Prevent errors when trying to search authors in target
                $content->fk_author = null;
            } catch (\Exception $e) {
            }

             // Change uri for href links except widgets
            if ($content->content_type_name !== 'widget') {
                $content->external    = 1;
                $content->externalUri = getService('router')
                    ->generate('frontend_external_article_show', [
                        'category_slug' => getService('core.helper.category')->getCategorySlug($content),
                        'slug'          => $content->slug,
                        'article_id'    => date('YmdHis', strtotime($content->created)) .
                        sprintf('%06d', $content->pk_content),
                    ]);
            }
        }

        return $contents;
    }

    /**
     * Returns the list of contents related to the provided contents.
     *
     * @param array $contents The list of contents to search related contents
     *                        for.
     *
     * @return array The list of related contents.
     */
    protected function getRelated($contents)
    {
        $ids = [];
        foreach ($contents as $content) {
            if (!empty($content->fk_video) || !empty($content->img1)) {
                $ids[] = !empty($content->fk_video)
                    ? $content->fk_video
                    : $content->img1;
            }

            $ids = array_merge($ids, array_map(function ($a) {
                return $a['target_id'];
            }, array_filter($content->related_contents, function ($a) {
                return $a['type'] === 'related_frontpage';
            })));
        }

        if (empty($ids)) {
            return [];
        }

        $related = getService('entity_repository')->findBy([
            'pk_content' => [[ 'value' => $ids, 'operator' => 'IN' ]],
        ], [ 'starttime' => 'DESC' ]);

        $related = getService('data.manager.filter')
            ->set($related)
            ->filter('mapify', [ 'key' => 'pk_content' ])
            ->get($related);

        foreach ($related as $r) {
            $r->external    = 1;
            $r->externalUri = getService('core.helper.url_generator')
                ->generate($r, [ 'absolute' => true ]);
        }

        return $related;
    }
}
