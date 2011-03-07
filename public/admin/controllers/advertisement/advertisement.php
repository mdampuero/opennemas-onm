<?php


/**
 * Setup app
*/
require_once(dirname(__FILE__).'/../../../bootstrap.php');
require_once(SITE_ADMIN_PATH.'session_bootstrap.php');



// Check ACL
require_once( SITE_CORE_PATH.'privileges_check.class.php' );
if(!Acl::check('ADVERTISEMENT_ADMIN')) {
    Acl::deny();
}

// Register events
require_once('./advertisement_events.php');

/**
 * Setup view
*/
$tpl = new TemplateAdmin(TEMPLATE_ADMIN);
$tpl->assign('titulo_barra', 'Advertisement Management');

$tpl->addScript( array('prototype.js', 'scriptaculous/scriptaculous.js', 'AdPosition.js') );

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

if (!isset($_REQUEST['category'])) {
    $_REQUEST['category'] = 0;
}

if (!isset($_REQUEST['page'])) {
     $_REQUEST['page'] = 1;
}

$tpl->assign('category', $_REQUEST['category']);
if (!isset($_SESSION['desde'])) {
    $_SESSION['desde'] = 'advertisement';
}
$ccm = ContentCategoryManager::get_instance();
list($parentCategories, $subcat, $datos_cat) = $ccm->getArraysMenu();

$tpl->assign('subcat', $subcat);
$tpl->assign('allcategorys', $parentCategories);
$tpl->assign('datos_cat', $datos_cat);

// Get filter and uri with params of list (query_string), remember don't assign to template $params
list($filter, $query_string) = buildFilter('fk_content_categories LIKE \'%' . $_REQUEST['category'] . '%\'');
$tpl->assign('query_string', $query_string);

