<?php
/**
 * Defines the frontend controller for the opinion-blog content type
 *
 * @package Frontend_Controllers
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Frontend\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Common\Core\Controller\Controller;
use Onm\Settings as s;

/**
 * Handles the actions for advertisements
 *
 * @package Frontend_Controllers
 **/
class BlogsController extends Controller
{
    /**
     * Renders the blog opinion frontpage.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     */
    public function frontpageAction(Request $request)
    {
        $page = $request->query->getDigits('page', 1);

        // Setup templating cache layer
        $this->view->setConfig('opinion');
        $cacheID = $this->view->getCacheId('frontpage', 'blog', $page);

        if (($this->view->getCaching() === 0)
            || !$this->view->isCached('opinion/blog_frontpage.tpl', $cacheID)
        ) {
            $authors = array();
            foreach (\User::getAllUsersAuthors() as $author) {
                if ($author->is_blog == 1) {
                    $authors[$author->id] = $author;
                }
            }

            $itemsPerPage = $this->get('setting_repository')->get('items_in_blog', 10);

            $order   = array('starttime' => 'DESC');
            $date    = date('Y-m-d H:i:s');
            $filters = array(
                'content_type_name' => [[ 'value' => 'opinion' ]],
                'type_opinion'      => [[ 'value' => 0 ]],
                'content_status'    => [[ 'value' => 1 ]],
                'blog'              => [[ 'value' => 1 ]],
                'starttime'         => [
                    'union' => 'OR',
                    [ 'value' => null, 'operator' => 'IS' ],
                    [ 'value' => $date, 'operator' => '<' ]
                ],
                'endtime'           => [
                    'union'   => 'OR',
                    [ 'value'  => null, 'operator' => 'IS', 'field' => true ],
                    [ 'value' => '0000-00-00 00:00:00', 'operator' => '=' ],
                    [ 'value' => $date, 'operator' => '>' ]
                ],
            );

            $em         = $this->get('opinion_repository');
            $blogs      = $em->findBy($filters, $order, $itemsPerPage, $page);
            $countItems = $em->countBy($filters);

            $pagination = $this->get('paginator')->get([
                'directional' => true,
                'epp'         => $itemsPerPage,
                'total'       => $countItems,
                'route'       => 'frontend_blog_frontpage',
            ]);

            foreach ($blogs as &$blog) {
                if (array_key_exists($blog->fk_author, $authors)) {
                    $blog->author           = $authors[$blog->fk_author];
                    $blog->name             = $blog->author->name;
                    $blog->author_name_slug = $blog->author->username;
                    // ????
                    $item = new \Content();
                    $item->loadAllContentProperties($blog->pk_content);
                    $blog->summary = $item->summary;
                    $blog->img1_footer = $item->img1_footer;
                    if (isset($item->img1) && ($item->img1 > 0)) {
                        $blog->img1 = $this->get('entity_repository')->find('Photo', $item->img1);
                    }

                    $blog->author->uri = \Uri::generate(
                        'frontend_blog_author_frontpage',
                        array(
                            'slug' => urlencode($blog->author->username),
                            'id'   => $blog->author->id
                        )
                    );
                }
            }

            $this->view->assign([
                'opinions'   => $blogs,
                'authors'    => $authors,
                'pagination' => $pagination,
                'page'       => $page
            ]);
        }

        list($positions, $advertisements) = $this->getAds();

        return $this->render('opinion/blog_frontpage.tpl', [
            'advertisements'  => $advertisements,
            'ads_positions'   => $positions,
            'cache_id'        => $cacheID,
            'actual_category' => 'blog', // Used in renderMenu
            'x-tags'          => 'blog-frontpage,'.$page
        ]);
    }

