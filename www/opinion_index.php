<?php
//error_reporting(E_ALL); -> Have a look to the errors and Notice IMPORTANT
require('./config.inc.php');
require_once('./core/application.class.php');

Application::import_libs('*');
$app = Application::load();

// redirect to /mobile/ if it's mobile device request
$app->mobileRouter();

require_once('./core/content_manager.class.php');
require_once('./core/content.class.php');
require_once('./core/opinion.class.php');
require_once('./core/advertisement.class.php');
require_once('./core/rating.class.php');
require_once('./core/comment.class.php');

require('./core/photo.class.php');
require('./core/author.class.php');
require('./core/content_category.class.php');
require('./core/content_category_manager.class.php');

$tpl = new Template(TEMPLATE_USER);

/******************************  CATEGORIES & SUBCATEGORIES  *********************************/


if($_GET['category_name']!="opinion") {
    Application::forward('/home/');
}


// Necesaria esta asignación para que funcione en index_sections.php e o menú
$category_name = $_GET['category_name'];
$ccm = new ContentCategoryManager();
require_once ("index_sections.php");

if(isset($_REQUEST['action']) ) {
    switch($_REQUEST['action']) {
      	case 'read':
      		//Listado de opiniones portada seccion opinion.
            $cm = new ContentManager();
            if(!isset($_GET['pageop'])){
                $page = 0;
            }else{
                $page = $_GET['pageop']-1;
            }
            $editorial = $cm->find('Opinion', 'opinions.type_opinion=1 and contents.available=1 and contents.in_home=1 and contents.content_status=1', 'ORDER BY position ASC, created DESC LIMIT '.($page*2).',2');            
            $director = $cm->find('Opinion','opinions.type_opinion=2 and contents.available=1 and contents.in_home=1 and contents.content_status=1', 'ORDER BY created DESC LIMIT '.$page.',1');
            $aut = new Author($director[0]->fk_author);
            $foto = $aut->get_photo($director[0]->fk_author_img);
            $dir['photo']=$foto->path_img;
            $dir['name'] =$aut->name;
            $tpl->assign('dir', $dir);

            $tpl->assign('editorial', $editorial);
            $tpl->assign('director', $director[0]);

            $items_page=16;
            $_limit='LIMIT '.($page*$items_page).', '.($items_page);
            $params='/seccion/opinion';
            $opinions = $cm->find_listAuthors('opinions.type_opinion=0 and contents.available=1 and contents.content_status=1', 'ORDER BY in_home DESC, position ASC, created DESC '.$_limit);
            $total_opinions = $cm->cache->count('Opinion','opinions.type_opinion=0 and contents.available=1  and contents.content_status=1');
            $paginacion =$cm->create_paginate($total_opinions, $items_page, 4, 'URL', $params);

            $tpl->assign('opinions', $opinions);
            $tpl->assign('paginate',  $paginacion);


        break;

        case 'authors':  //Listado de Opiniones de un autor
            $cm = new ContentManager();
            $items_page=16;
            if(!isset($_GET['page'])){
                $page = 0;
            }else{
                $page = $_GET['page']-1;
            }
            $_limit=' LIMIT '.($page*$items_page).', '.($items_page);
                /* find_listAuthors, find_listAuthorsEditorial QUITAR PAGINACION METER TB LEFT JOIN */
            if($_REQUEST['author_id']==1) { //Editorial
                $opinions = $cm->find_listAuthorsEditorial('contents.available=1  and contents.content_status=1', 'ORDER BY created DESC '.$_limit);
                $total_opinions = $cm->cache->count('Opinion','opinions.type_opinion=1 and contents.available=1  and contents.content_status=1');
                $name_author= 'Editorial';
            } elseif($_REQUEST['author_id']==2 || $_REQUEST['author_id']==58) { //Director
                // Bug #1036 - FRONTEND - Desplegable Opinion - El nombre del director no linka correctamente
                $opinions = $cm->find_listAuthors('opinions.type_opinion=2 and contents.available=1 and contents.content_status=1', 'ORDER BY created DESC '.$_limit);
                 $total_opinions = $cm->cache->count('Opinion','opinions.type_opinion=2 and contents.available=1  and contents.content_status=1');
                $name_author= 'Director';
            } else { //Author
                //necesito saber el count para paginar si es necesario.
                $total_opinions = $cm->count('Opinion','opinions.type_opinion=0 and opinions.fk_author='.($_REQUEST['author_id']).' and contents.available=1  and contents.content_status=1');

                $opinions = $cm->find_listAuthors('opinions.type_opinion=0 and opinions.fk_author='.($_REQUEST['author_id']).' and contents.available=1  and contents.content_status=1','ORDER BY created DESC '.$_limit);
              // list($opinions, $pages)= $cm->find_pages('Opinion', 'available=1 AND opinions.fk_author='.$_REQUEST['author_id'], 'ORDER BY  created DESC,  title ASC ',$_REQUEST['page'],10);
               $aut = new Author($_REQUEST['author_id']);
               $name_author= $aut->name;
            }

          //   $opinions = $cm->paginate_array($opinions,16);
           // $opinions = $cm->paginate_array_num_url($opinions,16,'opinions/opinions_do_autor/'.$_REQUEST['author_id'].'/'.$name_author);

            $params='/opinions/opinions_do_autor/'.$_REQUEST['author_id'].'/'.$name_author;
            $pagination =$cm->create_paginate($total_opinions, $items_page, 2, 'URL', $params);


            $tpl->assign('author_name', $name_author);

            $tpl->assign('pagination_list', $pagination);
            $tpl->assign('opinions', $opinions);
            $tpl->assign('author_id', $_REQUEST['author_id']);
        break;

        case 'list_authors':
            //Listado de autores de la columna2right
            $aut = new Author();
            $todos = $aut->cache->all_authors(NULL,'ORDER BY name');
          //  $cm = new ContentManager();
            if( ereg('.*/seccion/opinion/$',$_SERVER['HTTP_REFERER'])){
                $tpl->assign('list_view', 'min');
             //   $todos = $cm->paginate_num_js($todos, 9, 1, 'get_paginate_authors','NULL');
            }elseif(ereg('.*/opinions/opinions_do_autor/.*',$_SERVER['HTTP_REFERER'])){
              //  $todos = $cm->paginate_num_js($todos, 9, 1, 'get_paginate_authors','NULL');
                $tpl->assign('list_view', 'min');
            } else {
               // $todos = $cm->paginate_num_js($todos, 9, 3, 'get_paginate_authors','NULL');
                $tpl->assign('list_view', 'max');
            }
        //    $tpl->assign('pag_authores', $cm->pager);
            $tpl->assign('todos_pag', $todos);

            $tpl->display('modulo_opinion_lista_xornalistas.tpl');
            exit(0);
        break;
    }
}

$aut = new Author();
$todos = $aut->cache->all_authors(NULL,'ORDER BY name');
$tpl->assign('autores', $todos); //combobox1
$tpl->assign('list_view', 'min');
$tpl->assign('todos_pag', $todos); //combobox2

/*********************************PUBLICIDAD***********************************/
//require_once ("index_advertisement.php");
require_once ("opinion_index_advertisement.php");


/**********************************  CONECTA COLUMN3  ******************************************/
 require_once("conecta_cuadro.php");
/**********************************  CONECTA COLUMN3  ******************************************/

$tpl->assign('MEDIA_IMG_PATH_WEB', MEDIA_IMG_PATH_WEB);

// Visualizar
$tpl->display('opinion_index.tpl');
