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
use Onm\Settings as s;
/**
 * Setup app
*/
require_once(dirname(__FILE__).'/../../../bootstrap.php');
require_once(SITE_ADMIN_PATH.'session_bootstrap.php');

/**
 * Setup view
*/
$tpl = new TemplateAdmin(TEMPLATE_ADMIN);
$tpl->assign('titulo_barra', 'Import new articles from paper XML');



// TODO : define in settings
define('SITE_TMP_PATH',  SITE_PATH.'..'.DS.'tmp'.DS.'xml'.DS);
$uploaddir  = SITE_TMP_PATH;

if(!file_exists($uploaddir)){
    mkdir($uploaddir);
}


$numCategories = array();
$ccm = new ContentCategoryManager();
$numCategories = $ccm->get_all_categories();

$finales = array(".EFE", ". EFE", ".n", ". n", ".  n", ".l", ". l", ".XdG", ". XdG", ".EP", ". EP");

function decompressZIP($file) {
    $zip = new ZipArchive;

    // open archive
    if ($zip->open($file) !== TRUE) {
        die ("Could not open archive");
    }

    $dataZIP = array();

    // get number of files in archive
    $numFiles = $zip->numFiles;

    // iterate over file list
    // print details of each file
    // DEL REVËS   for ($x=$numFiles; $x>0; $x--) {
    for ($x=0; $x<$numFiles; $x++) {
        $file = $zip->statIndex($x);
        $dataZIP[$x] = $file['name'];
    }

    $zip->extractTo(SITE_ADMIN_TMP_PATH.DS.'xml'.DS);
    $zip->close();
    return $dataZIP;
}

function importXML($XMLFile)
{
    $XMLstr = @file_get_contents($XMLFile);
    $s = simplexml_load_string($XMLstr);

    return $s;
}

function splitBodyInHtmlParagraph($body) {

    $bodyiso = mb_convert_encoding ($body,"UTF-8","ISO-8859-1");

    $bodyisoArray = split("â©", $bodyiso );

    $bodyiso = array();
    foreach ($bodyisoArray as $stringArray => $value) {
        $bodyiso[] = "<p>".$value."</p>";
    }

    foreach ($bodyiso as $stringArray => $value) {
        $bodyutf8 .= mb_convert_encoding ($value,"ISO-8859-1","UTF-8");
    }

    return $bodyutf8;
}


