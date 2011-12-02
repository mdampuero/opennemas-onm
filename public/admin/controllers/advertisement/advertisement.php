<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use Onm\Settings as s,
    Onm\Message as m;
/**
 * Setup app
*/
require_once(dirname(__FILE__).'/../../../bootstrap.php');
require_once(SITE_ADMIN_PATH.'session_bootstrap.php');

\Onm\Module\ModuleManager::checkActivatedOrForward('ADS_MANAGER');

Acl::checkOrForward('ADVERTISEMENT_ADMIN');

// Register events
require_once('./advertisement_events.php');

/**
 * Setup view
*/
$tpl = new \TemplateAdmin(TEMPLATE_ADMIN);
$tpl->assign('titulo_barra', 'Advertisement Management');
 
function buildFilter($filter) {
    $filters = array();
    $url = array();

    $filters[] = $filter;

    if(isset($_REQUEST['filter']['type_advertisement'])
       && ($_REQUEST['filter']['type_advertisement'] >= 0)) {
        $filters[] = '`type_advertisement`=' . $_REQUEST['filter']['type_advertisement'];

        $url[] = 'filter[type_advertisement]=' . $_REQUEST['filter']['type_advertisement'];
    }

    if(isset($_REQUEST['filter']['available'])
       && ($_REQUEST['filter']['available'] >= 0)) {
        if($_REQUEST['filter']['available']==1) {
            $filters[] = '`available`=1';
        } else {
            $filters[] = '(`available`<>1 OR `available` IS NULL)';
        }

        $url[] = 'filter[available]=' . $_REQUEST['filter']['available'];
    }

    if(isset($_REQUEST['filter']['type'])
       && ($_REQUEST['filter']['type'] >= 0)) {
        // with_script == 1 => is script banner, otherwise is a media banner
        if($_REQUEST['filter']['type']==1) {
            $filters[] = '`with_script`=1';
        } else {
            $filters[] = '(`with_script`<>1 OR `with_script` IS NULL)';
        }

        $url[] = 'filter[type]=' . $_REQUEST['filter']['type'];
    }

    return array( implode(' AND ',$filters), implode('&amp;', $url) );
}

$page = filter_input(INPUT_GET,'page',FILTER_VALIDATE_INT, array('options' => array('default' => 1)));

/******************* GESTION CATEGORIAS  *****************************/
$contentType = Content::getIDContentType('advertisement');

// Sometimes category is array. When create & update advertisement
$category = filter_input(INPUT_GET,'category');
if(empty($category)) {
    $category = filter_input(INPUT_POST,'category',FILTER_VALIDATE_INT, array('options' => array('default' => 0 )));
}
if(is_array($category)){
    $category = array_shift($category);
    
}
$ccm = ContentCategoryManager::get_instance();
list($parentCategories, $subcat, $categoryData) = $ccm->getArraysMenu($category, $contentType);

$tpl->assign('category', $category);

if (!isset($_SESSION['desde'])) {
    $_SESSION['desde'] = 'advertisement';
}

$tpl->assign('subcat', $subcat);
$tpl->assign('allcategorys', $parentCategories);
$tpl->assign('datos_cat', $categoryData);
/******************* GESTION CATEGORIAS  *****************************/

// Get filter and uri with params of list (query_string), remember don't assign to template $params
list($filter, $query_string) = buildFilter('fk_content_categories LIKE \'%' . $category . '%\'');
$tpl->assign('query_string', $query_string);

$action = filter_input( INPUT_POST, 'action' , FILTER_SANITIZE_STRING );
if (!isset($action)) {
    $action = filter_input( INPUT_GET, 'action' , FILTER_SANITIZE_STRING, array('options' => array('default' => 'list')) );
}

