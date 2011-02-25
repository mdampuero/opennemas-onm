<?php
/**
 * This files handles the internal cache to refresh, delete and change them
 *
 * New BSD License
 *
 * @category   Zend
 * @package    Onm
 * @subpackage Wand
 * @copyright  Copyright (c) 2008-2010 OpenHost S.L. (http://www.openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id:$
 * @link       http://framework.zend.com/package/PackageName
 */

require_once('../bootstrap.php');
require_once('./session_bootstrap.php');
require_once(SITE_CORE_PATH.'privileges_check.class.php');

set_time_limit(0);
//ignore_user_abort(true);

/**
 * Check if the user can do Administrative actions
*/
if(!Acl::check('BACKEND_ADMIN')) {
    Acl::deny();
}

/**
 * Setup view
*/
$tpl = new TemplateAdmin(TEMPLATE_ADMIN);
$tpl->assign('titulo_barra', 'Cache manager');

require_once(SITE_LIBS_PATH.'Pager/Pager.php');

/**
 * Utility functions
 */

/**
 * Function to refresh cache for a given cacheid and tpl
 *
 * This function try to delete one particular cache from a given cacheid and tpl
 * by deleting the cache and regenerating it with a curl request.
 *
 * @param $tpl, the tpl cache manager instance
 * @param $cacheid, the id for the cache
 * @param $tpl, the tpl from the cache was generated from
*/
function refreshAction(&$tplManager, $cacheid, $tpl)
{
    $tplManager->delete($cacheid, $tpl);

    $matches = array();
    preg_match('/(?P<category>[^\|]+)\|(?P<resource>[0-9]+)$/', $cacheid, $matches);
    if(($tpl == 'opinion.tpl') && isset($matches['resource'])) { // opinion of author
        $url = SITE_URL . '/controllers/opinion.php?category_name=opinion&opinion_id=' . $matches['resource'] . '&action=read';
    } elseif(($tpl == 'mobile.index.tpl') && isset($matches['resource'])) { // mobile frontpage
        if($matches['category']!='home') {
            $url = SITE_URL . 'mobile/seccion/'.$matches['category'].'/'.$matches['resource'];
        } else {
            $url = SITE_URL . 'mobile/';
        }
    } elseif(($tpl == 'video_frontpage.tpl')) { // video frontpage

        $url = SITE_URL . 'controllers/videos.php?category_name='.$cacheid.'&action=list';

    } elseif(($tpl == 'video_inner.tpl') && isset($matches['resource'])) { // video inner
        $url = SITE_URL . 'controllers/videos.php?id='.$matches['resource'].'&action=inner';

    } elseif(($tpl == 'opinion_index.tpl')) { // opinion frontpage
        $url = SITE_URL . 'controllers/videos.php?category_name='.$cacheid.'&action=list';

    } elseif(($tpl == 'opinion_inner.tpl') && isset($matches['resource'])) { // opinion inner
        $url = SITE_URL . 'controllers/opinion.php?category_name=opinion&opinion_id='.$matches['resource'].'&action=read';

    }elseif(isset($matches['resource'])) {

        if(preg_match('/[0-9]{14,19}/', $matches['resource'])) { // 19 digits then it's a pk_content
            $url = SITE_URL . 'controllers/article.php?article_id='.$matches['resource'].'&action=read&category_name='.$matches['category'];
        } else {

            if($matches['category']!='home') {
                $url = SITE_URL . 'seccion/'.$matches['category'].'/'.$matches['resource'];
            } else {
                $url = SITE_URL;
            }
        }
    }  else {
        preg_match('/(?P<category>[^\|]+)\|RSS(?P<resource>[0-9]*)$/', $cacheid, $matches);
        $url = SITE_URL.'rss/'.$matches['category'].'/'.$matches['resource'];
    }

    $url = preg_replace('/^https:/', 'http:', $url);
    $tplManager->fetch($url);
}