    /**
     * Renders the opinion author's frontpage
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function frontpageAuthorAction(Request $request)
    {
        $page = $request->query->getDigits('page', 1);
        $slug = $request->query->filter('author_slug', '', FILTER_SANITIZE_STRING);

        if (empty($slug)) {
            return new RedirectResponse($this->generateUrl('frontend_blog_frontpage'));
        }

        // Setup templating cache layer
        $this->view->setConfig('opinion');
        $cacheID = $this->view->getCacheId('frontpage', 'author', $slug, $page);

        if (($this->view->getCaching() === 0)
            || !$this->view->isCached('opinion/blog_author_index.tpl', $cacheID)
        ) {
            $itemsPerPage = s::get('items_per_page');

            $ur = $this->get('user_repository');
            $author = $ur->findOneBy("username='{$slug}'", 'ID DESC');
            if (!empty($author)) {
                $author->slug = $author->username;
                $author->photo = $this->get('entity_repository')->find('Photo', $author->avatar_img_id);
                $author->getMeta();

                $filter = 'opinions.type_opinion=0 AND opinions.fk_author='.$author->id;
                  // generate pagination params

                $_limit = ' LIMIT '.(($page-1)*$itemsPerPage).', '.($itemsPerPage);

                $this->cm = new \ContentManager();

                // Get the number of total opinions for this author for pagination purpouses
                $countItems = $this->cm->count(
                    'Opinion',
                    $filter
                    .' AND contents.content_status=1 '
                );

                // Get the list articles for this author
                $blogs = $this->cm->getOpinionArticlesWithAuthorInfo(
                    $filter
                    .' AND contents.content_status=1 AND starttime <= NOW()',
                    'ORDER BY starttime DESC '.$_limit
                );

                if (!empty($blogs)) {
                    foreach ($blogs as &$blog) {
                        // Overload blog array with more information
                        $item = new \Content();
                        $item->loadAllContentProperties($blog['pk_content']);
                        $blog['summary']           = $item->summary;
                        $blog['img1_footer']       = $item->img1_footer;
                        $blog['pk_author']         = $author->id;
                        $blog['author_name_slug']  = $author->slug;
                        $blog['comments']          = $item->comments;
                        if (isset($item->img1) && ($item->img1 > 0)) {
                            $blog['img1'] = $this->get('entity_repository')->find('Photo', $item->img1);
                        }

                        // Generate blog item uri
                        $blog['uri'] = $this->generateUrl(
                            'frontend_blog_show',
                            array(
                                'blog_id'     => date('YmdHis', strtotime($blog['created'])).$blog['id'],
                                'author_name' => $blog['author_name_slug'],
                                'blog_title'  => $blog['slug'],
                            )
                        );

                        // Generate author uri
                        $blog['author_uri'] = $this->generateUrl(
                            'frontend_blog_author_frontpage',
                            array(
                                'author_slug' => $blog['author_name_slug'],
                            )
                        );
                    }
                }

                $pagination = $this->get('paginator')->get([
                    'directional' => true,
                    'epp'         => $itemsPerPage,
                    'total'       => $countItems,
                    'route'       => [
                        'name'   => 'frontend_blog_author_frontpage',
                        'params' => [ 'author_slug' => $author->slug ]
                    ],
                ]);

                $this->view->assign([
                    'pagination' => $pagination,
                    'blogs'      => $blogs,
                    'author'     => $author,
                    'page'       => $page,
                ]);
            }
        } // End if isCached

        list($positions, $advertisements) = $this->getAds();

        return $this->render('opinion/blog_author_index.tpl', [
            'cache_id'        => $cacheID,
            'advertisements'  => $advertisements,
            'ads_positions'   => $positions,
            'actual_category' => 'blog', // Used in renderMenu
            'x-tags'          => 'blog-author-frontpage,'.$slug.','.$page
        ]);
    }

    /**
     * Displays a blog given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function showAction(Request $request)
    {
        $dirtyID = $request->query->getDigits('blog_id');
        $urlSlug = $request->query->filter('blog_title', '', FILTER_SANITIZE_STRING);

        $blog = $this->get('content_url_matcher')
            ->matchContentUrl('opinion', $dirtyID, $urlSlug);

        if (empty($blog)) {
            throw new ResourceNotFoundException();
        }

        $subscriptionFilter = new \Frontend\Filter\SubscriptionFilter($this->view, $this->getUser());
        $cacheable = $subscriptionFilter->subscriptionHook($blog);

        // Setup templating cache layer
        $this->view->setConfig('opinion');
        $cacheID = $this->view->getCacheId('content', $blog->id);

        if (($this->view->getCaching() === 0)
            || !$this->view->isCached('blog/blog_inner.tpl', $cacheID)
        ) {
            $author = $this->get('user_repository')->find($blog->fk_author);
            $blog->author = $author;

            // This assignation is required to get the frontpage opinion link generated properly
            $blog->author_name_slug = $author->username;
            if (!array_key_exists('is_blog', $author->meta)
                || (array_key_exists('is_blog', $author->meta) && $author->meta['is_blog'] != 1)
            ) {
                return new RedirectResponse(
                    $this->generateUrl('frontend_opinion_show',[
                        'blog_id' => $dirtyID,
                        'author_name' => $author->username,
                        'blog_title'  => $blog->slug,
                    ])
                );
            }

            // Associated media code --------------------------------------
            if (isset($blog->img2) && ($blog->img2 > 0)) {
                $photo = $this->get('entity_repository')->find('Photo', $blog->img2);
                $this->view->assign('photo', $photo);
            }
            $this->view->assign(['author' => $author]);
        }

        list($positions, $advertisements) = $this->getAds('inner');

        // Show in Frontpage
        return $this->render('opinion/blog_inner.tpl', [
            'ads_positions'   => $positions,
            'advertisements'  => $advertisements,
            'blog'            => $blog,
            'cache_id'        => $cacheID,
            'content'         => $blog,
            'contentId'       => $blog->id,
            'actual_category' => 'blog', // Used in renderMenu
            'x-tags'          => 'blog-inner,'.$blog->id,
            'x-cache-for'     => '+1 day',
            'x-cacheable'     => $cacheable
        ]);
    }

    /**
     * Fetches the advertisement
     *
     * @param string $context the context to fetch ads from
     *
     * @return array the list of advertisement objects
     */
    private function getAds($context = '')
    {
        // Get opinion positions
        $positionManager = $this->get('core.helper.advertisement');
        if ($context == 'inner') {
            $positions = $positionManager->getPositionsForGroup('opinion_inner', [ 7 ]);
        } else {
            $positions = $positionManager->getPositionsForGroup('opinion_frontpage', [ 7, 9 ]);
        }

        $advertisements = $this->get('advertisement_repository')
            ->findByPositionsAndCategory($positions, 4);

        return [ $positions, $advertisements ];
    }
}
