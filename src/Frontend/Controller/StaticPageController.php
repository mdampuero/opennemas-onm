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
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

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
        'show' => 'article_inner'
    ];

    /**
     * {@inheritdoc}
     */
    protected $positions = [
        'show' => [ 1, 2, 5, 6, 7 ]
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

    /**
     * {@inheritdoc}
     */
    protected function getItem(Request $request)
    {
        try {
            $item = $this->get('api.service.content')
                ->getItemBySlugAndContentType(
                    $request->get('slug'),
                    'static_page'
                );
        } catch (\Exception $e) {
            throw new ResourceNotFoundException();
        }

        if (!$this->get('core.helper.content')->isReadyForPublish($item)) {
            throw new ResourceNotFoundException();
        }

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
            'tags' => $this->getTags([ $item])
        ]);
    }
}
