<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Backend\Controllers;

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
        $this->checkAclOrForward('BACKEND_ADMIN');

        $this->view = new \TemplateAdmin(TEMPLATE_ADMIN);

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
     * @return string the string response
     **/
    public function defaultAction(Request $request)
    {
        list($this->filter, $this->params, $this->page, $this->itemsPerPage) =
            $this->buildFilter($this->request);

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

        $allAuthors  = array();
        $author      = new \Author();
        $all_authors = $author->cache->all_authors(null, 'ORDER BY name');
        foreach ($all_authors as $author) {
            $allAuthors[$author->pk_author] = $author->name;
        }

        $cm            = new \ContentManager();
        $articles      = $cm->getContents($pkContents);
        $articleTitles = array();
        $articleUris   = array();

        // Build information for each article/opinion element
        if (count($articles)>0) {
            foreach ($articles as $article) {

                $articleTitles[$article->pk_content] = $article->title;
                if ($article->fk_content_type == '4') {
                    $authorName = !empty($allAuthors[$article->fk_author])
                        ? $allAuthors[$article->fk_author]:'opinion';
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
            $authorsForContents = $author->find('pk_author IN ('.implode(',', $pkAuthors).')');
            foreach ($authorsForContents as $author) {
                $authors['RSS'.$author->pk_author] = $author->name;
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
                'allAuthors'   => $allAuthors,
                'page'         => $this->page,
                'itemsperpage' => $this->itemsPerPage,
            )
        );
    }

    /**
     * Recreates a template cache
     *
     * @return string the string response
     **/
    public function deleteAction(Request $request)
    {
        $itemsSelected = $this->request->query->get('selected', null);
        $itemsCacheIds = $this->request->query->get('cacheid');
        $itemsTemplate = $this->request->query->get('tpl');

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
    }

    /**
     * Deletes all the frontend cache files
     * DANGER: this action is really CPU expensive
     *
     * @return string the result string
     **/
    public function deleteAllAction(Request $request)
    {
        $this->frontpageTemplate->clearAllCache();

        return $this->redirect($this->generateUrl('admin_tpl_manager'));
    }

    /**
     * Show the configuration form and stores it information
     *
     * @return string the string response
     **/
    public function configAction(Request $request)
    {
        if ($this->request->getMethod() == 'POST') {
            $config = array();

            $cacheGroups             = $this->request->request->get('group');
            $cacheGroupsCacheEnabled = $this->request->request->get('caching');
            $cacheGroupsLifeTime     = $this->request->request->get('cache_lifetime');

            foreach ($cacheGroups as $i => $section) {
                $caching          = (isset($cacheGroupsCacheEnabled[$section]))? 1: 0;
                $cache_lifetime   = intval($cacheGroupsLifeTime[$section]);

                $config[$section] = array(
                    'caching'        => $caching,
                    'cache_lifetime' => $cache_lifetime,
                );
            }

            $this->templateManager->saveConfig($config);

            m::add(_('Cache configuration saved successfully.'), m::SUCCESS);

            return $this->redirect($this->generateUrl('admin_tpl_manager'));
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
     * Cleans the category frontpage given its id
     *
     * @return Response the response object
     **/
    public function cleanFrontpageAction(Request $request)
    {
        $tplManager = new \TemplateCacheManager(TEMPLATE_USER_PATH);
        $category = $this->request->request->filter('category', null, FILTER_SANITIZE_STRING);

        if (isset($category)) {
            $ccm = \ContentCategoryManager::get_instance();
            if ($category != 'home' && $category != 'opinion') {
                $category_name = $ccm->get_name($category);
                $title = $ccm->get_title($category_name);
                $title = sprintf(_("Frontpage for category %s"), $title);
            } elseif ($category == 'opinion') {
                $category_name = 'opinion';
                $title = 'Opinion';
                $tplManager->delete($category_name, 'opinion_frontpage.tpl');
            } else {
                $category_name = 'home';
                $title = _('General frontpage');
            }
            $category_name = preg_replace('/[^a-zA-Z0-9\s]+/', '', $category_name);

            $tplManager->delete($category_name . '|RSS');
            $delete = $tplManager->delete($category_name . '|0');

            $content = "<div class='alert alert-success'>"
                    ."<button class='close' data-dismiss='alert'>×</button>"
                    . _("<strong>{$title}</strong> cache deleted succesfully.")
                ."</div>";
        } else {
            $content = "<div class='alert alert-error'>"
                    ."<button class='close' data-dismiss='alert'>×</button>"
                    ._("There was an error trying to delete the requested cache page.")
                ."</div>";
        }

        return new Response($content);
    }

    /**
     * Builds the search filter for listing the listing cache action
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