function buildFilter()
{
    /* $_REQUEST['items_page'], $_REQUEST['type'], $_REQUEST['section'], $_REQUEST['page'] */
    $filter = '';
    $params = array();
    if(isset($_REQUEST['section']) && !empty($_REQUEST['section'])) {
        $filter  .= '^'.preg_quote($_REQUEST['section']).'\^.*?';
        $params[] = 'section='.$_REQUEST['section'];
    }

    if(isset($_REQUEST['type']) && !empty($_REQUEST['type'])) {
        $regexp = array(
                        'frontpages' => 'frontpage\.tpl\.php$',
                        'opinions' => 'opinion\.tpl\.php$',
                        'frontpage-opinions' => 'opinion_author_index\.tpl\.php$',
                        'articles' => 'article\.tpl\.php$',
                        'rss' => '\^RSS[0-9]*\^',
                        'mobilepages' => 'frontpage-mobile\.tpl\.php$',
                        'video-frontpage' => 'video_frontpage\.tpl\.php$',
                        'video-inner' => 'video_inner\.tpl\.php$'
                    );
        $filter  .= $regexp[ $_REQUEST['type'] ];
        $params[] = 'type='.$_REQUEST['type'];
    }

    if(isset($_REQUEST['page']) && !empty($_REQUEST['page'])) {
        $params[] = 'page='.$_REQUEST['page'];
        $page = $_REQUEST['page'];
    } else {
        $page = 1;
    }


    $items_page = $_REQUEST['items_page'] = (isset($_REQUEST['items_page']))? intval($_REQUEST['items_page']): 15;
    $items_page = $_REQUEST['items_page'] = ($_REQUEST['items_page']===0)? 1: $_REQUEST['items_page'];
    $params[] = 'items_page='.$_REQUEST['items_page'];
    if(!empty($filter)) {
        $filter = '@'.$filter.'@';
    }

    // return $filter and URI $params
    return array( $filter, implode('&', $params), $page, $items_page);
}

$tplManager = new TemplateCacheManager(TEMPLATE_USER_PATH, new Template(TEMPLATE_USER));
// Get $filter and $params values
list($filter, $params, $page, $items_page) = buildFilter();


