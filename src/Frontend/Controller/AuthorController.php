<?php

namespace Frontend\Controller;

use Api\Exception\GetItemException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;

class AuthorController extends Controller
{
    /**
     * Shows the author frontpage.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function authorFrontpageAction(Request $request)
    {
        $slug         = $request->query->filter('slug', '', FILTER_SANITIZE_STRING);
        $page         = (int) $request->get('page', 1);
        $itemsPerPage = 12;

        try {
            $user = $this->container->get('api.service.author')
                ->getItemBy("username = '$slug' or slug = '$slug'");
        } catch (GetItemException $e) {
            throw new ResourceNotFoundException();
        }

        $expected = $this->get('router')
            ->generate('frontend_author_frontpage', [ 'slug' => $user->slug ]);

        if ($request->getPathInfo() !== $expected) {
            return new RedirectResponse($expected);
        }

        // Setup templating cache layer
        $this->view->setConfig('articles');
        $cacheID = $this->view->getCacheId('frontpage', 'author', $user->id, $page);

        if (($this->view->getCaching() === 0)
           || (!$this->view->isCached('user/author_frontpage.tpl', $cacheID))
        ) {
            if ($page <= 0 || $page > $this->getParameter('core.max_page')) {
                throw new ResourceNotFoundException();
            }

            $criteria = [
                'fk_author'       => [[ 'value' => $user->id ]],
                'fk_content_type' => [[ 'value' => [1, 4, 7, 9], 'operator' => 'IN' ]],
                'content_status'  => [[ 'value' => 1 ]],
                'in_litter'       => [[ 'value' => 0 ]],
                'starttime'       => [
                    'union' => 'OR',
                    [ 'value' => '0000-00-00 00:00:00' ],
                    [ 'value' => null, 'operator'  => 'IS', 'field' => true ],
                    [ 'value' => date('Y-m-d H:i:s'), 'operator' => '<=' ],
                ],
                'endtime'         => [
                    'union' => 'OR',
                    [ 'value' => '0000-00-00 00:00:00' ],
                    [ 'value' => null, 'operator'  => 'IS', 'field' => true ],
                    [ 'value' => date('Y-m-d H:i:s'), 'operator' => '>' ],
                ]
            ];

            $er            = $this->get('entity_repository');
            $contentsCount = $er->countBy($criteria);
            $contents      = $er->findBy($criteria, 'starttime DESC', $itemsPerPage, $page);

            foreach ($contents as &$item) {
                if (isset($item->img1) && !empty($item->img1)) {
                    $image = $er->find('Photo', $item->img1);

                    if (is_object($image) && !is_null($image->id)) {
                        $item->img1_path = $image->path_file . $image->name;
                        $item->img1      = $image;
                    }
                }

                if ($item->fk_content_type == 7 && !empty($item->cover_id)) {
                    $image = $er->find('Photo', $item->cover_id);

                    if (is_object($image) && !is_null($image->id)) {
                        $item->img1_path = $image->path_file . $image->name;
                        $item->img1      = $image;
                    }
                }

                if ($item->fk_content_type == 9) {
                    $item->obj_video = $item;
                    $item->summary   = $item->description;
                }

                if (isset($item->fk_video) && !empty($item->fk_video)) {
                    $item->video = $er->find('Video', $item->fk_video2);
                }
            }
            // Build the pagination
            $pagination = $this->get('paginator')->get([
                'directional' => true,
                'epp'         => $itemsPerPage,
                'page'        => $page,
                'total'       => $contentsCount,
                'route'       => [
                    'name'   => 'frontend_author_frontpage',
                    'params' => [ 'slug' => $slug, ]
                ],
            ]);

            $this->view->assign([
                'contents'   => $contents,
                'author'     => $user,
                'pagination' => $pagination,
            ]);
        }

        list($positions, $advertisements) = $this->getInnerAds();

        return $this->render('user/author_frontpage.tpl', [
            'ads_positions'  => $positions,
            'advertisements' => $advertisements,
            'cache_id'       => $cacheID,
            'x-tags'         => 'author-user-frontpage,' . $slug . ',' . $page,
            'x-cacheable'    => true,
        ]);
    }

    /**
     * Redirects to the author frontpage in the external site.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function extAuthorFrontpageAction(Request $request)
    {
        $categoryName = $request->query->filter('category_slug', '', FILTER_SANITIZE_STRING);
        $slug         = $request->query->filter('slug', '', FILTER_SANITIZE_STRING);

        // Get sync params
        $wsUrl = $this->get('core.helper.instance_sync')->getSyncUrl($categoryName);
        if (empty($wsUrl)) {
            throw new ResourceNotFoundException();
        }

        return $this->redirect($wsUrl . '/author/' . $slug);
    }

    /**
     * Shows the author frontpage.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function frontpageAuthorsAction(Request $request)
    {
        $page         = (int) $request->get('page', 1);
        $itemsPerPage = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('items_in_blog', 10);

        $offset = ($page - 1) * $itemsPerPage;

        if ($page <= 0 || $page > $this->getParameter('core.max_page')) {
            throw new ResourceNotFoundException();
        }

        // Redirect to first page
        if ($page < 1) {
            return $this->redirectToRoute('frontend_frontpage_authors', [
                'page' => 1
            ]);
        }

        // Setup templating cache layer
        $this->view->setConfig('articles');
        $cacheID = $this->view->getCacheId('frontpage', 'authors', $page);

        if ($this->view->getCaching() === 0
           || !$this->view->isCached('user/frontpage_author.tpl', $cacheID)
        ) {
            $sql = "SELECT SQL_CALC_FOUND_ROWS contents.fk_author as id, count(pk_content) as total FROM contents"
                . " WHERE contents.fk_author IN (SELECT users.id FROM users)"
                . " AND fk_content_type IN (1, 4, 7, 9)  AND content_status = 1 AND in_litter!= 1"
                . " GROUP BY contents.fk_author ORDER BY total DESC"
                . " LIMIT $itemsPerPage OFFSET $offset";

            $items = $this->get('dbal_connection')->fetchAll($sql);

            $sql = 'SELECT FOUND_ROWS()';

            $total = $this->get('dbal_connection')->fetchAssoc($sql);
            $total = array_pop($total);

            // Redirect to last page
            if (ceil($total / $itemsPerPage) < $page) {
                $page = ceil($total / $itemsPerPage);

                return $this->redirectToRoute('frontend_frontpage_authors', [
                    'page' => $page
                ]);
            }

            // Use id as array key
            $items = $this->get('data.manager.filter')
                ->set($items)
                ->filter('mapify', [ 'key' => 'id' ])
                ->get();

            $response = $this->get('api.service.author')->getListByIds(array_keys($items));
            $authors  = $this->get('data.manager.filter')
                ->set($response['items'])
                ->filter('mapify', [ 'key' => 'id' ])
                ->get();

            foreach ($items as &$item) {
                $author = $authors[$item['id']];

                $author->total_contents = $item['total'];

                $item = $author;
            }

            $items = array_filter($items, function ($a) {
                return !is_array($a);
            });

            // Build the pagination
            $pagination = $this->get('paginator')->get([
                'directional' => true,
                'epp'         => $itemsPerPage,
                'page'        => $page,
                'total'       => $total,
                'route'       => 'frontend_frontpage_authors'
            ]);

            $this->view->assign([
                'authors_contents' => $items,
                'pagination'       => $pagination,
            ]);
        }

        list($positions, $advertisements) = $this->getInnerAds();

        return $this->render('user/frontpage_authors.tpl', [
            'ads_positions'  => $positions,
            'advertisements' => $advertisements,
            'cache_id'       => $cacheID,
            'x-tags'         => 'authors-users-frontpage,' . $page,
            'x-cacheable'    => true,
        ]);
    }

    /**
     * Fetches advertisements for article inner.
     *
     * @param string category The category identifier.
     *
     * @return The list of advertisement from positions ids.
     */
    public static function getInnerAds($category = 0)
    {
        $positionManager = getService('core.helper.advertisement');
        $positions       = $positionManager->getPositionsForGroup('article_inner');
        $advertisements  = getService('advertisement_repository')
            ->findByPositionsAndCategory($positions, $category);

        return [ $positions, $advertisements ];
    }
}
