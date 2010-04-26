<?php
/* -*- Mode: PHP; tab-width: 4 -*- */
/**
 * OpenNeMas project
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   OpenNeMas
 * @package    OpenNeMas
 * @copyright  Copyright (c) 2009 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
 
if (eregi('mediamanagerController.php', $_SERVER['PHP_SELF'])) {
	die();
}

/**
 * mediamanagerController
 * 
 * @package    OpenNeMas
 * @copyright  Copyright (c) 2009 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: mediamanagerController.php 1 2009-11-18 09:40:03Z vifito $
 */
class mediamanagerController { // FIXME: nome das clases a primeira en maiusculas e con camelcase MediaController
    /**
     * Constants for thumbnails resolution
    */
    const THUMB_WIDTH  = 140;
    const THUMB_HEIGHT = 100;
    
    /* View object */
    public $tpl;
    public $category;
    public $parentCategories;
    public $subcat;
    public $category_name;
    public $page;
    public $alert;
    
    public function action_init()
    {
        $this->tpl = new TemplateAdmin(TEMPLATE_ADMIN);
        
        $this->tpl->assign('titulo_barra', 'Gestor de Imágenes');
        
        $ccm = ContentCategoryManager::get_instance();
        $this->category = $_REQUEST['category'];
        
        list($this->parentCategories, $this->subcat, $datos_cat) = $ccm->getArraysMenu();
        if($this->category != 'GLOBAL') {
            $this->category_name = $ccm->categories[$this->category]->name;
        }
        $this->tpl->assign('subcat', $this->subcat);
        $this->tpl->assign('allcategorys', $this->parentCategories);
        $this->tpl->assign('datos_cat', $datos_cat);
    }