function createArticle($elementXML,$numCategories,$check='1')
{
    $data = array();
    $ccm = ContentCategoryManager::get_instance();

    $data['fk_publisher'] = $_SESSION['userid'];

    $data['subtitle']="";
    $data['agency']="";
    $data['title']="";
    $data['summary']="";
    $data['body']="";
    $data['pk_author'] = $_SESSION['userid'];

    foreach ($elementXML->epigrafe as $texto) {
       $data['subtitle'].= $texto;
    }

    //If article does not have antetitulo, the sistem will introduce article section
    if (empty($data['subtitle'])) {
        $data['subtitle'] = strtoupper($elementXML->seccion);
    }

    foreach ($elementXML->entradilla as $texto ) {
       $data['summary'].= $texto;
    }

    foreach ($elementXML->autor_nombre as $texto) {
       $data['agency'].= $texto ;
    }

    $texto=trim($data['agency']);
    substr_replace($texto ,'',-1);
    $data['agency'] = mb_strtoupper($texto,'UTF-8');

    if(strlen($data['agency']) == 0) {
        $data['agency'] = "Agencias";

    } elseif( $data['agency']=='REDACCIÓN' ||  $data['agency']=='AGENCIAS' ||
             $data['agency']=='REDACCIÓN/AGENCIAS'  ||  $data['agency']=='AXENCIAS' ||
             $data['agency']=='REDACCIÓN/AXENCIAS') {
        $data['agency'] = "Agencias";
    }
    $elementXML->autor_nombre= $elementXML->autor_nombre.' ('.  $data['agency'].')';

    foreach ($elementXML->titulo as $texto) {
       $data['title'].= $texto;
    }
    if (!empty($elementXML->NombreIndesignOriginal)) {
     //XEG00626_1259183965254Art_3.xml, XEG00826Art_1.xml, XEG03626_09Art_1.xml
       $data['paper_page']=substr($elementXML->NombreIndesignOriginal ,3,3);
    }else{
        $data['paper_page']=0;
    }
    $elementXML->page =$data['paper_page'];
    $elementXML->num_article=substr($elementXML['nombre'],-2);

    $stringISO = mb_convert_encoding ($data['title'] , "UTF-8" , "ISO-8859-1");
    $stringISO = preg_replace('/â©/', ' ' , $stringISO);
    $data['title'] = mb_convert_encoding ($stringISO,"ISO-8859-1","UTF-8");

    //Parsing body for including <p> tags

  // $data['body'] = splitBodyInHtmlParagraph($elementXML->textoArticulo);
    $body = splitBodyInHtmlParagraph($elementXML->textoArticulo);
    $pclave = PClave::getInstance();
    $data['body'] = $pclave->replaceTerms( $body , $pclave->cache->getList());

    //Removing strange chars from inDesign importation
    $finales = array(".EFE" => "", ". EFE" => "", ".n" => ".", ". n" => ".", ".  n" => ".",
                     ".l" => "", ". l" => "", ".XdG" => ".", ". XdG" => ".", ".EP" => "", ". EP" => ".",
                     " n</p>" => ".</p>", " l</p>" => ".</p>", " EP</p>" => ".</p>", " XdG</p>" => ".</p>");
    $data['body'] = strtr($data['body'], $finales);
    $current_category = strtolower(String_Utils::normalize_name($elementXML->seccion));


    $data['category'] = $ccm->get_id($current_category);

    //If the system does not recognize the category, send to unknown category
    if (empty($data['category'])) {

        $current_category = 'unknown';

        $data['category'] = 20;
    }else{
 
        $elementXML['category'] = $elementXML['category'].' (Assigned to '.$current_category.')';
    }

    $numCategories[ $current_category ]+=1;

    //Creating article object
    $data['metadata']="";$data['agency_web']="";$data['img1']="";$data['img1']="";$data['img1_footer']="";
    $data['img2']="";$data['img2_footer']="";$data['with_galery']="";$data['with_galery_int']="";$data['with_comment']="1";
    $data['columns']="1";$data['description']="";$data['fk_video']="";$data['fk_video2']="";$data['footer_video2']="";
    $data['ordenArti']="";$data['ordenArtiInt']="";

    $article = new Article();
    $metadata = String_Utils::get_title($data['title']);
    $data['metadata'] = str_replace('-',',',$metadata);

//NEW CODE: The import module imports articles from each category to frontpage directly
    if( $current_category == 'unknown'){
        $data['content_status']=0;
        $data['available']=0;
        $data['frontpage']=0;
    }else{
        if($check==1) { //Importar a pendientes
                $data['content_status']=0;
                $data['available']=0;
                $data['frontpage']=0;
           
        }else{ //directo a portada
                $data['content_status']=1;
                $data['available']=1;
                $data['frontpage']=0;

                //Disponible si pero sin ir a portada directamente
            /*    $data['frontpage']=1;
                if( $numCategories[ $ccm->get_title($current_category) ]==1){
                    //Meter la destacada actual en el placeholder_0_1
                    $cm=new ContentManager();
                    $destacado = $cm->find_by_category('Article',  $data['category'], 'fk_content_type=1 AND content_status=1  AND available=1 AND frontpage=1  AND placeholder="placeholder_0_0" ', 'ORDER BY created DESC' );
                    foreach ($destacado as $art){
                           $old_destacada=new Article($art->id);
                           $params=array('4', 'placeholder_0_1', $art->id);
                           $old_destacada->set_position($params, $_SESSION['userid']);
                    }

                    $data['position']=1;
                    $data['placeholder']='placeholder_0_0';
                }else{
                    $data['position']=$numCategories[ $ccm->get_title($current_category) ];
                    $data['placeholder']='placeholder_0_1';
                }*/

                }
        }

    $article->create( $data );
    return $numCategories;
} #end createArticle

