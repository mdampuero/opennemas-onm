<?php
use Onm\Settings as s,
    Onm\LayoutManager;
/*
 * This file is part of the Onm package.
 *
 * (c)  Sandra Pereira <sandra@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
require_once('../../../bootstrap.php');
require_once('../../session_bootstrap.php');


$tpl = new TemplateAdmin(TEMPLATE_ADMIN);


$action = filter_input( INPUT_GET, 'action' , FILTER_SANITIZE_STRING, array('options' => array('default' => 'list')) );


switch($action) {
        case 'search_adv':
            /* Buscador en pestaña de contenidos relacionandos*/
            $cm = new ContentManager();
            $mySearch = cSearch::Instance();
            //Transform the input string to search like: 'La via del tren' => '+via +tren'
            $szSourceTags = explode(', ', String_Utils::get_tags($_REQUEST['metadata']));
            $where="available=1 ";
            $search=$mySearch->SearchRelatedContents($szSourceTags, 'Article,Opinion',NULL,$where);
            $szSourceTags = explode(', ', htmlentities(String_Utils::get_tags($_REQUEST['metadata']),NULL,'UTF-8'));
            //Put searched words with diferent color
            $ind = 0; $indice = 0;
            $res = array();
            if ($search) {
                foreach ($search as $res ) {
                    $search[$indice]['metadata'] = htmlentities($search[$indice]['metadata'],NULL, 'UTF-8');
                    for($ind=0; $ind < sizeof($szSourceTags); $ind++){
                        $search[$indice]['title'] = String_Utils::ext_str_ireplace($szSourceTags[$ind], '<b><font color=blue>$1</font></b>', $search[$indice]['title']);
                    }
                    $indice++;
                }
            }

            if(($search) && count($search)>0){
                $params="0,'".$_REQUEST['metadata']."'";
                $search = $cm->paginate_array_num_js($search,20 , 3, "search_adv", $params);
                $pages=$cm->pager;
                $paginas='<p align="center">'.$pages->links.'</p>';
                $div=print_search_related(0, $search);
            } else{
                $div="<h3>No hay noticias sugeridas</h3>";
                $paginas='No hay noticias que se relacionen con las palabras clave: '.$_REQUEST['metadata'];
            }

            Application::ajax_out($div.$paginas);

        break;

        case 'getArticles':
            $items_page = s::get('items_per_page') ?: 20;
            $categoryID = filter_input( INPUT_GET, 'action' , FILTER_SANITIZE_STRING, array('options' => array('default' => 'home')) );
            $pageID = filter_input( INPUT_GET, 'action' , FILTER_SANITIZE_STRING, array('options' => array('default' => 'home')) );
            $cm = new ContentManager();

            list($articles, $pages)= $cm->find_pages('Article', 'fk_content_type=1 and content_status=1 AND available=1 ', 'ORDER BY  starttime DESC,  contents.title ASC ',$page, $items_page,$category);


            $tpl->assign(array('articles'=>$articles,
                                'pages'=>$pages
                        ));

            $html_out = $tpl->fetch("common/content_provider/content_provider.tpl");
            Application::ajax_out($html_out);

        break;



         case 'get_pendientes':
            $cm = new ContentManager();
            if (!isset($_GET['category'])
                || empty($_GET['category'])
                || ($_GET['category'] == 'home')
                || ($_GET['category'] == 'todos')
                || ($_GET['category'] == ' ')
            ) {
                $category = 36;//Galicia
                $datos_cat = $ccm->find('pk_content_category=36', NULL);
            } else {
                $category = $_GET['category'];
                $datos_cat = $ccm->find('pk_content_category='.$category, NULL);
            }

            $categorys=print_menu($allcategorys,$subcat,$datos_cat[0],'pendientes');

            list($articles, $pages)= $cm->find_pages('Article', 'fk_content_type=1 and available=0', 'ORDER BY  created DESC,  contents.title ASC ',$_REQUEST['page'],20,$category);

            $params=$_REQUEST['id'].", 'pendientes',$category";
            $paginacion=$cm->makePagesLinkjs($pages, ' get_div_contents', $params);
            $paginacionV='<p align="center">'.$paginacion .'</p>'	;

            $html_out = print_lists_related($_REQUEST['id'], $articles, 'pendientes_div');
            Application::ajax_out("<h2>Noticias Pendientes:</h2>".$categorys.$html_out.$paginacionV);

        break;


        case 'get_videos':
            $cm = new ContentManager();
            if (!isset($_GET['category'])
                || empty($_GET['category'])
                || ($_GET['category'] == 'home')
                || ($_GET['category'] == 'todos')
                || ($_GET['category'] == ' ')
            ) {
                $category = 36;//Galicia
                $datos_cat = $ccm->find('pk_content_category=36', NULL);
            } else {
                $category = $_GET['category'];
                $datos_cat = $ccm->find('pk_content_category='.$category, NULL);
            }

            $categorys=print_menu($allcategorys,$subcat,$datos_cat[0],'videos');

            list($videos, $pages)= $cm->find_pages('Video', 'available=1', 'ORDER BY  created DESC,  contents.title ASC ',$_REQUEST['page'],20, $category);

            $params=$_REQUEST['id'].", 'videos',0";
            $paginacion=$cm->makePagesLinkjs($pages, ' get_div_contents', $params);
            $paginacionV='<p align="center">'.$paginacion .'</p>'	;

            $html_out = print_lists_related($_REQUEST['id'], $videos, 'videos_div');
            Application::ajax_out("<h2>Videos: </h2>".$categorys.$html_out.$paginacionV);

        break;

        case 'get_albums':
            $cm = new ContentManager();
            if (!isset($_GET['category'])
                || empty($_GET['category'])
                || ($_GET['category'] == 'home')
                || ($_GET['category'] == 'todos')
                || ($_GET['category'] == ' ')
            ) {
                $category = 3;//Album
                $datos_cat = $ccm->find('pk_content_category=3', NULL);
            } else {
                $category = $_GET['category'];
                $datos_cat = $ccm->find('pk_content_category='.$category, NULL);
            }

           $categorys=print_menu($allcategorys,$subcat,$datos_cat[0],'albums');

           list($albums, $pages)= $cm->find_pages('Album', 'available=1  AND fk_content_type=7', 'ORDER BY  created DESC,  contents.title ASC ',$_REQUEST['page'],20,$category);

           $params=$_REQUEST['id'].", 'albums',".$category;
           $paginacion=$cm->makePagesLinkjs($pages, ' get_div_contents', $params);
           $paginacionV='<p align="center">'.$paginacion .'</p>'	;

           $html_out=print_lists_related($_REQUEST['id'], $albums, 'albums_div')	;
           Application::ajax_out("<h2>Galerias:</h2>".$categorys.$html_out.$paginacionV);

        break;
        case 'get_opinions':
            $cm = new ContentManager();
            $menu=print_menu_opinion($_GET['category']);
             list($opinions, $pages)= $cm->find_pages('Opinion', 'content_status=1  and available=1 and type_opinion='.$_GET['category'], 'ORDER BY  created DESC,  contents.title ASC ',$_REQUEST['page'],20);

            $params=$_REQUEST['id'].", 'opinions',".$_GET['category'];
            $paginacion=$cm->makePagesLinkjs($pages, ' get_div_contents', $params);
            $paginacionV='<p align="center">'.$paginacion .'</p>'	;
            $html_out=print_lists_related($_REQUEST['id'], $opinions, 'opinions_div')	;
            Application::ajax_out("<h2>Opiniones:</h2>".$menu.$html_out.$paginacionV);

        break;

        case 'get_adjuntos':
            $cm = new ContentManager();

            $cm = new ContentManager();
            if (!isset($_GET['category'])
                || empty($_GET['category'])
                || ($_GET['category'] == 'home')
                || ($_GET['category'] == 'todos')
                || ($_GET['category'] == ' ')
            ) {
                $category = 36;//Galicia
                $datos_cat = $ccm->find('pk_content_category=36', NULL);
            } else {
                $category = $_GET['category'];
                $datos_cat = $ccm->find('pk_content_category='.$category, NULL);
            }

            $categorys=print_menu($allcategorys,$subcat,$datos_cat[0],'adjuntos');

            list($attaches, $pages)= $cm->find_pages('Attachment', 'content_status=1  AND fk_content_type=3', 'ORDER BY  created DESC,  contents.title ASC ',$_REQUEST['page'],20,$category);

            $params=$_REQUEST['id'].", 'adjuntos',".$category;
            $paginacion=$cm->makePagesLinkjs($pages, ' get_div_contents', $params);
            $paginacionV='<p align="center">'.$paginacion .'</p>'	;

            $html_out=print_lists_related($_REQUEST['id'], $attaches, 'adjuntos_div');
            Application::ajax_out("<h2>Ficheros:</h2>".$categorys.$html_out.$paginacionV);
        break;

        case 'get_categorys_list':

             $allcategorys =$ccm->cache->renderCategoriesTree();
             $data=json_encode($allcategorys);
             header('Content-type: application/json');
             Application::ajax_out($data);


        break;

        case 'reload_menu':
            $cm = new ContentManager();
            if (!isset($_GET['category'])
                || empty($_GET['category'])
                || ($_GET['category'] == 'home')
                || ($_GET['category'] == 'todos')
                || ($_GET['category'] == ' ')
            ) {
                $category = 36;//Galicia
                $datos_cat = $ccm->find('pk_content_category=36', NULL);
            } else {
                $category = $_GET['category'];
                $datos_cat = $ccm->find('pk_content_category='.$category, NULL);
            }
            $tpl->assign('category', $_GET['category']);
            $tpl->assign('home', '');
            $html_out=$tpl->fetch('menu_categorys.tpl');
            Application::ajax_out($html_out);

        break;

        default: {
            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);
        } break;
    } //switch
