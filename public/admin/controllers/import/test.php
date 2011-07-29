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
define('SITE_TMP_PATH',  SITE_PATH.'..'.DS.'tmp'.DS);
$uploaddir  = SITE_TMP_PATH;

if(!file_exists($uploaddir)){
    mkdir($uploaddir);
}


function importXML($XMLFile)
{
    $XMLstr = @file_get_contents($XMLFile);
    $s = simplexml_load_string($XMLstr);

    return $s;
}

 $XMLFile =$uploaddir.'prueba.xml';
 //$XMLFile =$uploaddir.'breve.xml';

 $docXML = importXML($XMLFile);
//var_dump($eltoXML);

  


 function check_label($label){
        $relation = array( "/Antetitulo/"=>"subtitle",
                      "/TextoGeneral/"=>"body",
                      "/Titulo/"=>"title",
                      "/Título Int/"=>"titleInt",
                      "/TituloBreveDespiece/"=>"title",
                      "/TextoBandera/"=>"summary",
                      "/Firma/" => "agency",
                      "/data/" => "agency"
                       );
     foreach($relation as $pattern=>$value) {
         
         if(preg_match($pattern, $label)) {
            return $value;
         }
     }
     
 }

function checkXML($docXML){
    $data =array();
    foreach( $docXML as $nodeXML ) {
        foreach( $nodeXML as $eleto ) {            
            if($eleto->getname()=='meta'){                
                        if($eleto->attributes()->name =='day'){
                            $day =$eleto->attributes()->content;
                        }
                        if($eleto->attributes()->name =='month'){
                            $month =$eleto->attributes()->content;
                        }
                        if($eleto->attributes()->name =='year'){
                            $year = $eleto->attributes()->content;
                        }
                        if($eleto->attributes()->name =='page'){
                            $data['paper_page'] =$eleto->attributes()->content;
                        }
            }else{
                foreach($eleto->attributes() as $a => $b) {
                    if($a == 'class') // Tiene los nombres en el atribute class
                     $field = check_label($b);
                     if(!empty($field) && empty($data[$field])){
                          //El primero que encuentra es con el que se queda
                         $data[$field] = '';
                         foreach ($eleto->p as $span) {
                             foreach ($span as $texto)
                                 $data[$field] .=$texto;
                         }
                     }
                     // Algunos son nodos inferiores
                     if(count($eleto->children())>0) {
                         foreach($eleto->children() as $node) {
                             foreach($node->attributes() as $c=>$d){
                                 $field = check_label($d);
                                 if(!empty($field) && empty($data[$field])) {
                                     // $name=$node->getname();  probar a usar en vez de las etiquetas
                                     $data[$field] = $node->p->span ;
                                 }
                             }
                         }

                    }
                }
            }
        }
    }

    $data['created']= $year.'-'.$month.'-'.$day.' '.'00:00:00' ;

    $data['metadata']="";$data['agency_web']="";$data['img1']="";$data['img1']="";$data['img1_footer']="";
    $data['img2']="";$data['img2_footer']="";$data['with_galery']="";$data['with_galery_int']="";$data['with_comment']="1";
    $data['columns']="1";$data['description']="";$data['fk_video']="";$data['fk_video2']="";$data['footer_video2']="";
    $data['ordenArti']="";$data['ordenArtiInt']="";

   
    $metadata = String_Utils::get_title($data['title']);
    $data['metadata'] = str_replace('-',',',$metadata);

    $data['content_status']=0;
    $data['available']=0;
    $data['frontpage']=0;
    $data['category']=20;

    return ($data);
}


$data =checkXML($docXML);

  