    //List and count photos in categorys.
    public function action_list_categorys()
    {
        $ccm = ContentCategoryManager::get_instance();
        $nameCat = 'GLOBAL'; //Se mete en litter pq category 0
        $fullcat = $ccm->order_by_posmenu($ccm->categories);
        $photoSet = $ccm->data_media_by_type_group('media_type="image"');
        $photoSetJPG = $ccm->count_media_by_type_group('media_type="image" and type_img="jpg"');
        $photoSetGIF = $ccm->count_media_by_type_group('media_type="image" and type_img="gif"');
        $photoSetPNG = $ccm->count_media_by_type_group('media_type="image" and type_img="png"');
        $photoSetBN = $ccm->count_media_by_type_group('media_type="image" and color="BN"');

       // var_dump($photoSet);
        $num_sub_photos = array();
        
        foreach($this->parentCategories as $k => $v) {
            if(isset($photoSet[$v->pk_content_category])) {
                $num_photos[$k] = $photoSet[$v->pk_content_category];
                $num_photos[$k]->jpg = $photoSetJPG[$v->pk_content_category];
                $num_photos[$k]->gif = $photoSetGIF[$v->pk_content_category];
                $num_photos[$k]->png = $photoSetPNG[$v->pk_content_category];
                $num_photos[$k]->other = $photoSet[$v->pk_content_category]->total - $photoSetJPG[$v->pk_content_category] - $photoSetGIF[$v->pk_content_category] - $photoSetPNG[$v->pk_content_category];
                $num_photos[$k]->BN = $photoSetBN[$v->pk_content_category];
                $num_photos[$k]->color = $photoSet[$v->pk_content_category]->total - $photoSetBN[$v->pk_content_category];
            }
            $j=0;
            foreach($fullcat as $child) {
                if(($v->pk_content_category == $child->fk_content_category) &&
                        isset($photoSet[$child->pk_content_category])) {                        
                            $num_sub_photos[$k][$j] = $photoSet[$child->pk_content_category];
                            $num_sub_photos[$k][$j]->jpg = $photoSetJPG[$child->pk_content_category];
                            $num_sub_photos[$k][$j]->gif = $photoSetGIF[$child->pk_content_category];
                            $num_sub_photos[$k][$j]->png = $photoSetPNG[$child->pk_content_category];
                            $num_sub_photos[$k][$j]->other = $photoSet[$child->pk_content_category]->total - $photoSetJPG[$child->pk_content_category] - $photoSetGIF[$child->pk_content_category] - $photoSetPNG[$child->pk_content_category];
                            $num_sub_photos[$k][$j]->BN = $photoSetBN[$child->pk_content_category];
                            $num_sub_photos[$k][$j]->color = $photoSet[$child->pk_content_category]->total - $photoSetBN[$child->pk_content_category];
                            $j++;                           
                }
            }
        }
       
        //Categorias especiales
        $j = 0;
        
        // FIXME: eliminar as dependencias xeradas por un mal deseño
        $especials = array(3 => 'album', 2 => 'publicidad');
        foreach($especials as $key=>$cat) {
            $num_especials[$j]->title = $cat;
            $num_especials[$j]->total = $photoSet[$key]->total;
            $num_especials[$j]->size = $photoSet[$key]->size;
            $num_especials[$j]->jpg = $photoSetJPG[$key];
            $num_especials[$j]->gif = $photoSetGIF[$key];
            $num_especials[$j]->png = $photoSetPNG[$key];
            $num_especials[$j]->other = $photoSet[$key]->total - $photoSetJPG[$key] - $photoSetGIF[$key] - $photoSetPNG[$key];
            $num_especials[$j]->BN = $photoSetBN[$key];
            $num_especials[$j]->color = $photoSet[$key]->total - $photoSetBN[$key];

            $j++;
        }
        
        $this->tpl->assign('categorys', $this->parentCategories);
        $this->tpl->assign('subcategorys', $this->subcat);
        $this->tpl->assign('num_photos', $num_photos);
        $this->tpl->assign('num_sub_photos', $num_sub_photos);
        $this->tpl->assign('especials', $especials);
        $this->tpl->assign('num_especials', $num_especials);
        $_SESSION['where']!='';
        $_SESSION['desde']!='list_categorys';
    }
    
    
    public function process_photos($photos)
    {
        //Recorremos para comprobar si están sino mostramos default
        if($photos) {
            foreach($photos as $photo) {
                if(!file_exists(MEDIA_IMG_PATH . $photo->path_file . $photo->name)){
                    $photo->content_status = 0;
                    $ph = new Photo($photo->pk_photo);
                    $ph->set_available(0, $_SESSION['userid']);
                }
            }            
            
            $this->tpl->assign('photo', $photos);                
            $this->tpl->assign('MEDIA_IMG_PATH', MEDIA_IMG_PATH_WEB);
        }            
    }
    
    
    public function action_list_today()
    {        
        $cm = new ContentManager();
        $ayer = 'DATE_SUB(CURDATE(), INTERVAL 1 DAY)';
        $page = (isset($_REQUEST['page']))? $_REQUEST['page']: 0;
        
        list($photos, $pager)= $cm->find_pages('Photo',
                                               'contents.fk_content_type=8 and photos.media_type="image" and created >=' .
                                               $ayer.' ',
                                               'ORDER BY created DESC ',
                                               $page, 56, $this->category);
        
        foreach($photos as $photo) {
            $photo->description_utf = html_entity_decode(($photo->description));
            $photo->metadata_utf = html_entity_decode($photo->metadata);
            $extension = strtolower($photo->type_img);
            
            if( (!file_exists(dirname(__FILE__)  . $photo->path_file . '140x100-' . $photo->name))
                && file_exists(dirname(__FILE__)  . $photo->path_file . $photo->name)){
                
                if(preg_match('/^(jpeg|jpg|gif|png)$/i', $extension)) {
                    if(is_readable(dirname(__FILE__) .MEDIA_IMG_PATH. $photo->path_file . $photo->name)){
                        $thumb = new Imagick(dirname(__FILE__)  .MEDIA_IMG_PATH. $photo->path_file . $photo->name);
                        
                        $thumb->thumbnailImage(self::THUMB_WIDTH, self::THUMB_HEIGHT, true);
                        
                        // Write the new image to a file
                        $thumb->writeImage(dirname(__FILE__) .
                                           $photo->path_file . self::THUMB_WIDTH . 'x' . self::THUMB_HEIGHT .
                                           '-' . $photo->name);                    
                    }
                }
            }
        }
        
        $_SESSION['desde'] = 'list_today';
        $_SESSION['where']!='';
        $this->process_photos($photos);
        $this->tpl->assign('paginacion', $pager);
    }


    public function action_list_all()
    {
        $cm = new ContentManager();
        $page = (isset($_REQUEST['page']))? $_REQUEST['page']: 0;
        list($photos, $pager) = $cm->find_pages('Photo', 'contents.fk_content_type=8 and photos.media_type="image"',
                                                'ORDER BY  created DESC ', $page, 56, $this->category);
        foreach($photos as $photo) {
            $extension = strtolower($photo->type_img);
            $photo->description_utf = html_entity_decode(($photo->description));
            $photo->metadata_utf = html_entity_decode($photo->metadata);
            
            if((!file_exists(dirname(__FILE__) .  $photo->path_file . '140x100-' . $photo->name))
               && file_exists(dirname(__FILE__) .  $photo->path_file . $photo->name)){
                
                if(preg_match('/^(jpeg|jpg|gif|png)$/', $extension)){
                    if(is_readable(dirname(__FILE__) . $photo->path_file . $photo->name)){
                        $thumb = new Imagick(dirname(__FILE__) .
                                             $photo->path_file . $photo->name);
                        
                        $thumb->thumbnailImage(self::THUMB_WIDTH, self::THUMB_HEIGHT, true);
                        
                        //Write the new image to a file
                        $thumb->writeImage(dirname(__FILE__) . 
                                           $photo->path_file . self::THUMB_WIDTH . 'x' . self::THUMB_HEIGHT .
                                           '-' . $photo->name);
                        //logMessage( $photo[0]->path_file.' -> ' . $photo[0]->name);                        
                    }
                }
            }
        }
        
        $_SESSION['desde'] = 'list_all';
        $_SESSION['where']!='';
        $this->process_photos($photos);
        $this->tpl->assign('paginacion', $pager);
    }