function createOpinion($elementXML,$numCategories,$check='1')
{

            $data['body']="";
            $data['title']="";
            $data['author']="";

            foreach ($elementXML->textoArticulo as $texto) {
               $data['body'].= substr_replace(trim($texto) ,'',-2);
            }

            // $data['body'] = splitBodyInHtmlParagraph($elementXML->textoArticulo);
            $body = splitBodyInHtmlParagraph($elementXML->textoArticulo);
            $pclave = PClave::getInstance();
            $data['body'] = $pclave->replaceTerms( $body , $pclave->cache->getList());

            foreach ($elementXML->titulo as $texto) {
               $data['title'].= $texto;
            }

           if (!empty($elementXML->NombreIndesignOriginal)) {
             //XEG00626_1259183965254Art_3.xml, XEG00826Art_1.xml, XEG03626_09Art_1.xml
               $data['paper_page']=substr($elementXML->NombreIndesignOriginal ,3,3);
            }else{
                $data['paper_page']=0;
            }
            $elementXML->NombreIndesignOriginal=$data['paper_page'];
            //Codigo nombre author y buscar foto
            foreach ($elementXML->autor_nombre as $texto) {
               $name.= $texto." / ";
            }
            $name=strtolower($name);
            $name=String_Utils::normalize_name($name);
            $name=preg_replace('/[\-]+/', '', $name);
            $name=trim($name);

            $au=new Author();
            $authors= $au->list_authors();
            $old_percent=70;
            $cont=0;
            foreach ($authors as $author){
                $author_name=trim(String_Utils::normalize_name($author->name));

                $i=similar_text($author_name,$name,$percen);
                if($percen>$old_percent){
                  //  echo '<br>'.$i.'- '.$percen.' <b>lo encontré </b>-'.$name.'-=-'.$author_name.'-';
                    $author_data[$cont]['percen']= $percen;
                    $author_data[$cont]['name']= $author_name;
                    $author_data[$cont]['original_name']= $author->name;
                    $author_data[$cont]['id']= $author->id;
                    $cont++;
                  $old_percent=$percen;
                }

            }

            $data['fk_author']="";
            $data['fk_author_img']="";
            $data['fk_author_img_widget']="";
            if($author_data){
                $metas_name= $author_data[0]['original_name'];
                rsort($author_data);
                $data['fk_author'] = $author_data[0]['id'];
                $photos = $au->get_author_photos($data['fk_author']);
                foreach($photos as $photo) {
                     if($photo->width < 70) {
                        $data['fk_author_img_widget']=$photo->pk_img;
                     }else{
                          $data['fk_author_img']= $photo->pk_img;
                     }
                }
                $data['type_opinion']='0';
                if(strtolower($author_data[0]['original_name'])=='editorial'){
                     $data['type_opinion']='1';
                }
                 if($check==1) { //Importar a pendientes
                        $data['content_status']=0;
                        $data['available']=0;
                        $data['in_home']=0;
                }else{ //directo a portada
                        $data['content_status']=1;
                        $data['available']=1;
                        $data['in_home']=1;
                }
                $elementXML->autor_nombre = $elementXML->autor_nombre .' ('.$author_data[0]['original_name'].')';
             }else{
                 $data['type_opinion']='0';
                 $elementXML->autor_nombre = $elementXML->autor_nombre .' ( Autor Desconocido )';
                 $data['content_status']=0;
                 $data['available']=0;
                 $data['in_home']=0;
            }


            $data['fk_publisher']=$_SESSION['userid'];
            $data['metadata']="";$data['category']="";$data['description']="";
            $data['with_comment']="1";$data['publisher']="3";
            $metadata = String_Utils::get_title($data['title']);
            $data['metadata'] = str_replace('-',',',$metadata);
            $data['metadata'] = $data['metadata'].', '.$metas_name;

            $opinion = new Opinion();
            $opinion->create( $data );

            $numCategories['opinion']+=1;

            return $numCategories;
} #end createOpinion

