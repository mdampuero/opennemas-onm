<?php
/**
 * Handles the actions for the cache manager
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
 * Handles the actions for the cache manager
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

        unset($pkAuthors);

        // Initialize vars
        $cm       = new \ContentManager();
        $contents = $cm->getContents($pkContents);
        $index = 0;
        foreach ($caches as &$cache) {
            $cache['cache_id'] = $cache["category"] . "|" . $cache["resource"];
            $cache['tpl'] = $cache["template"] . ".tpl";
            $cache['id']  = $cache['cache_id']."@".$cache['tpl'];
            $this->identifyCacheType($cache, $contents);

            $index++;
        }

        // new code
        return new JsonResponse(
            array(
                'elements_per_page' => $elementsPerPage,
                'extra'             => array(
                    'caches'       => $caches,
                ),
                'page'              => $page,
                'results'           => $caches,
                'total'             => $total,
            )
        );
    }

    /**
     * Removes a template cache
     *
     * @param Request $request the request object
     *
     * @return string the string response
     *
     * @Security("has_role('CACHE_TPL_ADMIN')")
     *
     * @CheckModuleAccess(module="CACHE_MANAGER")
     **/
    public function removeAction(Request $request)
    {
        // Initialization of the template cache manager
        $this->cacheManager = $this->get('template_cache_manager');
        $this->cacheManager->setSmarty(new \Template(TEMPLATE_USER));

        $itemsSelected = $request->request->get('selected', null);

        $errors = $success = [];
        if (count($itemsSelected) > 0) {
            foreach ($itemsSelected as $item) {
                list($cacheId, $tpl) = explode('@', $item);
                $result = $this->cacheManager->delete($cacheId, $tpl);

                if ($result) {
                    $success[] = array(
                        'id'      => $item,
                        'message' => _('Item deleted successfully'),
                        'type'    => 'success'
                    );
                } else {
                    $errors[] = array(
                        'id'      => $id,
                        'message' => sprintf(_('Unable to delete the item "%s"'), $item),
                        'type'    => 'error'
                    );
                }
            }


        } else {
            $errors[] = array(
                'message' => _('No items selected'),
                'type'    => 'error'
            );
        }

        return new JsonResponse(
            array(
                'messages'  => array_merge($success, $errors),
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
        $section      = null;
        $type         = null;
        $page         = $request->request->get('page', 1);
        $itemsPerPage = $request->request->get('epp', 15);

        if ($request->get('search')) {
            $search  = $request->get('search');

            if (array_key_exists('type', $search)) {
                $type = $search['type'][0]['value'];
            }

            if (array_key_exists('section', $search)) {
                $type = $search['section'][0]['value'];
            }
        }


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
                'sitemap'            => 'sitemap.*\.php'
            );
            $filter  .= $regexp[ $type ];
            $params[] = 'type='.$type;
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
    private function identifyCacheType(&$cache, $contents)
    {
        $url = '';
        $title = _('Unknown cache file');
        $isContent = false;
        $extra = [];

        if (($cache['template'] == 'article')) {
            $type  = 'article';
            $title = _('Article inner').': ';
            $isContent = true;
        } elseif ($cache['template'] == 'mobile-article-inner') {
            $type  = 'mobile';
            $title = _('Mobile article inner').': ';
            $url   .= 'mobile/';
            $isContent = true;
        } elseif ($cache['template'] == 'frontpage-mobile') {
            $type  = 'mobile';
            $title = _('Mobile frontpage').': '.$cache['category'];
            if ($cache['category'] == 'ultimas') {
                $url .= 'mobile/last';
            } else {
                $url .= 'mobile/';
            }
        } elseif ($cache['template'] == 'opinionmobile') {
            $type  = 'mobile';
            $title = _('Mobile opinion inner').': ';
            $url   .= 'mobile/';
            $isContent = true;
        } elseif ($cache['template'] == 'opinion-index') {
            $type  = 'mobile';
            $title = _('Mobile opinion frontpage');
            $url   .= 'mobile/opinion';
        } elseif (in_array($cache['template'], ['video_frontpage', 'video_main_frontpage'])) {
            $type  = 'video';
            $title = _('Video frontpage').': '.$cache['category'];
            $url   = 'video/'.$cache['category'];
        } elseif (in_array($cache['template'], ['video_inner' ])) {
            $type  = 'video';
            $title = _('Video inner').': ';
            $isContent = true;
        } elseif (in_array($cache['template'], ['album_frontpage'])) {
            $type  = 'album';
            $title = _('Album frontpage').': '.$cache['category'];
            $url   = 'album/'.$cache['category'];
        } elseif (in_array($cache['template'], ['album'])) {
            $type  = 'album';
            $title = _('Album inner').': ';
            $isContent = true;
        } elseif (in_array($cache['template'], ['opinion_frontpage'])) {
            $type  = 'opinion';
            $title = _('Opinion frontpage').': '.sprintf(_("Page %s"), $cache['resource']);
            $url   = 'opinion/?page='.$cache['resource'];
        } elseif ($cache['template'] == 'opinion_author_index') {
            $type  = 'opinion';
            $title = _('Opinion frontpage').': '.sprintf(_("Page %s"), $cache['resource']);
            $url   = 'opinion/autor/'.$cache['category'].'/autor?page='.$cache['resource'];
        } elseif (in_array($cache['template'], ['opinion', 'blog_inner'])) {
            $type  = 'opinion';
            $title = _('Opinion inner').': ';
            if ($cache['category'] == 'blog') {
                $title = _('Blog inner').': ';
            }
            $isContent = true;
        } elseif (strtolower($cache['template']) == 'rss') {
            $type  = 'rss';
            $title = _('RSS').': '.$cache['category'];
            $url   = 'rss/'.$cache['category'];
            if ($cache['category'] == 'rssauthor') {
                $title = _('RSS Author').': '.$cache['resource'];
                $url = 'rss/author/'.$cache['resource'];
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
        } elseif (in_array($cache['template'], ['poll'])) {
            $type  = 'poll';
            $title = _('Poll inner').': ';
            $isContent = true;
        } elseif ($cache['template'] == 'poll_frontpage') {
            $type  = 'poll';
            $title = _('Poll frontpage').': '.$cache['category'];
            $url   = 'poll/'.$cache['category'];
        } elseif ($cache['template'] == 'custom_css') {
            $type = 'custom_css';
            $title = _('Custom CSS');
        } elseif ($cache['template'] == 'sitemap') {
            $type = 'sitemap';
            if (empty($cache['resource'])) {
                $cache['resource'] = 'home';
            }
            switch ($cache['resource']) {
                case 'home':
                    $url = 'sitemap.xml.gz';
                    break;

                case 'web':
                    $url = 'sitemapweb.xml.gz';
                    break;

                case 'news':
                    $url = 'sitemapnews.xml.gz';
                    break;
            }
            $title = _('Sitemap: ').$cache['resource'];
        } else {
            $type  = 'unknown';
            $url   = '#notfound';
            $title = _('Unknown cache file');
            $extra = var_export($cache, true);
        }


        if ($isContent) {
            $content = array_filter($contents, function($contentItem) use ($cache) {
                if ($contentItem->id == $cache['resource']) {
                    return $contentItem;
                }
            });
            if (count($content) > 0) {
                $content = array_pop($content);
                $url .= $content->uri;
                $title .= $content->title;
            }
        }

        $cache['type']             = $type;
        $cache['url']              = $url;
        $cache['title']            = $title;
        $cache['extra']            = $extra;
        $cache['expires']          = date('c', $cache['expires']);

        return $cache;
    }
}