    public function action_save_data()
    {
        $id = $_REQUEST['id'];
        $ph = new Photo($id);

        //Pq se pasa un array multiple de cuando son subidas multiples.
        $_REQUEST['title'] = $ph->title;
       // $_REQUEST['category'] = $ph->category;
        $_REQUEST['metadata'] = $_REQUEST['metadata'][$id];
        $_REQUEST['description'] = $_REQUEST['description'][$id];
        $_REQUEST['author_name'] = $_REQUEST['author_name'][$id];
        $_REQUEST['color'] = $_REQUEST['color'][$id];
        $_REQUEST['date'] = $_REQUEST['fecha'][$id];
        $_REQUEST['resolution'] = $_REQUEST['resolution'][$id];
        $_REQUEST['address'] = $_REQUEST['address'][$id];
       
        $ph->set_data($_REQUEST);
    }

    public function action_read_image_data()
    {
        $foto = new Photo($_REQUEST['id']);
        $foto_data = $foto->read_alldata($_REQUEST['id']);
        
        $image = MEDIA_IMG_PATH . $foto->path_file . $foto->name;
        $size = getimagesize($image, $info);
        
        $this->tpl->assign('photo1', $foto_data);
        $this->alert = $foto_data->infor;
    }
  
    public function action_addPhoto()
    {
        $fallos = "";
        $uploads = array();
        
        for($i=0; $i < count($_FILES["file"]["name"]); $i++) {
            $nameFile = $_FILES["file"]["name"][$i];
            
            if(!empty($nameFile)) {
                $data['fk_category'] = $this->category;
                $data['category'] = $this->category;

               // Check upload directory
                $dir_date =date("/Y/m/d/");
                $uploaddir = MEDIA_PATH.MEDIA_IMG_DIR.$dir_date ;
                
                if(!is_dir($uploaddir)) {
                    FilesManager::createDirectory($uploaddir);
                }                
                $datos = pathinfo($nameFile);     //sacamos infor del archivo
                
                //Preparamos el nuevo nombre YYYYMMDDHHMMSSmmmmmm
                $extension = strtolower($datos['extension']);
                $t = gettimeofday(); //Sacamos los microsegundos
                $micro = intval(substr($t['usec'], 0, 5)); //Le damos formato de 5digitos a los microsegundos
                $name = date("YmdHis") . $micro . "." . $extension;
                
                if (move_uploaded_file($_FILES["file"]["tmp_name"][$i], $uploaddir.$name)) {
                    $data['title'] = $nameFile;
                    $data['name'] = $name;
                    $data['path_file'] = $dir_date;
                    
                    $data['nameCat'] = $this->category_name; //nombre de la category
                    
                    $infor  = new MediaItem( $uploaddir.$name ); 	//Para sacar todos los datos de la imag
           
                    $data['created'] = date("Y-m-d H:i:s");
                    $data['changed'] = $infor->mtime;
                    $data['date']    = $infor->mtime;
                    $data['size']    = round($infor->size/1024, 2);
                    $data['width']   = $infor->width;
                    $data['height']  = $infor->height;
                    $data['type_img'] = $extension;
                    $data['media_type'] = $_REQUEST['media_type'];
                    
                    // Default values
                    $data['author_name']  = '';
                    $data['pk_author']    = $_SESSION['userid'];
                    $data['fk_publisher'] = $_SESSION['userid'];
                    $data['description']  = '';
                    $data['metadata']     = ''; 
                    
                    $foto = new Photo();
                    $elid = $foto->create($data);
                    
                    if($elid) {
                        if(preg_match('/^(jpeg|jpg|gif|png)$/', $extension)) {
                            // miniatura 
                            $thumb = new Imagick($uploaddir.$name);
                            
                            $thumb->thumbnailImage(self::THUMB_WIDTH, self::THUMB_HEIGHT, true);
                            //Write the new image to a file
                            $thumb->writeImage($uploaddir . self::THUMB_WIDTH . 'x' . self::THUMB_HEIGHT . '-' . $name);
                        }
                    }
                    
                    $uploads[] = $elid;
                } else {
                    $fallos .= " '" . $nameFile . "' ";
                }
            } //if empty
        } //for
        
        $uploads = implode('-',  $uploads);
        $this->alert = $fallos;
        
        return $uploads;
    }

