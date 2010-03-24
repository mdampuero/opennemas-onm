<?php
error_reporting(E_ALL);
require_once('./config.inc.php');
require_once('./session_bootstrap.php');

set_time_limit(0);
//ignore_user_abort(true);

// Ejemplo para tener objeto global
require_once('core/application.class.php');
Application::import_libs('*');
$app = Application::load();

require_once('core/privileges_check.class.php');
if(!Acl::_('BACKEND_ADMIN')) {
    Acl::deny();
}

$tpl = new TemplateAdmin(TEMPLATE_ADMIN);
$tpl->assign('titulo_barra', 'Gestor de Caché');

require_once('libs/Pager/Pager.php');
require_once('core/content_manager.class.php');
require_once('core/content.class.php');
require_once('core/author.class.php');
require_once('core/content_category.class.php');

/**
 * Utility functions
 */
function refreshAction(&$tplManager, $cacheid, $tpl)
{
    $tplManager->delete($cacheid, $tpl);
    
    $matches = array();
    preg_match('/(?P<category>[^\|]+)\|(?P<resource>[0-9]+)$/', $cacheid, $matches);
    if(($tpl == 'opinion.tpl') && isset($matches['resource'])) { // opinion of author
        $url = URL_PUBLIC . '/opinion.php?category_name=opinion&opinion_id=' . $matches['resource'] . '&action=read';
        
    } elseif(($tpl == 'mobile.index.tpl') && isset($matches['resource'])) { // mobile frontpage
        if($matches['category']!='home') {
            $url = URL_PUBLIC . 'mobile/seccion/'.$matches['category'].'/'.$matches['resource'];
        } else {
            $url = URL_PUBLIC . 'mobile/';
        }
    } elseif(isset($matches['resource'])) {
        
        if(preg_match('/[0-9]{14,19}/', $matches['resource'])) { // 19 digits then it's a pk_content
            $url = URL_PUBLIC . 'article.php?article_id='.$matches['resource'].'&action=read&category_name='.$matches['category'];
            
        } else {
            
            if($matches['category']!='home') {
                $url = URL_PUBLIC . 'seccion/'.$matches['category'].'/'.$matches['resource'];
            } else {
                $url = URL_PUBLIC;
            }
        }
    } else {
        preg_match('/(?P<category>[^\|]+)\|RSS(?P<resource>[0-9]*)$/', $cacheid, $matches);
        $url = URL_PUBLIC.'rss/'.$matches['category'].'/'.$matches['resource'];
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
        $regexp = array('frontpages' => '%frontpage\.tpl$', 'opinions' => '%opinion\.tpl$', 'articles' => '%article\.tpl$', 'rss' => '\^RSS[0-9]*\^', 'mobilepages' => '%mobile\.index\.tpl$');
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
                $expires = preg_replace('@([0-9]{2}:[0-9]{2}) ([0-9]{2})/([0-9]{2})/([0-9]{4})@', '$1:00 $4-$3-$2', $_REQUEST['expires'][$idx]);
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
        if(isset($_REQUEST['selected']) && count($_REQUEST['selected'])>0) {
            foreach($_REQUEST['selected'] as $idx) {
                if(isset($_REQUEST['cacheid'][$idx])) {
                    refreshAction($tplManager, $_REQUEST['cacheid'][$idx],  $_REQUEST['tpl'][$idx]);
                }
            }
        } else {
            if(isset($_REQUEST['cacheid']) && is_string($_REQUEST['cacheid'])){
                refreshAction($tplManager, $_REQUEST['cacheid'],  $_REQUEST['tpl']);
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
            
            Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list');
        } else {
            $config = $tplManager->dumpConfig();            
            $tpl->assign('config', $config);
            $tpl->assign('groupName', array(
                'frontpages'       => 'Portadas',                
                'frontpage-mobile' => 'Portadas versión móvil',
                'articles' => 'Artículo interior',                
                'opinion'  => 'Opinión interior',                
                'rss' => 'RSS',
            ));
            
            $tpl->assign('groupIcon', array(
                'frontpages'       => 'home16x16.png',                
                'frontpage-mobile' => 'phone16x16.png',
                'articles' => 'article16x16.png',
                'opinion' => 'opinion16x16.png',                
                'rss' => 'rss16x16.png'
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