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

use Common\ORM\Core\Exception\EntityNotFoundException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Common\Core\Controller\Controller;
use Onm\Settings as s;

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

        $cacheID = $this->view->generateCacheId('author-'.$slug, '', $page);

        if (($this->view->getCaching() === 0)
           || (!$this->view->isCached('user/author_frontpage.tpl', $cacheID))
        ) {
            $user = $this->get('user_repository')->findOneBy("username='{$slug}'");

            if (empty($user)) {
                throw new ResourceNotFoundException();
            }

            $user->photo = $this->get('entity_repository')->find('Photo', $user->avatar_img_id);

            $criteria = array(
                'fk_author'       => array(array('value' => $user->id)),
                'fk_content_type' => array(array('value' => array(1, 4, 7, 9), 'operator' => 'IN')),
                'content_status'  => array(array('value' => 1)),
                'in_litter'       => array(array('value' => 0)),
            );

            $er = $this->get('entity_repository');
            $contentsCount  = $er->countBy($criteria);
            $contents = $er->findBy($criteria, 'starttime DESC', $itemsPerPage, $page);

            foreach ($contents as &$item) {
                $item = $item->get($item->id);
                $item->author = $user;
                if (isset($item->img1) && ($item->img1 > 0)) {
                    $image = $this->get('entity_repository')->find('Photo', $item->img1);
                    if (is_object($image) && !is_null($image->id)) {
                        $item->img1_path = $image->path_file.$image->name;
                        $item->img1 = $image;
                    }
                }

                if ($item->fk_content_type == 7) {
                    $image = $this->get('entity_repository')->find('Photo', $item->cover_id);
                    $item->img1_path = $image->path_file.$image->name;
                    $item->img1 = $image;
                    $item->summary = $item->subtitle;
                    $item->subtitle= '';
                }

                if ($item->fk_content_type == 9) {
                    $item->obj_video = $item;
                    $item->summary = $item->description;
                }

                if (isset($item->fk_video) && ($item->fk_video > 0)) {
                    $item->video = $this->get('entity_repository')->find('Video', $item->fk_video2);
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
            'x-tags'         => 'author-user-frontpage,'.$slug.','.$page,
            'x-cache-for'    => '+1 day'
        ]);
    }

    /**
     * Shows the author frontpage from external source.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function extAuthorFrontpageAction(Request $request)
    {
        $categoryName = $request->query->filter('category_name', '', FILTER_SANITIZE_STRING);
        $slug = $request->query->filter('slug', '', FILTER_SANITIZE_STRING);

        // Get sync params
        $wsUrl = '';
        $syncParams = $this->get('setting_repository')->get('sync_params');
        if ($syncParams) {
            foreach ($syncParams as $siteUrl => $values) {
                if (in_array($categoryName, $values['categories'])) {
                    $wsUrl = $siteUrl;
                }
            }
        }

        if (empty($wsUrl)) {
            throw new \Symfony\Component\Routing\Exception\ResourceNotFoundException();
        }

        return $this->redirect($wsUrl.'/author/'.$slug);
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

        $cacheID = $this->view->generateCacheId('frontpage-authors', '', $page);

        if ($this->view->getCaching() === 0
           || !$this->view->isCached('user/frontpage_author.tpl', $cacheID)
        ) {
            $sql = "SELECT count(pk_content) as total_contents, users.id FROM contents, users "
                ." WHERE users.fk_user_group  LIKE '%3%' "
                ." AND contents.fk_author = users.id  AND fk_content_type IN (1, 4, 7, 9) "
                ." AND available = 1 AND in_litter!= 1 GROUP BY users.id ORDER BY total_contents DESC";

            $authors = $this->get('dbal_connection')->fetchAll($sql);

            $total   = count($authors);
            $authors = array_slice($authors, ($page - 1) * $itemsPerPage, $itemsPerPage);

            // Build the pagination
            $pagination = $this->get('paginator')->get([
                'directional' => true,
                'epp'         => $itemsPerPage,
                'page'        => $page,
                'total'       => $total,
                'route'       => 'frontend_frontpage_authors'
            ]);

            $ids = array_map(function ($a) {
                return $a['id'];
            }, $authors);

            // Get user by slug
            $oql   = sprintf('id in [%s]', implode(',', $ids));
            $users = $this->get('orm.manager')->getRepository('User')->findBy($oql);

            // Map to keep original order
            $map = [];
            foreach ($users as $user) {
                $map[$user->id] = $user;
            }

            foreach ($authors as &$item) {
                $user                 = $map[$item['id']];
                $user->total_contents = $item['total_contents'];
                $item                 = $user;
                // Fetch user avatar if exists
                if (!empty($item->avatar_img_id)) {
                    $item->photo = $this->get('entity_repository')->find(
                        'Photo',
                        $item->avatar_img_id
                    );
                }
            }

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
        $advertisements  = \Advertisement::findForPositionIdsAndCategoryPlain($positions, $category);

        return [ $positions, $advertisements ];
    }
}
