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

use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;

/**
 * Handles the actions for the user profile.
 */
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
        $page         = $request->query->getDigits('page', 1);
        $itemsPerPage = 12;

        $user = $this->get('user_repository')->findOneBy("username='{$slug}'");
        if (empty($user)) {
            throw new ResourceNotFoundException();
        }

        // Setup templating cache layer
        $this->view->setConfig('articles');
        $cacheID = $this->view->getCacheId('frontpage', 'author', $user->id, $page);

        if (($this->view->getCaching() === 0)
           || (!$this->view->isCached('user/author_frontpage.tpl', $cacheID))
        ) {
            $user->photo = $this->get('entity_repository')->find('Photo', $user->avatar_img_id);

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
                $item         = $item->get($item->id);
                $item->author = $user;
                if (isset($item->img1) && ($item->img1 > 0)) {
                    $image = $er->find('Photo', $item->img1);
                    if (is_object($image) && !is_null($image->id)) {
                        $item->img1_path = $image->path_file . $image->name;
                        $item->img1      = $image;
                    }
                }

                if ($item->fk_content_type == 7) {
                    $image           = $er->find('Photo', $item->cover_id);
                    $item->img1_path = $image->path_file . $image->name;
                    $item->img1      = $image;
                }

                if ($item->fk_content_type == 9) {
                    $item->obj_video = $item;
                    $item->summary   = $item->description;
                }

                if (isset($item->fk_video) && ($item->fk_video > 0)) {
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
            'x-cache-for'    => '+1 day'
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
        $categoryName = $request->query->filter('category_name', '', FILTER_SANITIZE_STRING);
        $slug         = $request->query->filter('slug', '', FILTER_SANITIZE_STRING);

        // Get sync params
        $wsUrl = $this->get('core.helper.instance_sync')->getSyncUrl($categoryName);
        if (empty($wsUrl)) {
            throw new ResourceNotFoundException();
        }

        if (empty($wsUrl)) {
            throw new \Symfony\Component\Routing\Exception\ResourceNotFoundException();
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
        $page         = $request->query->getDigits('page', 1);
        $itemsPerPage = 16;

        // Setup templating cache layer
        $this->view->setConfig('articles');
        $cacheID = $this->view->getCacheId('frontpage', 'authors', $page);

        if ($this->view->getCaching() === 0
           || !$this->view->isCached('user/frontpage_author.tpl', $cacheID)
        ) {
            $response = $this->get('api.service.author')->getList();
            $authors  = $this->get('data.manager.filter')
                ->set($response['items'])
                ->filter('mapify', [ 'key' => 'id' ])
                ->get();

            $sql = "SELECT contents.fk_author as id, count(pk_content) as total FROM contents"
                . " WHERE contents.fk_author IN (" . implode(',', array_keys($authors)) . ")"
                . " AND fk_content_type IN (1, 4, 7, 9)  AND available = 1 AND in_litter!= 1"
                . " GROUP BY contents.fk_author ORDER BY total DESC";

            $items = $this->get('dbal_connection')->fetchAll($sql);
            $items = array_slice($items, ($page - 1) * $itemsPerPage, $itemsPerPage);

            foreach ($items as &$item) {
                $authors[$item['id']]->total_contents = $item['total'];

                $item = $authors[$item['id']];

                // Fetch user avatar if exists
                if (!empty($item->avatar_img_id)) {
                    $item->photo = $this->get('entity_repository')->find(
                        'Photo',
                        $item->avatar_img_id
                    );
                }
            }

            // Build the pagination
            $pagination = $this->get('paginator')->get([
                'directional' => true,
                'epp'         => $itemsPerPage,
                'page'        => $page,
                'total'       => $response['total'],
                'route'       => 'frontend_frontpage_authors'
            ]);

            $this->view->assign([
                'authors_contents' => $authors,
                'pagination'       => $pagination,
            ]);
        }

        list($positions, $advertisements) = $this->getInnerAds();

        return $this->render('user/frontpage_authors.tpl', [
            'ads_positions'  => $positions,
            'advertisements' => $advertisements,
            'cache_id'       => $cacheID,
            'x-tags'         => 'authors-users-frontpage,' . $page,
            'x-cache-for'    => '+1 day'
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