// Extract action
$action = (isset($_REQUEST['action']))? $_REQUEST['action']: 'list';
switch($action) {
    case 'update': {
        if(isset($_REQUEST['selected']) && count($_REQUEST['selected'])) {
            foreach($_REQUEST['selected'] as $idx) {
                $expires = preg_replace('@([0-9]{2}:[0-9]{2}) ([0-9]{2})/([0-9]{2})/([0-9]{4})@',
                                        '$1:00 $4-$3-$2',
                                        $_REQUEST['expires'][$idx]);
                $tplManager->update(strtotime($expires), $_REQUEST['cacheid'][$idx], $_REQUEST['tpl'][$idx]);
            }
        }

        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&'.$params);
    } break;

    case 'delete': {
        if(isset($_REQUEST['selected']) && count($_REQUEST['selected'])>0) {
            foreach($_REQUEST['selected'] as $idx) {
                $tplManager->delete($_REQUEST['cacheid'][$idx], $_REQUEST['tpl'][$idx]);
            }
        } else {
            if(isset($_REQUEST['cacheid']) && is_string($_REQUEST['cacheid'])){
                $tplManager->delete($_REQUEST['cacheid'], $_REQUEST['tpl']);
            }
        }

        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&'.$params);
    } break;

    case 'refresh': {

        /**
         * Fetch request variables
        */
        $selectedElements = (isset($_REQUEST['selected']) ? $_REQUEST['selected'] : null );
        $cacheElements = $_REQUEST['cacheid'];
        $tplElements = $_REQUEST['tpl'];

        /**
         * If the user selected elements to refresh try to process the action
        */
        if (isset($selectedElements)) {

            if(count($selectedElements)>0) {
                /**
                 * The selected elements are stored in one array so clean them separately
                */
                foreach($selectedElements as $idx) {
                    if(isset($cacheElements[$idx])) {
                        refreshAction($tplManager, $cacheElements[$idx],  $tplElements[$idx]);
                    }
                }
            } else {
                /**
                 * If just one element was selected try to refresh it individualy
                */
                if(is_string($cacheElements)){
                    refreshAction($tplManager, $cacheElements,  $tplElements);
                }
            }
        } else {
            if(isset($_REQUEST['cacheid']) && is_string($_REQUEST['cacheid'])){
                refreshAction($tplManager, $_REQUEST['cacheid'], $_REQUEST['tpl']);
            }
        }

        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&'.$params);

    } break;

    case 'config': {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $config = array();

            foreach($_REQUEST['group'] as $i => $section) {
                $caching = (isset($_REQUEST['caching'][$section]))? 1: 0;
                $cache_lifetime = intval($_REQUEST['cache_lifetime'][$section]);

                $config[$section] = array(
                    'caching' => $caching,
                    'cache_lifetime' => $cache_lifetime,
                );
            }

            $tplManager->saveConfig($config);

            Application::forward($_SERVER['PHP_SELF'] . '?action=list');
        } else {
            $config = $tplManager->dumpConfig();
            $tpl->assign('config', $config);
            $tpl->assign('groupName', array(
                'frontpages'        => 'Frontpage',
                'frontpage-mobile'  => 'Frontpage mobile version',
                'articles'          => 'Inner Article',
                'articles-mobile'   => 'Inner Article mobile version',
                'opinion'           => 'Inner Opinion',
                'rss'               => 'RSS',
                'video'             => 'Frontpage videos',
                'video-inner'       => 'Inner video',
                'gallery-frontpage'       => 'Gallery frontpage',
                'gallery-inner'       => 'Gallery Inner',
            ));

            $tpl->assign('groupIcon', array(
                'frontpages'        => 'home16x16.png',
                'frontpage-mobile'  => 'phone16x16.png',
                'articles'          => 'article16x16.png',
                'articles-mobile'   => 'phone16x16.png',
                'opinion'           => 'opinion16x16.png',
                'rss'               => 'rss16x16.png',
                'video'             => 'video16x16.png',
                'video-inner'       => 'video16x16.png',
                'gallery-frontpage'       => 'gallery16x16.png',
                'gallery-inner'       => 'gallery16x16.png',
            ));

            $tpl->display('tpl_manager/config.tpl');
            exit(0);
        }
    } break;

    case 'list':
    default: {
        $caches = $tplManager->scan($filter);


        // Pager
        $pager_options = array(
            'mode'        => 'Sliding',
            'perPage'     => $items_page,
            'append'      => false,
            'path'        => '',
            'fileName'    => 'javascript:paginate(%d);',
            'delta'       => 4,
            'clearIfVoid' => true,
            'urlVar'      => 'page',
            'totalItems'  => count($caches),
        );
        $pager = Pager::factory($pager_options);

        $caches = array_slice($caches, ($page-1)*$items_page, $items_page);

        $tplManager->parseList($caches);

        // ContentCategoryManager manager to handle categories
        $ccm = ContentCategoryManager::get_instance();

        list($pk_contents, $pk_authors) = $tplManager->getResources($caches);

        $cm = new ContentManager();
        $articles = $cm->getContents( $pk_contents );
        $articleTitles = array();
        if(count($articles)>0) {
            foreach($articles as $a) {
                $articleTitles[$a->pk_content] = $a->title;
            }
        }

        $authors = array();
        $author = new Author();
        if(count($pk_authors)>0) {
            $it = $author->find('pk_author IN ('.implode(',', $pk_authors).')');
            foreach($it as $a) {
                $authors[ 'RSS'.$a->pk_author ] = $a->name;
            }
        }

        $sections = array();
        foreach($tplManager->cacheGroups as $cacheGroup) {
            $category_name = $ccm->get_title($cacheGroup);
            $sections[ $cacheGroup ] = (empty($category_name))? 'PORTADA': $category_name;
        }

        $tpl->assign('authors', $authors);
        $tpl->assign('paramsUri', $params);
        $tpl->assign('pager', $pager);
        $tpl->assign('sections', $sections);
        $tpl->assign('ccm', $ccm);
        $tpl->assign('titles', $articleTitles);
        $tpl->assign('caches', $caches);
    } break;
}

$tpl->display('tpl_manager/tpl_manager.tpl');
