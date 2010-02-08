<?php
if (eregi('mediagraficosController.php', $_SERVER['PHP_SELF'])) {
	die();
}

/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class mediagraficosController extends mediamanagerController{
    /* View object */
 
 function action_init()
    {
        $this->tpl = new TemplateAdmin(TEMPLATE_ADMIN);

        $this->tpl->assign('titulo_barra', 'Gestor de Gr&aacute;ficos');


        $ccm = ContentCategoryManager::get_instance();
       
         $this->category=$_REQUEST['category'];
        list($this->parentCategories, $this->subcat, $datos_cat) = $ccm->getArraysMenu();
        $this->category_name= $ccm->categories[$this->category]->name;
   
        $this->tpl->assign('subcat', $this->subcat);
        $this->tpl->assign('allcategorys', $this->parentCategories);
        $this->tpl->assign('datos_cat', $datos_cat);

       


    }
  
   //List and count photos in categorys.
     function action_list_categorys(){        
            $ccm = ContentCategoryManager::get_instance();
            $nameCat='GLOBAL'; //Se mete en litter pq category 0
            $fullcat = $ccm->order_by_posmenu($ccm->categories);
            $photoSet = $ccm->data_media_by_type_group('photos.media_type="graphic"');
            $photoSetJPG = $ccm->count_media_by_type_group('media_type="graphic" and type_img="jpg"');
            $photoSetGIF = $ccm->count_media_by_type_group('media_type="graphic" and type_img="gif"');
            $photoSetPNG = $ccm->count_media_by_type_group('media_type="graphic" and type_img="png"');
            $photoSetBN = $ccm->count_media_by_type_group('media_type="graphic" and color="BN"');

 
            foreach($this->parentCategories as $k => $v) {
                $num_photos[$k] = $photoSet[$v->pk_content_category];
                $num_photos[$k]->jpg = $photoSetJPG[$v->pk_content_category];
                $num_photos[$k]->gif = $photoSetGIF[$v->pk_content_category];
                $num_photos[$k]->png = $photoSetPNG[$v->pk_content_category];
                $num_photos[$k]->other = $photoSet[$v->pk_content_category]->total - $photoSetJPG[$v->pk_content_category] - $photoSetGIF[$v->pk_content_category] - $photoSetPNG[$v->pk_content_category];
                $num_photos[$k]->BN = $photoSetBN[$v->pk_content_category];
                $num_photos[$k]->color = $photoSet[$v->pk_content_category]->total - $photoSetBN[$v->pk_content_category];


                foreach($fullcat as $child) {
                    if($v->pk_content_category == $child->fk_content_category &&
                        isset($photoSet[$child->pk_content_category])) {
                            $num_sub_photos[$k][] = $photoSet[$child->pk_content_category];
                            $num_sub_photos[$k][]->jpg = $photoSetJPG[$child->pk_content_category];
                            $num_sub_photos[$k][]->gif = $photoSetGIF[$child->pk_content_category];
                            $num_sub_photos[$k][]->png = $photoSetPNG[$child->pk_content_category];
                            $num_sub_photos[$k][]->other = $photoSet[$child->pk_content_category]->total - $photoSetJPG[$child->pk_content_category] - $photoSetGIF[$child->pk_content_category] - $photoSetPNG[$child->pk_content_category];
                            $num_sub_photos[$k][]->BN = $photoSetBN[$child->pk_content_category];
                            $num_sub_photos[$k][]->color = $photoSet[$child->pk_content_category]->total - $photoSetBN[$child->pk_content_category];

                    }
                }
            }
            //Categorias especiales
            $j=0;

            $this->tpl->assign('categorys', $this->parentCategories);
            $this->tpl->assign('subcategorys', $this->subcat);
            $this->tpl->assign('num_photos', $num_photos);
            $this->tpl->assign('num_sub_photos', $num_sub_photos);

    }



    function action_list_today(){
            $cm = new ContentManager();
            $ayer='DATE_SUB(CURDATE(), INTERVAL 1 DAY)';
            list($photos, $pager)= $cm->find_pages('Photo', 'contents.fk_content_type=8 and created >='.$ayer.'  and photos.media_type="graphic"', 'ORDER BY  created DESC ',$_REQUEST['page'], 56, $this->category);
            $_SESSION['desde']='list_today';
            $this->process_photos($photos);
            $this->tpl->assign('paginacion', $pager);
    }


    function action_list_all(){
        $cm = new ContentManager();
        list($photos, $pager)= $cm->find_pages('Photo', 'contents.fk_content_type=8 and photos.media_type="graphic"', 'ORDER BY  created DESC ',$_REQUEST['page'], 56, $this->category);
        $_SESSION['desde']='list_all';
        $this->process_photos($photos);
        $this->tpl->assign('paginacion', $pager);
    }

    public function action_searchResult()
    {
        $cm = new ContentManager();
        $items_page=56;
        $page=$_REQUEST['page'];
        $_REQUEST['action']='searchResult';

        if(!empty($_REQUEST['where'])){
           $where=base64_decode($_REQUEST['where']);
          
           $category= $_REQUEST['category'];
           $_REQUEST['categ']= $_REQUEST['category'];
        }else{
            $where='';
            $search.='Resultado de la Búsqueda: ';
            if(!empty($_REQUEST['stringSearch'])) {
                $stringSearch=preg_replace('/[\ ]+/', '%',$_REQUEST['stringSearch']);
                $stringSearch = preg_replace('/[\,]+/', '%', $stringSearch);
                $where.='`contents`.`metadata` LIKE "%'.$stringSearch.'%" AND '; $search.='Contiene '.$_REQUEST['stringSearch'];
            }
            if(!empty($_REQUEST['categ']) && $_REQUEST['categ']!='todas') { $category= $_REQUEST['categ']; } else {$category=''; $_REQUEST['category']='todas'; $search.=' De todas las categorias';}
            if(!empty($_REQUEST['author'])) { $where.=' `photos`.`author_name` LIKE "%'.$_REQUEST['author'].'%" AND ' ; $search.=' El autor '.$_REQUEST['author'];}
            if(!empty($_REQUEST['anchoMax'])) { $where.=' `photos`.`width` <= "'.$_REQUEST['anchoMax'].'" AND ' ; $search.=' Ancho menor que '.$_REQUEST['anchoMin'];}
            if(!empty($_REQUEST['altoMax'])) { $where.=' `photos`.`height` <= "'.$_REQUEST['altoMax'].'" AND ' ; $search.='Alto menor que '.$_REQUEST['altoMin'];}
            if(!empty($_REQUEST['anchoMin'])) { $where.=' `photos`.`width` >= "'.$_REQUEST['anchoMin'].'" AND ' ; $search.=' Ancho mayor que '.$_REQUEST['anchoMin'];}
            if(!empty($_REQUEST['altoMin'])) { $where.=' `photos`.`height` >= "'.$_REQUEST['altoMin'].'" AND ' ; $search.='Alto mayor que '.$_REQUEST['altoMin'];}
            if(!empty($_REQUEST['endtime'])) { $where.=' `photos`.`date` <= "'.$_REQUEST['endtime'].'" AND ' ; $search.='Fecha anterior a '.$_REQUEST['endtime'];}
            if(!empty($_REQUEST['startime'])) { $where.=' `photos`.`date` >= "'.$_REQUEST['startime'].'" AND ' ; $search.='Fecha posterior a '.$_REQUEST['startime'];}
            if(!empty($_REQUEST['pesoMax'])) { $where.=' `photos`.`size` <= "'.$_REQUEST['pesoMax'].'" AND ' ; $search.='Peso menor que '.$_REQUEST['pesoMax'];}
            if(!empty($_REQUEST['pesoMin'])) { $where.=' `photos`.`size` >= "'.$_REQUEST['pesoMin'].'" AND ' ; $search.='Peso mayor que '.$_REQUEST['pesoMin'];}
            if(!empty($_REQUEST['tipo'])) { $where.=' `photos`.`type_img` = "'.$_REQUEST['tipo'].'" AND ' ; $search.='De tipo '.$_REQUEST['tipo'];}
            if(!empty($_REQUEST['color'])) { $where.=' `photos`.`color` <= "'.$_REQUEST['color'].'" AND ' ; $search.='Con color '.$_REQUEST['color'];}
            $where.=' 1=1 ';
        }
        list($photos, $pager) = $cm->find_pages('Photo', 'contents.fk_content_type=8 and photos.media_type="graphic" AND '.$where,
                                                'ORDER BY  created DESC ', $page, $items_page, $category);

        foreach($photos as $photo) {
            $extension = strtolower($photo->type_img);
            $photo->description_utf = html_entity_decode(($photo->description));
            $photo->metadata_utf = html_entity_decode($photo->metadata);

            if((!file_exists(dirname(__FILE__) . '/../www/media/images' . $photo->path_file . '140x100-' . $photo->name))
               && file_exists(dirname(__FILE__) . '/../www/media/images' . $photo->path_file . $photo->name)){

                if(preg_match('/^(jpeg|jpg|gif|png)$/', $extension)){
                    if(is_readable(dirname(__FILE__) . '/../www/media/images' . $photo->path_file . $photo->name)){
                        $thumb = new Imagick(dirname(__FILE__) . '/../www/media/images' .
                                             $photo->path_file . $photo->name);

                        $thumb->thumbnailImage(self::THUMB_WIDTH, self::THUMB_HEIGHT, true);

                        //Write the new image to a file
                        $thumb->writeImage(dirname(__FILE__) . '/../www/media/images' .
                                           $photo->path_file . self::THUMB_WIDTH . 'x' . self::THUMB_HEIGHT .
                                           '-' . $photo->name);
                        //logMessage( $photo[0]->path_file.' -> ' . $photo[0]->name);
                    }
                }
            }
        }



        $pager_options = array(
            'mode'        => 'Sliding',
            'perPage'     => $items_page,
            'append'      => false,
            'path'        => '',
            'fileName'    => 'mediagraficos.php?action=searchResult&page=%d&category='.$_REQUEST['categ'].'&where='.base64_encode($where),
            'delta'       => 4,
            'clearIfVoid' => true,
            'urlVar'      => 'page',
            'totalItems'  => $pager->_totalItems,
        );
        $pages = Pager::factory($pager_options);

        $_SESSION['desde'] = 'searchResult';
        $this->process_photos($photos);
        $this->tpl->assign('paginacion', $pager);
        $this->tpl->assign('search', $search);
        $this->tpl->assign('where', $where);
        $this->tpl->assign('pages', $pages);


    }


   
}
