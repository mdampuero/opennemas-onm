<?php
/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Backend\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Backend\Annotation\CheckModuleAccess;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;

/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 **/
class CacheManagerController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        // Initialization of the frontend template object
        $this->frontpageTemplate = new \Template(TEMPLATE_USER);

        // Initialization of the template cache manager
        $this->cacheManager = $this->get('template_cache_manager');
        $this->cacheManager->setSmarty($this->frontpageTemplate);
    }

    /**
     * Lists cache files and perform searches across them
     *
     * @param Request $request the request object
     *
     * @return string the string response
     *
     * @Security("has_role('CACHE_TPL_ADMIN')")
     *
     * @CheckModuleAccess(module="CACHE_MANAGER")
     **/
    public function defaultAction(Request $request)
    {
        list($this->filter, $this->params, $this->page, $this->itemsPerPage) =
            $this->buildFilter($request);

        // Get available cache files
        $caches = $this->cacheManager->scan($this->filter);
        if (!is_array($caches)) {
            $caches = array();
        }

        // Build the pager
        $pagination = $this->get('paginator')->create([
            'elements_per_page' => $this->itemsPerPage,
            'total_items'       => count($caches),
            'base_url'          => $this->generateUrl(
                'admin_tpl_manager',
                array(
                    'items_page'      => $this->itemsPerPage,
                    'section'         => $this->request->query->filter('section', '', FILTER_SANITIZE_STRING),
                    'type'            => $this->request->query->filter('type', '', FILTER_SANITIZE_STRING),
                )
            ),
        ]);

        // Get only cache files within pagination range
        $caches = array_slice($caches, ($this->page-1)*$this->itemsPerPage, $this->itemsPerPage);

        // Get all the information of the available cache files
        $caches = $this->cacheManager->parseList($caches);

        // ContentCategoryManager manager to handle categories
        $ccm = \ContentCategoryManager::get_instance();

        list($pkContents, $pkAuthors) = $this->cacheManager->getResources($caches);

        // Fetch all authors and generate associated array
        $allAuthors = \User::getAllUsersAuthors();
        $allAuthorsArray = array();
        foreach ($allAuthors as $author) {
            $allAuthorsArray[$author->id] = $author->name;
        }

        // Initialize vars
        $cm            = new \ContentManager();
        $articles      = $cm->getContents($pkContents);
        $articleTitles = array();
        $articleUris   = array();

        // Build information for each article/opinion element
        if (count($articles)>0) {
            foreach ($articles as $article) {
                $articleTitles[$article->pk_content] = $article->title;
                if ($article->fk_content_type == '4') {
                    $authorName = !empty($allAuthorsArray[$article->fk_author])
                        ? $allAuthorsArray[$article->fk_author]:'opinion';
                    $articleUris[$article->pk_content] = \Uri::generate(
                        'opinion',
                        array(
                            'id'       => sprintf('%06d', $article->id),
                            'date'     => date('YmdHis', strtotime($article->created)),
                            'category' => $authorName,
                            'slug'     => $article->slug,
                        )
                    );
                } else {
                    $articleUris[$article->pk_content] = $article->uri;
                }
            }
        }

        // Build information for author front pages
        $authors = array();
        if (count($pkAuthors) > 0) {
            $authorsForContents = $author->find('id IN ('.implode(',', $pkAuthors).')');
            foreach ($authorsForContents as $author) {
                $authors['RSS'.$author->id] = $author->name;
            }
        }

        // Build information for frontpages
        $sections = array();
        foreach ($this->cacheManager->cacheGroups as $cacheGroup) {
            $categoryName = $ccm->getTitle($cacheGroup);
            $sections[$cacheGroup] = (empty($categoryName))? _('FRONTPAGE'): $categoryName;
        }

        foreach ($caches as &$cache) {
            $cache['cache_id'] = $cache["category"] . "|" . $cache["resource"];
            $cache['tpl'] = $cache["template"] . ".tpl";
            if ($cache["template"] == 'opinion_author_index') {
                if (preg_match('/([0-9]+)_([0-9]+)/', $cache['resource'], $match)) {
                    $cache["authorid"] =(int)$match[1];
                    $cache["page"] =$match[2];
                }
            }
        }

        return $this->render(
            'tpl_manager/tpl_manager.tpl',
            array(
                'authors'      => $authors,
                'paramsUri'    => $this->params,
                'pagination'   => $pagination,
                'sections'     => $sections,
                'ccm'          => $ccm,
                'titles'       => $articleTitles,
                'contentUris'  => $articleUris,
                'caches'       => $caches,
                'allAuthors'   => $allAuthorsArray,
                'page'         => $this->page,
                'itemsperpage' => $this->itemsPerPage,
            )
        );
    }

    /**
     * Recreates a template cache
     *
     * @param Request $request the request object
     *
     * @return string the string response
     *
     * @Security("has_role('CACHE_TPL_ADMIN')")
     *
     * @CheckModuleAccess(module="CACHE_MANAGER")
     **/
    public function deleteAction(Request $request)
    {
        $itemsSelected = $request->request->get('selected', null);
        $itemsCacheIds = $request->request->get('cacheid');
        $itemsTemplate = $request->request->get('tpl');

        if (is_null($itemsSelected) && is_null($itemsCacheIds) && is_null($itemsTemplate)) {
            $itemsSelected = $request->query->get('selected', null);
            $itemsCacheIds = $request->query->get('cacheid');
            $itemsTemplate = $request->query->get('tpl');
        }

        // If there was selected more than one item
        // delete them if not delete only one
        if (count($itemsSelected) > 0) {
            foreach ($itemsSelected as $item) {
                $result = $this->cacheManager->delete($itemsCacheIds[$item], $itemsTemplate[$item]);
            }
        } elseif (is_string($itemsCacheIds)) {
            $result = $this->cacheManager->delete($itemsCacheIds, $itemsTemplate);
        }

        if (!$this->request->isXmlHttpRequest()) {
            return $this->redirect($this->generateUrl('admin_tpl_manager'));
        }
        return new Response('OK', 200);
    }

    /**
     * Deletes all the frontend cache files
     * DANGER: this action has really CPU expensive
     *
     * @return string the result string
     *
     * @Security("has_role('CACHE_TPL_ADMIN')")
     *
     * @CheckModuleAccess(module="CACHE_MANAGER")
     **/
    public function deleteAllAction()
    {
        $this->frontpageTemplate->clearAllCache();

        return $this->redirect($this->generateUrl('admin_tpl_manager'));
    }

    /**
     * Show the configuration form and stores it information
     *
     * @param Request $request the request object
     *
     * @return string the string response
     *
     * @Security("has_role('CACHE_TPL_ADMIN')")
     *
     * @CheckModuleAccess(module="CACHE_MANAGER")
     **/
    public function configAction(Request $request)
    {
        $configDir = $this->frontpageTemplate ->config_dir[0];
        $configManager = $this->container->get('template_cache_config_manager')->setConfigDir(
            $configDir
        );

        if ($this->request->getMethod() == 'POST') {
            $config = array();

            $cacheGroups             = $request->request->get('group');
            $cacheGroupsCacheEnabled = $request->request->get('caching');
            $cacheGroupsLifeTime     = $request->request->get('cache_lifetime');

            foreach ($cacheGroups as $section) {
                $caching          = (isset($cacheGroupsCacheEnabled[$section]))? 1: 0;
                $cache_lifetime   = intval($cacheGroupsLifeTime[$section]);

                $config[$section] = array(
                    'caching'        => $caching,
                    'cache_lifetime' => $cache_lifetime,
                );
            }
            $saved = $configManager->save($config);

            if ($saved) {
                $this->get('session')->getFlashBag()->add(
                    'success',
                    _('Cache configuration saved successfully.')
                );
            } else {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    _('Unable to save the cache configuration.')
                );
            }

            return $this->redirect($this->generateUrl('admin_tpl_manager_config'));
        } else {
            $config = $configManager->load();

            return $this->render(
                'tpl_manager/config.tpl',
                array(
                    'config'    => $config,
                    'groupName' => array(
                        'frontpages'        => _('Frontpage'),
                        'frontpage-mobile'  => _('Frontpage mobile version'),
                        'articles'          => _('Inner Article'),
                        'articles-mobile'   => _('Inner Article mobile version'),
                        'opinion'           => _('Inner Opinion'),
                        'rss'               => _('RSS'),
                        'sitemap'           => _('Sitemap'),
                        'video'             => _('Frontpage videos'),
                        'video-inner'       => _('Inner video'),
                        'gallery-frontpage' => _('Gallery frontpage'),
                        'gallery-inner'     => _('Gallery Inner'),
                        'poll-frontpage'    => _('Polls frontpage'),
                        'poll-inner'        => _('Poll inner'),
                    ),
                    'groupIcon' => array(
                        'frontpages'        => 'frontpage.png',
                        'frontpage-mobile'  => 'mobile.png',
                        'articles'          => 'article.png',
                        'articles-mobile'   => 'mobile.png',
                        'opinion'           => 'opinion.png',
                        'rss'               => 'rss.png',
                        'video'             => 'video.png',
                        'video-inner'       => 'video.png',
                        'gallery-frontpage' => 'album.png',
                        'gallery-inner'     => 'album.png',
                        'poll-frontpage'    => 'polls.png',
                        'poll-inner'        => 'polls.png',
                        'sitemap'           => 'sitemap.png',
                    ),
                )
            );
        }
    }

    /**
     * Builds the search filter for listing the listing cache action
     *
     * @param Request $request the request object
     *
     * @return array
     **/
    private function buildFilter($request)
    {
        $section      = $request->query->get('section', null);
        $type         = $request->query->get('type', null);
        $page         = $request->query->get('page', 1);
        $itemsPerPage = $request->query->get('items_page', 15);
        if (empty($itemsPerPage)) {
            $itemsPerPage = 15;
        }

        // If section is defined include it in filter and params
        $filter = '';
        $params = array();
        if (isset($section) && !empty($section)) {
            $filter  .= '^'.preg_quote($section).'\^.*?';
            $params[] = 'section='.$section;
        }

        // If cache file type is defined include it in filter and params
        if (isset($type) && !empty($type)) {
            $regexp = array(
                'frontpages'         => 'frontpage\.tpl\.php$',
                'opinions'           => 'opinion\.tpl\.php$',
                'frontpage-opinions' => 'opinion_author_index\.tpl\.php$',
                'articles'           => 'article\.tpl\.php$',
                'rss'                => '\^RSS[0-9]*\^',
                'mobilepages'        => 'frontpage-mobile\.tpl\.php$',
                'poll'               => 'poll\.tpl\.php$',
                'video-frontpage'    => 'video_frontpage\.tpl\.php$',
                'video-inner'        => 'video_inner\.tpl\.php$',
                'gallery-frontpage'  => 'album_frontpage\.tpl\.php$',
                'gallery-inner'      => 'album\.tpl\.php$',
                'poll-frontpage'     => 'poll_frontpage\.tpl\.php$',
                'poll-inner'         => 'poll.tpl\.php$',
            );
            $filter  .= $regexp[ $_REQUEST['type'] ];
            $params[] = 'type='.$_REQUEST['type'];
        }

        // If page is defined include it in params and page
        if (!empty($page)) {
            $params[] = 'page='.$page;
        }

        $params[] = 'items_page='.$itemsPerPage;
        if (!empty($filter)) {
            $filter = '@'.$filter.'@';
        }

        // return $filter and URI $params
        return array($filter, implode('&', $params), $page, $itemsPerPage);
    }
}
