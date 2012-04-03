<?php

use Onm\Settings as s;

/**
 * Setup app
*/
require_once('../../../bootstrap.php');
require_once('../../session_bootstrap.php');

/**
 * Setup view
*/
$tpl = new \TemplateAdmin(TEMPLATE_ADMIN);

require_once(SITE_LIBS_PATH.'ofc1/open-flash-chart.php');
require_once(SITE_LIBS_PATH.'ofc1/open_flash_chart_object.php');


// Assign a content types for don't reinvent the wheel into template
$tpl->assign('content_types', array(1 => 'Noticia' , 7 => 'Galeria', 9 => 'Video', 4 => 'Opinion', 3 => 'Fichero'));

// Fetch vars
$action = filter_input( INPUT_GET , 'action', FILTER_SANITIZE_STRING, array( 'options' => array( 'default' => 'index' ) ) );
$category = filter_input( INPUT_GET , 'category', FILTER_SANITIZE_STRING, array( 'options' => array( 'default' => '0' ) ) );
if (!isset($_SESSION['desde'])) {$_SESSION['desde'] = 'index';}

// Get all data category
$ccm = ContentCategoryManager::get_instance();
list($parentCategories, $subcat, $datos_cat) = $ccm->getArraysMenu();

// Assign vars to tpl
$tpl->assign( array (
        'category' => $category,
        'subcat' => $subcat,
        'allcategorys' => $parentCategories,
        'datos_cat' => $datos_cat,
    )
);