    public function action_view_results()
    {
        $ph = new Photo();
        $uploads = explode("-", $_REQUEST['uploads']);
        if(!empty($uploads)) {
            foreach($uploads as $up){
                $photos[] = $ph->read_alldata($up);
            }
        }
        
        $this->tpl->assign('photo', $photos);
    }

    public function action_updateDatasPhotos()
    {
        $tags = $_REQUEST['metadata'];
        $descript = $_REQUEST['description'];
        $authors = $_REQUEST['author_name'];
        $color = $_REQUEST['color'];
        $date  = $_REQUEST['fecha'];
        $resolution  = $_REQUEST['resolution'];
        $address  = $_REQUEST['address'] ;

        foreach($descript as $id => $descript) {
            $ph = new Photo($id);
            
            $data['id'] = $id ;
            $data['title'] = $ph->title;
            $data['description'] = $descript;
            $data['metadata'] = $tags[$id];
            $data['author_name'] = $authors[$id];
            $data['date'] = $date[$id];
            $data['color'] = $color[$id];
            $data['address'] = $address[$id];
            $data['category'] = $this->category;
           
            $ph->set_data($data);
        }
    }


    public function action_delPhoto(){
        $foto = new Photo($_REQUEST['id']);
        $contents = $foto->is_used($_REQUEST['id']);
        
        if($contents) { //Comprueba si la photo esta usada.
            $cm = new ContentManager();
            $relat = $cm->getContents($contents);
            
            foreach($relat as $content) {
                $msg .= "\n - " . strtoupper($content->content_type) . ": " . $content->title;
            }
            
            $this->alert = $msg;
        } else {
            $foto->delete($_REQUEST['id'], $_SESSION['userid']);
        }
        
        return $foto->name;
    }

    // TODO: cambiar: no hacer así los where para mantener condiciones de búsqueda en paginaciones.
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
        }elseif(!empty($_SESSION['where'])) {
                $where=$_SESSION['where'];

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
            if(!empty($_REQUEST['starttime'])) { $where.=' `photos`.`date` >= "'.$_REQUEST['starttime'].'" AND ' ; $search.='Fecha posterior a '.$_REQUEST['starttime'];}
            if(!empty($_REQUEST['pesoMax'])) { $where.=' `photos`.`size` <= "'.$_REQUEST['pesoMax'].'" AND ' ; $search.='Peso menor que '.$_REQUEST['pesoMax'];}
            if(!empty($_REQUEST['pesoMin'])) { $where.=' `photos`.`size` >= "'.$_REQUEST['pesoMin'].'" AND ' ; $search.='Peso mayor que '.$_REQUEST['pesoMin'];}
            if(!empty($_REQUEST['tipo'])) { $where.=' `photos`.`type_img` = "'.$_REQUEST['tipo'].'" AND ' ; $search.='De tipo '.$_REQUEST['tipo'];}
            if(!empty($_REQUEST['color'])) { $where.=' `photos`.`color` = "'.$_REQUEST['color'].'" AND ' ; $search.='Con color '.$_REQUEST['color'];}
            $where.=' 1=1 ';
            $_SESSION['where']=$where;
        }
   
        list($photos, $pager) = $cm->find_pages('Photo', 'contents.fk_content_type=8 and photos.media_type="image" AND '.$where,
                                                'ORDER BY  created DESC ', $page, $items_page, $category);
     
        foreach($photos as $photo) {
            $extension = strtolower($photo->type_img);
            $photo->description_utf = html_entity_decode(($photo->description));
            $photo->metadata_utf = html_entity_decode($photo->metadata);

            if((!file_exists(dirname(__FILE__) .  $photo->path_file . '140x100-' . $photo->name))
               && file_exists(dirname(__FILE__) . $photo->path_file . $photo->name)){

                if(preg_match('/^(jpeg|jpg|gif|png)$/', $extension)){
                    if(is_readable(dirname(__FILE__) .$photo->path_file . $photo->name)){
                        $thumb = new Imagick(dirname(__FILE__) . '/../www/media/images' .
                                             $photo->path_file . $photo->name);

                        $thumb->thumbnailImage(self::THUMB_WIDTH, self::THUMB_HEIGHT, true);

                        //Write the new image to a file
                        $thumb->writeImage(dirname(__FILE__) .
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
            'fileName'    => 'mediamanager.php?action=searchResult&page=%d&category='.$_REQUEST['categ'].'&where='.base64_encode($where),
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
