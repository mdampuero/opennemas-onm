<?php
/**
 * Handles the actions for the public RSS
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
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for the public RSS
 *
 * @package Frontend_Controllers
 **/
class RssController extends Controller
{
    /**
     * Shows a page that shows a list of available RSS sources
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function indexAction()
    {
        $this->view = new \Template(TEMPLATE_USER);
        $cacheID = $this->view->generateCacheId('Index', '', "RSS");

        // Fetch information for Advertisements
        \Frontend\Controller\ArticlesController::getAds();

        if (($this->view->caching == 0)
            || !$this->view->isCached('rss/index.tpl', $cacheID)
        ) {
            $ccm = \ContentCategoryManager::get_instance();

            $categoriesTree = $ccm->getCategoriesTreeMenu();
            $opinionAuthors = \User::getAllUsersAuthors();

            $this->view->assign(
                array(
                    'categoriesTree' => $categoriesTree,
                    'opinionAuthors' => $opinionAuthors,
                )
            );
        }

        return $this->render('rss/index.tpl', array('cache_id' => $cacheID));
    }

    /**
     * Displays the RSS feed for a given category, opinion or topic
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function generalRssAction(Request $request)
    {
        $categoryName = $request->query->filter('category_name', 'last', FILTER_SANITIZE_STRING);
        $author       = $request->query->filter('author', '', FILTER_SANITIZE_STRING);

        $this->view = new \Template(TEMPLATE_USER);
        $this->view->setConfig('rss');
        // Generate cache Id
        $cacheID = $this->view->generateCacheId($categoryName, '', 'RSS'.$author);

        if (($this->view->caching == 0)
           || (!$this->view->isCached('rss/rss.tpl', $cacheID))
        ) {
            // Get entity repository service
            $er = getService('entity_repository');

            // Set total number of contents and order for sql's
            $totalContents = 50;
            $order = array('created' => 'DESC');
            $rssTitle = '';
            if ($categoryName == 'opinion') {
                // Get opinion repository service
                $or = getService('opinion_repository');

                if (empty($author)) {
                    // Set sql filter
                    $filters = array(
                        'content_status' => array(array('value' => 1)),
                        'in_litter'      => array(array('value' => 1, 'operator' => '!='))
                    );

                    // Fetch last 50 opinions
                    $contents = $or->findBy($filters, $order, $totalContents, 1);

                    // Set RSS title
                    $rssTitle = 'Últimas Opiniones';
                } else {
                    // Set sql filter
                    $filters = array(
                        'content_status' => array(array('value' => 1)),
                        'author'         => array(array('value' => (int)$author)),
                        'in_litter'      => array(array('value' => 1, 'operator' => '!='))
                    );

                    // Fetch last 50 opinions of author
                    $contents = $or->findBy($filters, $order, $totalContents, 1);

                    // Set RSS title
                    if ($contents) {
                        $rssTitle = 'Opiniones de «'.$contents[0]->author.'»';
                    } else {
                        $rssTitle = 'Este autor no tiene opiniones todavía.';
                    }
                }
            } elseif ($categoryName == 'last') {
                // Set sql filter
                $filters = array(
                    'content_type_name' => array(array('value' => 'article')),
                    'content_status'    => array(array('value' => 1)),
                    'in_litter'         => array(array('value' => 1, 'operator' => '!='))
                );

                // Fetch last 50 articles
                $contents = $er->findBy($filters, $order, $totalContents, 1);

                // Set RSS title
                $rssTitle = 'Últimas Noticias';
            } else {
                // Set sql filter
                $filters = array(
                    'content_type_name' => array(array('value' => 'article')),
                    'category_name'     => array(array('value' => $categoryName)),
                    'content_status'    => array(array('value' => 1)),
                    'in_litter'         => array(array('value' => 1, 'operator' => '!='))
                );

                // Fetch articles for category
                $contents = $er->findBy($filters, $order, $totalContents, 1);

                // Fetch category title
                $category = getService('category_repository')->findBy(
                    array('name' => array(array('value' => $categoryName))),
                    'name ASC'
                );

                // Set RSS title
                $rssTitle = $category[0]->title;
            }

            // Filter by scheduled
            $cm = new \ContentManager();
            $contents = $cm->getInTime($contents);

            // Fetch photo for each article
            foreach ($contents as $key => $content) {
                // Fetch photo for each content
                if (isset($content->img1) && !empty($content->img1)) {
                    $contents[$key]->photo = $er->find('Photo', $content->img1);
                } elseif (isset($content->img2) && !empty($content->img2)) {
                    $contents[$key]->photo = $er->find('Photo', $content->img2);
                }

                // Exclude articles with external link from RSS
                if (isset($content->params['bodyLink'])
                    && !empty($content->params['bodyLink'])) {
                    unset($contents[$key]);
                }
            }

            $this->view->assign(
                array(
                    'rss_title' => $rssTitle,
                    'contents'  => $contents,
                    'type'      => $categoryName,
                )
            );
        }

        $response = new Response(
            '',
            200,
            array(
                'Content-Type' => 'text/xml; charset=UTF-8',
                'x-tags'       => 'rss',
            )
        );

        return $this->render(
            'rss/rss.tpl',
            array('cache_id' => $cacheID),
            $response
        );
    }

    /**
     * Shows the author frontpage
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function authorRSSAction(Request $request)
    {
        $slug         = $request->query->filter('author_slug', '', FILTER_SANITIZE_STRING);
        $page         = $request->query->getDigits('page', 1);
        $itemsPerPage = 50;

        $this->view = new \Template(TEMPLATE_USER);
        $this->view->setConfig('rss');

        $cacheID = $this->view->generateCacheId('authorRSS-'.$slug, '', $page);

        if (($this->view->caching == 0)
           || (!$this->view->isCached('rss/rss.tpl', $cacheID))
        ) {
            // Get user by slug
            $ur = $this->get('user_repository');
            $filters['username'] = array(array('value' => $slug));
            $user = $ur->findBy($filters, '');
            $user = $user[0];
            if (!empty($user)) {
                $rssTitle   = 'RSS de «'.$user->name.'»';
                // Get entity repository
                $er = $this->get('entity_repository');
                $user->photo = $er->find('Photo', $user->avatar_img_id);
                $user->getMeta();

                // Fetch author contents
                $searchCriteria =  array(
                    'fk_author'       => array(array('value' => $user->id)),
                    'fk_content_type' => array(array('value' => array(1, 4, 7), 'operator' => 'IN')),
                    'content_status'  => array(array('value' => 1)),
                    'in_litter'       => array(array('value' => 0)),
                );
                $contents = $er->findBy($searchCriteria, 'starttime DESC', $itemsPerPage, $page);

                foreach ($contents as $key => &$item) {
                    $item->author = $user;
                    if (isset($item->img1) && ($item->img1 > 0)) {
                        $contents[$key]->photo = $er->find('Photo', $item->img1);
                    }

                    if ($item->fk_content_type == 7) {
                        $contents[$key]->photo = $er->find('Photo', $item->cover_id);
                    }

                    if (empty($item->summary)) {
                        $item->summary = substr(strip_tags($item->body), 0, 350);
                    }
                }

                $this->view->assign(
                    array(
                        'contents'  => $contents,
                        'rss_title' => $rssTitle,
                    )
                );
            }
        }

        return $this->render(
            'rss/rss.tpl',
            array('cache_id' => $cacheID),
            new Response(
                '',
                200,
                array(
                    'Content-Type' => 'text/xml; charset=UTF-8',
                    'x-tags'       => 'rss',
                )
            )
        );

    }
}
