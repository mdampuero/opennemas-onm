<?php
/**
 * Handles the actions for the keywords
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
namespace BackendWebService\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Backend\Annotation\CheckModuleAccess;
use Onm\Framework\Controller\Controller;

/**
 * Handles the actions for the keywords
 *
 * @package Backend_Controllers
 **/
class CacheManagerController extends Controller
{
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
    public function listAction(Request $request)
    {
        // Initialization of the template cache manager
        $this->cacheManager = $this->get('template_cache_manager');
        $this->cacheManager->setSmarty(new \Template(TEMPLATE_USER));

        $elementsPerPage = $request->request->getDigits('elements_per_page', 10);
        $page            = $request->request->getDigits('page', 1);

        list($filter, $params) = $this->getFilters($request);

        // Get available cache files
        $caches = $this->cacheManager->scan($filter);
        if (!is_array($caches)) {
            $caches = array();
        }

        $total = count($caches);

        // Get only cache files within pagination range
        $caches = array_slice($caches, ($page-1)*$elementsPerPage, $elementsPerPage);

        // Get all the information of the available cache files
        $caches = $this->cacheManager->parseList($caches);

        list($pkContents, $pkAuthors) = $this->cacheManager->getResources($caches);

        // Fetch all authors and generate associated array
        $ccm = \ContentCategoryManager::get_instance();
        $allAuthors = \User::getAllUsersAuthors();
        $allAuthorsArray = array();
        foreach ($allAuthors as $author) {
            $allAuthorsArray[$author->id] = $author->name;
        }

        // Initialize vars
        $cm            = new \ContentManager();
        $contents      = $cm->getContents($pkContents);
        $articleTitles = array();
        $articleUris   = array();

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
            $cache['tpl']      = $cache["template"] . ".tpl";
            $this->identifyCacheType($cache, $contents);
        }

        // new code
        return new JsonResponse(
            array(
                'elements_per_page' => $elementsPerPage,
                'extra'             => array(
                    'authors'      => $authors,
                    'sections'     => $sections,
                    // 'titles'       => $articleTitles,
                    // 'contentUris'  => $articleUris,
                    'caches'       => $caches,
                    // 'allAuthors'   => $allAuthorsArray,
                ),
                'page'              => $page,
                'results'           => $caches,
                'total'             => $total,
            )
        );
    }

    /**
     * Builds the search filter for listing the listing cache action
     *
     * @param Request $request the request object
     *
     * @return array
     **/
    private function getFilters($request)
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

    /**
     * undocumented function
     *
     * @return void
     **/
    private function identifyCacheType(&$cache)
    {
        $url = '#notfound';
        $title = 'Unknown cache file';

        if (($cache['template'] == 'article')) {
            $type  = 'article';
            $title = _('Article inner').': '." \n".var_export($cache, true);
            $url   = '/article/bla';
        } elseif (in_array($cache['template'], ['video_inner', 'video_frontpage', 'video_main_frontpage' ])) {
            $type  = 'video';
            $title = _('Video inner').': '." \n".var_export($cache, true);
            $url   = '/article/bla';
        } elseif (in_array($cache['template'], ['album_frontpage', 'album_frontpage'])) {
            $type  = 'album';
            $title = _('Album frontpage').': '." \n".var_export($cache, true);
            $url   = '/album/bla';
        } elseif (in_array($cache['template'], ['album'])) {
            $type  = 'album';
            $title = _('Album inner').': '." \n".var_export($cache, true);
            $url   = '/album/bla';
        } elseif (in_array($cache['template'], ['opinion_author_index', 'opinion_frontpage', 'opinion', 'blog_inner'])) {
            $type  = 'opinion';
            $title = _('Opinion inner').': '." \n".var_export($cache, true);
            $url   = '/opinion/bla';
            if ($cache['category'] == 'blog') {
                $title = _('Blog inner').': '." \n".var_export($cache, true);
            }
        } elseif (strtolower($cache['template']) == 'rss') {
            $type  = 'rss';
            $title = _('RSS').': '." \n".var_export($cache, true);
            $url   = '/rss/bla';
            if ($cache['category'] == 'rssauthor') {
                $title = _('RSS Author').': '." \n".var_export($cache, true);
            }
        } elseif (in_array($cache['template'], ['frontpage'])) {
            $type  = 'frontpage';
            $title = _('Frontpage').': ';
            if ($cache['resource'] == 'home') {
                $url   = '/';
                $title .= _('Homepage');
            } else {
                $url   = '/seccion/'.$cache['resource'];
                $title .= $cache['resource'];
            }
        } elseif (in_array($cache['template'], ['poll_frontpage', 'poll'])) {
            $type  = 'poll';
            $title = _('Poll inner').': '." \n".var_export($cache, true);
            $url   = '/poll/bla';
        } elseif ($cache['template'] == 'custom_css') {
            $type = 'custom_css';
            $title = _('Custom CSS: ')." \n".var_export($cache, true);
        } else {
            $type  = 'unknown';
            $title = _('Unknown cache file')." \n".var_export($cache, true);
        }

        // Missed types books/covers/tags/special/static_pages/

        // if ($cache["template"] == 'opinion_author_index') {
        //     if (preg_match('/([0-9]+)_([0-9]+)/', $cache['resource'], $match)) {
        //         $cache["authorid"] =(int)$match[1];
        //         $cache["page"] =$match[2];
        //     }
        // }

        $cache['type']             = $type;
        $cache['url']              = $url;
        $cache['title']            = $title;

        return $cache;
    }
}
