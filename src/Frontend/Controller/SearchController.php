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

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;

/**
 * Handles the actions for searches
 *
 * @package Frontend_Controllers
 */
class SearchController extends Controller
{
    /**
     * {@inheritdoc}
     */
    protected $groups = [
        'google' => 'article_inner'
    ];

    /**
     * {@inheritdoc}
     */
    protected $positions = [
        'article_inner' => [ 7 ]
    ];

    /**
     * Displays the search results with the google algorithm.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function googleAction()
    {
        $this->getAdvertisements();

        return $this->render('search/search.tpl', [
            'x-tags'      => 'google-search',
            'x-cacheable' => true,
        ]);
    }

    /**
     * Displays the search results with the internal algorithm.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function internalAction(Request $request)
    {
        $slug = $this->get('data.manager.filter')
            ->set($request->get('tag_name', null))
            ->filter('slug')
            ->get();


        return $this->redirect(
            $this->get('core.decorator.url')->prefixUrl(
                $this->generateUrl('frontend_tag_frontpage', [
                    'slug' => $slug
                ])
            )
        );
    }
}
