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

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;
use Onm\Message as m;

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
        //Check if module is activated in this onm instance
        \Onm\Module\ModuleManager::checkActivatedOrForward('CACHE_MANAGER');

        // Initialization of the frontend template object
        $this->frontpageTemplate = new \Template(TEMPLATE_USER);

        // Initialization of the template cache manager
        $this->templateManager = new \TemplateCacheManager(
            $this->frontpageTemplate->templateBaseDir,
            $this->frontpageTemplate
        );

    }
    /**
     * Lists cache files and perform searches across them
     *
     * @param Request $request the request object
     *
     * @return string the string response
     *
     * @Security("has_role('CACHE_TPL_ADMIN')")
     **/
    public function defaultAction(Request $request)
    {
        list($this->filter, $this->params, $this->page, $this->itemsPerPage) =
            $this->buildFilter($request);

        // Get available cache files
        $caches = $this->templateManager->scan($this->filter);
        if (!is_array($caches)) {
            $caches = array();
        }

        // Build the pager
        $pagination = \Pager::factory(
            array(
                'mode'        => 'Sliding',
                'perPage'     => $this->itemsPerPage,
                'append'      => false,
                'path'        => '',
                'delta'       => 4,
                'clearIfVoid' => true,
                'urlVar'      => 'page',
                'totalItems'  => count($caches),
                'fileName'    => $this->generateUrl(
                    'admin_tpl_manager',
                    array(
                        'items_page'      => $this->itemsPerPage,
                        'section'         => $this->request->query->filter('section', '', FILTER_SANITIZE_STRING),
                        'type'            => $this->request->query->filter('type', '', FILTER_SANITIZE_STRING),
                    )
                ).'&page=%d',
            )
        );

        // Get only cache files within pagination range
        $caches = array_slice($caches, ($this->page-1)*$this->itemsPerPage, $this->itemsPerPage);

        // Get all the information of the available cache files
        $caches = $this->templateManager->parseList($caches);

        // ContentCategoryManager manager to handle categories
        $ccm = \ContentCategoryManager::get_instance();

        list($pkContents, $pkAuthors) = $this->templateManager->getResources($caches);

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
        foreach ($this->templateManager->cacheGroups as $cacheGroup) {
            $categoryName = $ccm->get_title($cacheGroup);
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
                $result = $this->templateManager->delete($itemsCacheIds[$item], $itemsTemplate[$item]);
            }
        } elseif (is_string($itemsCacheIds)) {
            $result = $this->templateManager->delete($itemsCacheIds, $itemsTemplate);
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
     * @param Request $request the request object
     *
     * @return string the result string
     *
     * @Security("has_role('CACHE_TPL_ADMIN')")
     **/
    public function deleteAllAction(Request $request)
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
     **/
    public function configAction(Request $request)
    {
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

            $this->templateManager->saveConfig($config);

            m::add(_('Cache configuration saved successfully.'), m::SUCCESS);

            return $this->redirect($this->generateUrl('admin_tpl_manager_config'));
        } else {
            $config = $this->templateManager->dumpConfig();

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
                        'video'             => _('Frontpage videos'),
                        'video-inner'       => _('Inner video'),
                        'gallery-frontpage' => _('Gallery frontpage'),
                        'gallery-inner'     => _('Gallery Inner'),
                        'poll-frontpage'    => _('Polls frontpage'),
                        'poll-inner'        => _('Poll inner'),
                        'sitemap'           => _('Sitemap'),
                    ),
                    'groupIcon' => array(
                        'frontpages'        => 'home16x16.png',
                        'frontpage-mobile'  => 'phone16x16.png',
                        'articles'          => 'article16x16.png',
                        'articles-mobile'   => 'phone16x16.png',
                        'opinion'           => 'opinion16x16.png',
                        'rss'               => 'rss16x16.png',
                        'video'             => 'video16x16.png',
                        'video-inner'       => 'video16x16.png',
                        'gallery-frontpage' => 'gallery16x16.png',
                        'gallery-inner'     => 'gallery16x16.png',
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