switch($action) {

    case 'list':
        // Advertisement map
        $map = Advertisement::$map;
        $tpl->assign('map', $map);

        // Filters
        $map = array('-1' => _("--All--")) + $map;
        $filter_options['type_advertisement'] = $map;
        $filter_options['available'] = array('-1' => _("-- All --"), '0' => _("No published"), '1' => _("Published"));
        $filter_options['type']      = array('-1' => _("-- All --"), '0' => _("Multimedia"), '1' => _("Javascript"));
        $tpl->assign('filter_options', $filter_options);
        
        $cm = new ContentManager();
        // ContentManager::find_pages(<TIPO_CONTENIDO>, <CLAUSE_WHERE>, <CLAUSE_ORDER>,<PAGE>,<ITEMS_PER_PAGE>,<CATEGORY>);
        list($advertisements, $pager)= $cm->find_pages('Advertisement',
                                                       $filter,
                                                       'ORDER BY created DESC ', $page, ITEMS_PAGE);

        $advertisementsCleaned = array();
        foreach($advertisements as $adv) {
            //Distinguir entre flash o no flash
            $img = new Photo($adv->path);
            //$img->read($adv->pk_advertisement);
            if ($img->type_img == "swf"){
                $adv->is_flash = 1;
            } else {
                $adv->is_flash = 0;
            }
            $adv->fk_content_categories = explode(',', $adv->fk_content_categories);

            if(in_array($category, $adv->fk_content_categories)
               or $adv->fk_content_categories == array(0))
            {
                $advertisementsCleaned []= $adv;
            }
        }

        $tpl->assign('paginacion', $pager);
        $tpl->assign('advertisements', $advertisementsCleaned);

        $_SESSION['desde'] = 'advertisement';
        $tpl->display('advertisement/list.tpl');

    break;

    case 'test_script':
        Acl::checkOrForward('ADVERTISEMENT_ADMIN');

        String_Utils::disabled_magic_quotes();
        $tpl->assign('script', $_POST['script']); // ten que vir por POST

        $tpl->display('advertisement/test_script.tpl');

    break;

    case 'new':

        Acl::checkOrForward('ADVERTISEMENT_CREATE');

        $tpl->display('advertisement/new.tpl');

    break;

    case 'read':
        //TODO: Pedir por ajax
        Acl::checkOrForward('ADVERTISEMENT_UPDATE');

        $advertisement = new Advertisement( $_REQUEST['id'] );
        if($advertisement->fk_user != $_SESSION['userid'] && (!Acl::check('CONTENT_OTHER_UPDATE')) ) {
             m::add(_("You can't modify this content because you don't have enought privileges.") );
        }
        $advertisement->fk_content_categories = explode(',', $advertisement->fk_content_categories);
        $tpl->assign('advertisement', $advertisement);

        $adv = $advertisement->img;
 
        if(isset($adv) && (!empty($adv))) {
            //Buscar foto where pk_foto=img1
            $photo1 = new Photo($adv);
        }

        $tpl->assign('photo1', $photo1);

        $tpl->display('advertisement/new.tpl');

    break;

    case 'create':
        Acl::checkOrForward('ADVERTISEMENT_CREATE');

        $_REQUEST['publisher'] = $_SESSION['userid'];
        $_REQUEST['fk_author'] = $_SESSION['userid'];

        $firstCategory = $_REQUEST['category'][0];
        $_REQUEST['category'] = implode(',', $_REQUEST['category']);

        $advertisement = new Advertisement();
        if($advertisement->create( $_REQUEST )) {
            if($_SESSION['desde']=='index_portada') {
                Application::forward('index.php');
            }

            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$firstCategory.'&page='.$page/*.'&'.$query_string*/);
        } else {
            $tpl->assign('errors', $advertisement->errors);
        }

        $tpl->display('advertisement/new.tpl');

    break;

    case 'update':
        Acl::checkOrForward('ADVERTISEMENT_UPDATE');

        $firstCategory = $_REQUEST['category'][0];
        $_REQUEST['category'] = implode(',', $_REQUEST['category']);

        $advertisement = new Advertisement($_REQUEST['id']);
        $_REQUEST['fk_user_last_editor'] = $_SESSION['userid'];

        if(!Acl::isAdmin() && !Acl::check('CONTENT_OTHER_UPDATE') && $advertisement->fk_user != $_SESSION['userid']) {
            m::add(_("You can't modify this content because you don't have enought privileges.") );
             Application::forward($_SERVER['SCRIPT_NAME'].'?action=read&id='.$id);
        } else {
            $advertisement->update( $_REQUEST );
        }
        if($_SESSION['desde']=='index_portada') {
            Application::forward('index.php');
        }

        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$firstCategory.'&page='.$page/*.'&'.$query_string*/);
    break;

    case 'validate':


        $advertisement = null;
        if(empty($_POST["id"])) {
             Acl::checkOrForward('ADVERTISEMENT_CREATE');
            $advertisement = new Advertisement();
            $_POST['publisher'] = $_SESSION['userid'];
            if(!$advertisement->create( $_POST )) {
                $tpl->assign('errors', $advertisement->errors);
            }
        } else {
            Acl::checkOrForward('ADVERTISEMENT_UPDATE');
            $advertisement = new Advertisement($_POST["id"]);
            $_REQUEST['fk_user_last_editor'] = $_SESSION['userid'];
            if(!Acl::isAdmin() && !Acl::check('CONTENT_OTHER_UPDATE') && $advertisement->fk_user != $_SESSION['userid']) {
                m::add(_("You can't modify this content because you don't have enought privileges.") );
                Application::forward($_SERVER['SCRIPT_NAME'].'?action=read&id='.$id);
            } else {
                $advertisement->update( $_REQUEST );
            }
        }

        Application::forward($_SERVER['SCRIPT_NAME'].'?action=read&id='.$advertisement->id.'&'.$query_string);
    break;

    case 'delete':
        Acl::checkOrForward('ADVERTISEMENT_DELETE');

        $advertisement = new Advertisement();
        $advertisement->delete( $_POST['id'],$_SESSION['userid'] );

        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category.'&page='.$page.'&'.$query_string);
    break;

    case 'change_status':

        Acl::checkOrForward('ADVERTISEMENT_AVAILA');

        $advertisement = new Advertisement($_REQUEST['id']);
        //Publicar o no, comprobar num clic
        $status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
        $advertisement->set_status($status,$_SESSION['userid']);

        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category.'&page='.$page.'&'.$query_string);
    break;

    case 'available_status':
        Acl::checkOrForward('ADVERTISEMENT_AVAILA');

        $advertisement = new Advertisement($_REQUEST['id']);
        //Publicar o no, comprobar num clic
        $status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
        $advertisement->set_available($status, $_SESSION['userid']);

        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category.'&page='.$page.'&'.$query_string);
    break;

    case 'mfrontpage':
        Acl::checkOrForward('ADVERTISEMENT_AVAILA');

        if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld'])>0) {
            $fields = $_REQUEST['selected_fld'];

            if(is_array($fields)) {
                foreach($fields as $i ) {
                    $advertisement = new Advertisement($i);
                    $advertisement->set_available($_REQUEST['id'],$_SESSION['userid']);   //Se reutiliza el id para pasar el estatus
                }
            }
        }

        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category.'&page='.$page.'&'.$query_string);
    break;

    case 'mdelete':
        Acl::checkOrForward('ADVERTISEMENT_DELETE');

        if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld'])>0) {
            $fields = $_REQUEST['selected_fld'];
            if(is_array($fields)) {
                foreach($fields as $i ) {
                    $advertisement = new Advertisement($i);
                    $advertisement->delete( $i,$_SESSION['userid'] );
                }
            }
        }

        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category.'&page='.$page.'&'.$query_string);
    break;

    default:
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category.'&page='.$page.'&'.$query_string);
    break;
}