if(isset($action) ) {
    switch($action) {

        case 'index':
            $tpl->display('statistics/statistics.tpl');
            break;

        case 'getPiwikWidgets':

            $piwik = s::get('piwik');

            foreach ($piwik as $value) {
                if ( !isset($value) || empty($value)) {
                    Application::forward ('/admin/controllers/system_settings/system_settings.php?action=list#external');
                }
            }

            $lang = split('_', s::get('site_language'));

            $httpParamsLastVisits[] = array(
                'module'=>'Widgetize',
                'action'=> 'iframe',
                'moduleToWidgetize' => 'VisitsSummary',
                'actionToWidgetize' => 'index',
                'idSite' => $piwik['page_id'],
                'language' => $lang[0],
                'period' => 'day',
                'date' => 'yesterday',
                'disableLink' => '1',
                'widget' => '1',
                'token_auth' => $piwik['token_auth'],

            );

            $httpParamsPageTitles[] = array(
                'module'=>'Widgetize',
                'action'=> 'iframe',
                'filter_limit' => '10',
                'moduleToWidgetize' => 'Actions',
                'actionToWidgetize' => 'getPageTitles',
                'idSite' => $piwik['page_id'],
                'language' => $lang[0],
                'period' => 'day',
                'date' => 'yesterday',
                'disableLink' => '1',
                'widget' => '1',
                'token_auth' => $piwik['token_auth'],

            );


            $httpParamsListKeywords[] = array(
                'module'=>'Widgetize',
                'action'=> 'iframe',
                'filter_limit' => '10',
                'moduleToWidgetize' => 'Referers',
                'actionToWidgetize' => 'getKeywords',
                'idSite' => $piwik['page_id'],
                'language' => $lang[0],
                'period' => 'day',
                'date' => 'yesterday',
                'disableLink' => '1',
                'widget' => '1',
                'token_auth' => $piwik['token_auth'],

            );


            $httpParamsBestSearchEngines[] = array(
                'module'=>'Widgetize',
                'action'=> 'iframe',
                'filter_limit' => '10',
                'moduleToWidgetize' => 'Referers',
                'actionToWidgetize' => 'getSearchEngines',
                'idSite' => $piwik['page_id'],
                'language' => $lang[0],
                'period' => 'day',
                'date' => 'yesterday',
                'disableLink' => '1',
                'widget' => '1',
                'token_auth' => $piwik['token_auth'],

            );


            $httpParamsExternalWebsites[] = array(
                'module'=>'Widgetize',
                'action'=> 'iframe',
                'filter_limit' => '10',
                'moduleToWidgetize' => 'Referers',
                'actionToWidgetize' => 'getWebsites',
                'idSite' => $piwik['page_id'],
                'language' => $lang[0],
                'period' => 'day',
                'date' => 'yesterday',
                'disableLink' => '1',
                'widget' => '1',
                'token_auth' => $piwik['token_auth'],

            );


            $httpParamsVisitorsBrowsers[] = array(
                'module'=>'Widgetize',
                'action'=> 'iframe',
                'filter_limit' => '10',
                'moduleToWidgetize' => 'UserSettings',
                'actionToWidgetize' => 'getBrowser',
                'idSite' => $piwik['page_id'],
                'language' => $lang[0],
                'period' => 'day',
                'date' => 'yesterday',
                'disableLink' => '1',
                'widget' => '1',
                'token_auth' => $piwik['token_auth'],

            );


            $urlLastVisits = $piwik['server_url'] . '?'.StringUtils::toHttpParams($httpParamsLastVisits);
            $urlPageTitles = $piwik['server_url'] . '?'.StringUtils::toHttpParams($httpParamsPageTitles);
            $urlListKeyword = $piwik['server_url'] . '?'.StringUtils::toHttpParams($httpParamsListKeywords);
            $urlBestSearchEngines = $piwik['server_url'] . '?'.StringUtils::toHttpParams($httpParamsBestSearchEngines);
            $urlExternalWebsites = $piwik['server_url'] . '?'.StringUtils::toHttpParams($httpParamsExternalWebsites);
            $urlVisitorsBrowsers = $piwik['server_url'] . '?'.StringUtils::toHttpParams($httpParamsVisitorsBrowsers);


            $tpl->assign(
                array(
                    'category' => 'piwik_widgets',
                    'last_visits' => $urlLastVisits,
                    'page_titles' => $urlPageTitles,
                    'list_keywords' => $urlListKeyword,
                    'best_search_engines' => $urlBestSearchEngines,
                    'external_websites' => $urlExternalWebsites,
                    'visitors_browsers' => $urlVisitorsBrowsers,
                )
            );

            $tpl->display('statistics/piwik_widgets.tpl');
            break;

        case 'get':

            $days = filter_input( INPUT_GET , 'days', FILTER_VALIDATE_INT );
            $type = filter_input( INPUT_GET , 'type', FILTER_SANITIZE_STRING );
            $page = filter_input( INPUT_GET , 'page', FILTER_VALIDATE_INT, array( 'options' => array( 'default' => 1 ) ) );

            $tiempo = "";
            if ($days<=3) {
                $tiempo = ($days*24)." Horas</h2>";
            } elseif ($days==7) {
                $tiempo = "1 semana";
            } elseif ($days==14) {
                $tiempo = "2 semana";
            } elseif ($days==30) {
                $tiempo = "1 mes";
            }

            if ($type=='viewed') {
                $title = "<h2>".sprintf(_("More seen in %s"), $tiempo)."</h2>";
                $items = Dashboard::getMostViewed('Article',$category,$days);
                StringUtils :: disabled_magic_quotes($items);
                $html_output = Dashboard::viewedTable($items, $title);

            } elseif ($type=='comented') {
                $title = "<h2>".sprintf(_("Most commented %s"), $tiempo)."</h2>";
                $items = Dashboard::getMostComented('Article',$category,$days);
                StringUtils :: disabled_magic_quotes($items);
                $html_output = Dashboard::comentedTable($items, $title);

            } elseif ($type=='voted') {
                $title = "<h2>".sprintf(_("Most voted %s"), $tiempo)."</h2>";
                $items = Dashboard::getMostVoted('Article',$category,$days);
                StringUtils :: disabled_magic_quotes($items);
                $html_output = Dashboard::votedTable($items, $title);
            }

            Application::ajax_out("$html_output");
            break;

        default:
            Application::forward($_SERVER['SCRIPT_NAME'].'?action=index&category='.$category.'&page='.$page);
            break;
    } //switch
} else {
    Application::forward($_SERVER['SCRIPT_NAME'].'?action=index&category='.$category);
}