if(isset($_REQUEST['action']) ) {
    switch($_REQUEST['action']) {
        case 'import':

            $dateStamp = date('Y') . date ('m') . date ('d');

            if (count($_FILES["file"]["name"]) >= 1 && !empty($_FILES["file"]["name"][0]) ) {

                for($i=0,$j=0;$i<count($_FILES["file"]["name"]);$i++) {

                    $nameFile = $_FILES["file"]["name"][$i];
 
                    $datos=pathinfo($nameFile);//sacamos info del archivo

                    //Preparamos el nuevo nombre YYYYMMDDHHMMSSmmmmmm
                    $extension=$datos['extension'];
                    $t=gettimeofday(); //Sacamos los microsegundos
                    $micro=intval(substr($t['usec'],0,5)); //Le damos formato de 5digitos a los microsegundos

                    $name= date("YmdHis").$micro.".".$extension;

                    if (move_uploaded_file($_FILES["file"]["tmp_name"][$i], $uploaddir.$name)) {

                        $check = !isset($_REQUEST['check_pendientes'][$i])?0:$_REQUEST['check_pendientes'][$i];

                        if ($extension == "zip"){
                            $dataZIP = array();
                            $dataZIP = decompressZIP($uploaddir.$name);

                            @chmod($uploaddir.$name,0775);
                            sort($dataZIP);
                            foreach($dataZIP as $elementZIP) {
                                @chmod($uploaddir.$elementZIP,0775);
                                $eltoXML = importXML ($uploaddir.$elementZIP);
                                $XMLFile[$j]=$elementZIP;

                                if (preg_match("/OPINIÓN|OPINION|opinion|opinión/", $eltoXML->seccion )){
                                    $numCategories=createOpinion($eltoXML,$numCategories,$check);

                                }else{
                                    $numCategories=createArticle($eltoXML,$numCategories,$check);
                                }
                                $dataXML[$j] = $eltoXML;
                                $j++;
                            }
                        }else{
                            $eltoXML = importXML ($uploaddir.$name);
                            $XMLFile[$j]=$nameFile;
                            if (preg_match("/OPINIÓN|OPINION|opinion|opinión/", $eltoXML->seccion)){
                                $numCategories=createOpinion($eltoXML,$numCategories);

                            }else{
                                $numCategories=createArticle($eltoXML,$numCategories,$check);
                            }

                            $dataXML[$j] = $eltoXML;
                            $j++;
                        }

                     }else{
                           echo "<br> Ocurrió algún error al subir el fichero ".$uploaddir.$name." - ".$nameFile." . No pudo guardarse,
                           <br> Compruebe su tamaño (MAX 300 MB)";
                     }
                }


                $tpl->assign('numCategories', $numCategories);
                $tpl->assign('XMLFile', $XMLFile);
                $tpl->assign('dataXML', $dataXML);
                $tpl->assign('action', "import");
                $tpl->assign('total_num', $j);
            }

            //Removed all of temp files in SITE_ADMIN_TMP_PATH
          //  foreach(glob(SITE_ADMIN_TMP_PATH.DS.'xml'.DS.'*.*') as $v){unlink($v);}
        break;

        case 'check':

            $dateStamp = date('Y') . date ('m') . date ('d');

            if (count($_FILES["file"]["name"]) >= 1 && !empty($_FILES["file"]["name"][0]) ) {

                for($i=0,$j=0;$i<count($_FILES["file"]["name"]);$i++) {

                    $nameFile = $_FILES["file"]["name"][$i];
 
                    $datos=pathinfo($nameFile);					 //sacamos inofr del archivo

                    //Preparamos el nuevo nombre YYYYMMDDHHMMSSmmmmmm
                    $extension=$datos['extension'];
                    $t=gettimeofday(); //Sacamos los microsegundos
                    $micro=intval(substr($t['usec'],0,5)); //Le damos formato de 5digitos a los microsegundos

                    $name= date("YmdHis").$micro.".".$extension;

                    if (move_uploaded_file($_FILES["file"]["tmp_name"][$i], $uploaddir.$name)) {

                      if ($extension == "zip")
                        {
                            $dataZIP = array();
                            $dataZIP = decompressZIP($uploaddir.$name);
 
                            sort($dataZIP);
                            foreach($dataZIP as $elementZIP) {
                                @chmod($uploaddir.$elementZIP,0775);
                                $eltoXML = importXML ($uploaddir.$elementZIP);
                                $XMLFile[$j]=$elementZIP;
                                $dataXML[$j] = $eltoXML;
                                $j++;
                            }
                        }
                        else
                        {
                                $eltoXML = importXML ($uploaddir.$name);
                                $XMLFile[$j]=$nameFile;
                                $dataXML[$j] = $eltoXML;
                                $j++;
                        }

                     }else{
                           echo "<br> Ocurrió algún error al subir el fichero ".$uploaddir.$name." - ".$nameFile." . No pudo guardarse,
                           <br> Compruebe su tamaño (MAX 300 MB)";
                     }
                }
                $tpl->assign('XMLFile', $XMLFile);
                $tpl->assign('dataXML', $dataXML);
                $tpl->assign('action', "check");
                $tpl->assign('total_num', $j);

            }
            //Removed all of temp files in SITE_ADMIN_TMP_PATH
         //   foreach(glob(SITE_ADMIN_TMP_PATH.DS.'xml'.DS.'*.*') as $v){unlink($v);}
        break;

	default:
            Application::forward($_SERVER['SCRIPT_NAME']);
	break;
    }

} else {
   $tpl->assign('action', "info");
}

$tpl->assign('formAttrs', 'enctype="multipart/form-data"');
$tpl->display('agency_importer/efe/EFE.tpl');