if( isset($_REQUEST['action']) ) {
    switch($_REQUEST['action']) {
        case 'list': {
            // Advertisement map
            $map = Advertisement::$map;
            $tpl->assign('map', $map);

            // Filters
            $map = array('-1' => _("--Todos--")) + $map;
            $filter_options['type_advertisement'] = $map;
            $filter_options['available'] = array('-1' => _("-- All --"), '0' => _("No published"), '1' => _("Published"));
            $filter_options['type']      = array('-1' => _("-- All --"), '0' => _("Multimedia"), '1' => _("Javascript"));
            $tpl->assign('filter_options', $filter_options);

            $cm = new ContentManager();
            // ContentManager::find_pages(<TIPO_CONTENIDO>, <CLAUSE_WHERE>, <CLAUSE_ORDER>,<PAGE>,<ITEMS_PER_PAGE>,<CATEGORY>);
            list($advertisements, $pager)= $cm->find_pages('Advertisement',
                                                           $filter,
                                                           'ORDER BY created DESC ', $_REQUEST['page'], 20);
            $tpl->assign('paginacion', $pager);
            $tpl->assign('advertisements', $advertisements);

            $_SESSION['desde'] = 'advertisement';
            $tpl->display('advertisement/list.tpl');
            
        } break;

        case 'test_script': {
            if(!Privileges_check::CheckPrivileges('ADVERTISEMENT_ADMIN')) {
                Privileges_check::AccessDeniedAction();
            }

            String_Utils::disabled_magic_quotes();
            $tpl->assign('script', $_POST['script']); // ten que vir por POST

            $tpl->display('advertisement/test_script.tpl');

        } break;

        case 'new':
            
            if(!Privileges_check::CheckPrivileges('ADVERTISEMENT_ADMIN')) {
                Privileges_check::AccessDeniedAction();
            }

            $cm = new ContentManager();
            $photos = $cm->find_by_category('Photo', 2, 'fk_content_type=8 ', 'ORDER BY created DESC');
            foreach($photos as $photo) {
                $photo->content_status = 1;
                $ph = new Photo($photo->pk_photo);
                $ph->set_status(1, $_SESSION['userid']);
            }

            $tpl->assign('MEDIA_IMG_PATH_URL', MEDIA_IMG_PATH_WEB);

            $photos = $cm->paginate_num($photos,16);
            $tpl->assign('photos', $photos);
            $pages = $cm->pager;
            $paginacion = "";

            for($i=1; $i<=($pages->_totalPages); $i++) {
                $paginacion .= ' <a style="cursor:pointer;" onClick="get_advertisements('.$i.')">'.$i.'</a> ';
            }

            if(($pages->_totalPages)>1) {
                $tpl->assign('paginacion', $paginacion);
            }

            $tpl->display('advertisement/advertisement.tpl');

        break;

        case 'read': {
            //habrá que tener en cuenta el tipo
            if(!Privileges_check::CheckPrivileges('ADVERTISEMENT_ADMIN')) {
                Privileges_check::AccessDeniedAction();
            }

            $advertisement = new Advertisement( $_REQUEST['id'] );
            $advertisement->fk_content_categories = explode(',', $advertisement->fk_content_categories);
            $tpl->assign('advertisement', $advertisement);

            $adv = $advertisement->img;

            $cm = new ContentManager();
            if(isset($adv)) {
                //Buscar foto where pk_foto=img1
                $photo1 = new Photo($adv);
            }

            $tpl->assign('photo1', $photo1);

            $photos = $cm->find_by_category('Photo',2, 'fk_content_type=8 ', 'ORDER BY created DESC');
            foreach($photos as $photo){
                $photo->content_status = 1;
                $ph = new Photo($photo->pk_photo);
                $ph->set_status(1,$_SESSION['userid']);
            }

            $tpl->assign('MEDIA_IMG_PATH_URL', MEDIA_IMG_PATH_WEB);
            $photos = $cm->paginate_num($photos,16);
            $tpl->assign('photos', $photos);
            $pages = $cm->pager;
            $paginacion = "";
            for($i=1;$i<=$pages->_totalPages;$i++){
                $paginacion.=' <a style="cursor:pointer;" onClick="get_advertisements('.$i.')">'.$i.'</a> ';
            }
            if($pages->_totalPages>1) {
                 $tpl->assign('paginacion', $paginacion);
            }

            $tpl->display('advertisement/advertisement.tpl');

            /**
                //Noticias relacionadas
                $cm = new ContentManager();
                $articules = $cm->find('Article','content_status=1', 'ORDER BY archive DESC');
                // Agrupa los artículos por categoría y controla si están publicados

                $articles_agrupados = Related_content::sortArticles($articules);
                $tpl->assign('articles_agrupados', $articles_agrupados);


                $rel = new Related_content();
                $relationes = $rel->get_relations( $_REQUEST['id'] );
                $tpl->assign('yarelations', $relationes);
                if($relationes) {
                    $tpl->assign('ya', 1);
                }
            **/
        } break;

        case 'create': {
            if(!Privileges_check::CheckPrivileges('ADVERTISEMENT_ADMIN')) {
                Privileges_check::AccessDeniedAction();
            }
            
            
            $_REQUEST['publisher'] = $_SESSION['userid'];
            $firstCategory = $_REQUEST['category'][0];
            $_REQUEST['category'] = implode(',', $_REQUEST['category']);

            $advertisement = new Advertisement();
            if($advertisement->create( $_REQUEST )) {
                if($_SESSION['desde']=='index_portada') {
                    Application::forward('index.php');
                }

                Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$firstCategory.'&page='.$_REQUEST['page']/*.'&'.$query_string*/);
            } else {
                $tpl->assign('errors', $advertisement->errors);
            }

            $tpl->display('advertisement/advertisement.tpl');

        } break;

        case 'update': {
            if(!Privileges_check::CheckPrivileges('ADVERTISEMENT_ADMIN')) {
                Privileges_check::AccessDeniedAction();
            }
            $firstCategory = $_REQUEST['category'][0];
            $_REQUEST['category'] = implode(',', $_REQUEST['category']);

            $advertisement = new Advertisement();
            $_REQUEST['fk_user_last_editor'] = $_SESSION['userid'];

            $advertisement->update( $_REQUEST );

            if($_SESSION['desde']=='index_portada') {
                Application::forward('index.php');
            }

            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$firstCategory.'&page='.$_REQUEST['page']/*.'&'.$query_string*/);
        } break;

        case 'validate': {
            if(!Privileges_check::CheckPrivileges('ADVERTISEMENT_ADMIN')) {
                Privileges_check::AccessDeniedAction();
            }

            $advertisement = null;
            if(empty($_POST["id"])) {
                $advertisement = new Advertisement();
                $_POST['publisher'] = $_SESSION['userid'];
                if(!$advertisement->create( $_POST )) {
                    $tpl->assign('errors', $advertisement->errors);
                }
            } else {
                $advertisement = new Advertisement($_POST["id"]);
                $_REQUEST['fk_user_last_editor'] = $_SESSION['userid'];
                $advertisement->update( $_REQUEST );
            }

            Application::forward($_SERVER['SCRIPT_NAME'].'?action=read&id='.$advertisement->id.'&'.$query_string);
        } break;

        case 'delete': {
            if(!Privileges_check::CheckPrivileges('ADVERTISEMENT_ADMIN')) {
                Privileges_check::AccessDeniedAction();
            }

            $advertisement = new Advertisement();
            $advertisement->delete( $_POST['id'],$_SESSION['userid'] );

            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category'].'&page='.$_REQUEST['page'].'&'.$query_string);
        } break;

        case 'change_status': {
            if(!Privileges_check::CheckPrivileges('ADVERTISEMENT_ADMIN')) {
                Privileges_check::AccessDeniedAction();
            }

            $advertisement = new Advertisement($_REQUEST['id']);
            //Publicar o no, comprobar num clic
            $status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
            $advertisement->set_status($status,$_SESSION['userid']);

            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category'].'&page='.$_REQUEST['page'].'&'.$query_string);
        } break;

        case 'available_status': {
            if(!Privileges_check::CheckPrivileges('ADVERTISEMENT_ADMIN')) {
                Privileges_check::AccessDeniedAction();
            }

            $advertisement = new Advertisement($_REQUEST['id']);
            //Publicar o no, comprobar num clic
            $status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
            $advertisement->set_available($status, $_SESSION['userid']);

            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category'].'&page='.$_REQUEST['page'].'&'.$query_string);
        } break;

        case 'mfrontpage': {
            if(!Privileges_check::CheckPrivileges('ADVERTISEMENT_ADMIN')) {
                Privileges_check::AccessDeniedAction();
            }

            if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld'])>0) {
                $fields = $_REQUEST['selected_fld'];

                if(is_array($fields)) {
                    foreach($fields as $i ) {
                        $advertisement = new Advertisement($i);
                        $advertisement->set_available($_REQUEST['id'],$_SESSION['userid']);   //Se reutiliza el id para pasar el estatus
                    }
                }
            }

            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category'].'&page='.$_REQUEST['page'].'&'.$query_string);
        } break;

        case 'mdelete': {
            if(!Privileges_check::CheckPrivileges('ADVERTISEMENT_ADMIN')) {
                Privileges_check::AccessDeniedAction();
            }

            if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld'])>0) {
                $fields = $_REQUEST['selected_fld'];
                if(is_array($fields)) {
                    foreach($fields as $i ) {
                        $advertisement = new Advertisement($i);
                        $advertisement->delete( $i,$_SESSION['userid'] );
                    }
                }
            }

            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category'].'&page='.$_REQUEST['page'].'&'.$query_string);
        } break;

        default: {
            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category'].'&page='.$_REQUEST['page'].'&'.$query_string);
        } break;
    }

} else {
    Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category'].'&page='.$_REQUEST['page'].'&'.$query_string);
}
