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
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for advertisements
 *
 * @package Frontend_Controllers
 **/
class BlogsController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        $this->category_name = $this->request->query->filter('category_name', 'blog', FILTER_SANITIZE_STRING);
        $this->view->assign('actual_category', 'blog'); // Used in renderMenu
    }

    /**
     * Renders the blog opinion frontpage.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     */
    public function frontpageAction(Request $request)
    {
        $page = $this->request->query->getDigits('page', 1);

        // Index frontpage
        $this->view = new \Template(TEMPLATE_USER);
        $this->view->setConfig('opinion');

        $cacheID = $this->view->generateCacheId($this->category_name, '', $page);

        // Don't execute the app logic if there are caches available
        if (($this->view->caching == 0)
            || !$this->view->isCached('opinion/blog_frontpage.tpl', $cacheID)
        ) {
            $authors = array();
            foreach (\User::getAllUsersAuthors() as $author) {
                if ($author->is_blog == 1) {
                    $authors[$author->id] = $author;
                }
            }

            $itemsPerPage = s::get('items_in_blog');

            $order   = array('starttime' => 'DESC');
            $filters = array(
                'content_type_name' => array(array('value' => 'opinion')),
                'type_opinion'      => array(array('value' => 0)),
                'content_status'    => array(array('value' => 1)),
                'blog'              => array(array('value' => 1)),
            );

            $em         = $this->get('opinion_repository');
            $blogs      = $em->findBy($filters, $order, $itemsPerPage, $page);
            $countItems = $em->countBy($filters);

            $pagination = \Pager::factory(
                array(
                    'mode'        => 'Sliding',
                    'perPage'     => $itemsPerPage,
                    'append'      => false,
                    'path'        => '',
                    'delta'       => 3,
                    'clearIfVoid' => true,
                    'urlVar'      => 'page',
                    'totalItems'  => $countItems,
                    'fileName'    => $this->generateUrl(
                        'frontend_blog_frontpage'
                    ).'/?page=%d',
                )
            );

            foreach ($blogs as &$blog) {
                if (array_key_exists($blog->fk_author, $authors)) {

                    $blog->author           = $authors[$blog->fk_author];
                    $blog->name             = $blog->author->name;
                    $blog->author_name_slug = \Onm\StringUtils::get_title($blog->name);
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
                            'slug' => $blog->author->name,
                            'id'   => $blog->author->id
                        )
                    );
                }
            }

            $this->view->assign(
                array(
                    'opinions'   => $blogs,
                    'authors'    => $authors,
                    'pagination' => $pagination,
                    'page'       => $page
                )
            );
        }

        $ads = $this->getAds();
        $this->view->assign('advertisements', $ads);

        return $this->render(
            'opinion/blog_frontpage.tpl',
            array('cache_id' => $cacheID)
        );
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
        $page = $this->request->query->getDigits('page', 1);
        $slug       = $request->query->filter('author_slug', '', FILTER_SANITIZE_STRING);

        if (empty($slug)) {
            return new RedirectResponse($this->generateUrl('frontend_blog_frontpage'));
        }

        $this->view = new \Template(TEMPLATE_USER);
        $this->view->setConfig('opinion');
        $cacheID = $this->view->generateCacheId($this->category_name, $slug, $page);

        // Don't execute the app logic if there are caches available
        if (($this->view->caching == 0)
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
                    .' AND contents.content_status=1',
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
                $pagination = \Pager::factory(
                    array(
                        'mode'        => 'Sliding',
                        'perPage'     => $itemsPerPage,
                        'append'      => false,
                        'path'        => '',
                        'delta'       => 4,
                        'clearIfVoid' => true,
                        'urlVar'      => 'page',
                        'totalItems'  => $countItems,
                        'fileName'    => $this->generateUrl(
                            'frontend_blog_author_frontpage',
                            array(
                                'author_slug' => $author->slug,
                            )
                        ).'/?page=%d',
                    )
                );

                $this->view->assign(
                    array(
                        'pagination' => $pagination,
                        'blogs'      => $blogs,
                        'author'     => $author,
                        'page'       => $page,
                    )
                );
            }

        } // End if isCached

        //Fetch information for Advertisements
        $ads = $this->getAds();
        $this->view->assign('advertisements', $ads);

        return $this->render(
            'opinion/blog_author_index.tpl',
            array('cache_id' => $cacheID)
        );

    }



    /**
     * Displays an blog given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function showAction(Request $request)
    {
        $dirtyID   = $request->query->getDigits('blog_id');
        $blogID = \Content::resolveID($dirtyID);

        // Redirect to blog frontpage if blog_id wasn't provided
        if (empty($blogID)) {
            return new RedirectResponse($this->generateUrl('frontend_blog_frontpage'));
        }

        $blog = $this->get('opinion_repository')->find('Opinion', $blogID);

        // TODO: Think that this comments related code can be deleted.
        if (($blog->content_status != 1) || ($blog->in_litter != 0)) {
            throw new \Symfony\Component\Routing\Exception\ResourceNotFoundException();
        }

        $this->view = new \Template(TEMPLATE_USER);
        $this->view->setConfig('opinion');

        //Fetch information for Advertisements
        $ads = $this->getAds('inner');
        $this->view->assign('advertisements', $ads);

        // Don't execute the app logic if there are caches available
        $cacheID = $this->view->generateCacheId($this->category_name, '', $blogID);
        if (($this->view->caching == 0)
            || !$this->view->isCached('blog/blog_inner.tpl', $cacheID)
        ) {
            $this->view->assign('contentId', $blogID);

            $author = $this->get('user_repository')->find($blog->fk_author);
            $blog->author = $author;

            // Rescato esta asignación para que genere correctamente el enlace a frontpage de opinion
            $blog->author_name_slug = \Onm\StringUtils::get_title($blog->name);
            if (!array_key_exists('is_blog', $author->meta) || (array_key_exists('is_blog', $author->meta) && $author->meta['is_blog'] != 1)) {
                return new RedirectResponse(
                    $this->generateUrl(
                        'frontend_opinion_show',
                        array(
                            'blog_id' => $dirtyID,
                            'author_name' => $author->username,
                            'blog_title'  => $blog->slug,
                        )
                    )
                );
            }

            // Associated media code --------------------------------------
            if (isset($blog->img2) && ($blog->img2 > 0)) {
                $photo = $this->get('entity_repository')->find('Photo', $blog->img2);
                $this->view->assign('photo', $photo);
            }
            $this->view->assign(
                array(
                    'blog'     => $blog,
                    'content'  => $blog,
                    'author'   => $author,
                )
            );

        } // End if isCached

        // Show in Frontpage
        return $this->render(
            'opinion/blog_inner.tpl',
            array('cache_id' => $cacheID)
        );
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
        $positionManager = getService('instance_manager')->current_instance->theme->getAdsPositionManager();
        if ($context == 'inner') {
            $positions = $positionManager->getAdsPositionsForGroup('opinion_inner', array(7, 9));
        } else {
            $positions = $positionManager->getAdsPositionsForGroup('opinion_frontpage', array(7, 9));
        }

        $ccm = \ContentCategoryManager::get_instance();
        $category = $ccm->get_id($this->category_name);
        $category = (!isset($category) || ($category=='home'))? 0: $category;

        return \Advertisement::findForPositionIdsAndCategory($positions, $category);
    }
}
