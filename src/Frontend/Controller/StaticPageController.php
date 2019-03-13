<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Frontend\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Displays static pages.
 */
class StaticPageController extends FrontendController
{
    /**
     * {@inheritdoc}
     */
    protected $caches = [
        'show' => 'articles'
    ];

    /**
     * {@inheritdoc}
     */
    protected $groups = [
        'list' => 'article_inner'
    ];

    /**
     * {@inheritdoc}
     */
    protected $positions = [
        'list' => [ 1, 2, 5, 6, 7 ]
    ];
    /**
     * {@inheritdoc}
     */
    protected $routes = [
        'show' => 'frontend_staticpage'
    ];

    /**
     * {@inheritdoc}
     */
    protected $templates = [
        'show' => 'static_pages/statics.tpl'
    ];

    //     $contentAux       = new \Content();
    //     $contentAux->id   = $content->id;
    //     $auxTagIds        = $contentAux->getContentTags($content->id);
    //     $content->tag_ids = array_key_exists($content->id, $auxTagIds) ?
    //         $auxTagIds[$content->id] :
    //         [];

    //     return $this->render('static_pages/statics.tpl', [
    //         'ads_positions'      => $positions,
    //         'advertisements'     => $advertisements,
    //         'category_real_name' => $content->title,
    //         'content'            => $content,
    //         'content_id'         => $content->id,
    //         'page'               => $content,
    //         'cache_id'           => $cacheID,
    //         'o_content'          => $content,
    //         'x-tags'             => 'static-page,' . $content->id,
    //         'tags'               => $this->get('api.service.tag')
    //             ->getListByIdsKeyMapped($content->tag_ids)['items']
    //     ]);
    // }

    /**
     * {@inheritdoc}
     */
    protected function getItem(Request $request)
    {
        $item = $this->get('api.service.content')
            ->getItemBySlug($request->get('slug'));

        return $item;
    }

    /**
     * {@inheritdoc}
     */
    protected function getParameters($request, $item = null)
    {
        $params = parent::getParameters($request, $item);

        return array_merge($params, [
            'page' => $params['content'],
        ]);
    }
}
