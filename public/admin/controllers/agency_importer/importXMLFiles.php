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
use Onm\Settings as s,
    Onm\Message  as m;
/**
 * Setup app
*/
require_once(dirname(__FILE__).'/../../../bootstrap.php');
require_once(SITE_ADMIN_PATH.'session_bootstrap.php');

/**
 * Setup view
*/
$tpl = new \TemplateAdmin(TEMPLATE_ADMIN);
$tpl->assign('titulo_barra', 'Import new contents from XML file or Zip XML');

$action = filter_input( INPUT_POST, 'action' , FILTER_SANITIZE_STRING );
if (!isset($action)) {
    $action = filter_input( INPUT_GET, 'action' , FILTER_SANITIZE_STRING, array('options' => array('default' => 'list')) );
}

if (
    is_null(s::get('xml_file_schema'))
    && $action != 'config'
) {
    m::add(_('Please provide XML file schema'));
    $httpParams [] = array(
                        'action'=>'config',
                    );
    Application::forward($_SERVER['SCRIPT_NAME'] . '?'.StringUtils::toHttpParams($httpParams));
}

// TODO : define in settings
define('SITE_TMP_PATH',  SITE_PATH.'..'.DS.'tmp'.DS.'xml'.DS);
$uploaddir  = SITE_TMP_PATH;

if(!file_exists($uploaddir)){
    FilesManager::createDirectory($uploaddir);
}


switch($action) {

    case 'config':


        if ( $schema = s::get('xml_file_schema') ) {

            $tpl->assign(array(
                'title'    => $schema['title'],
                'title_int' => $schema['title_int'],
                'subtitle' => $schema['subtitle'],
                'summary' => $schema['summary'],
                'agency' => $schema['agency'],
                'created' => $schema['created'],
                'metadata'    => $schema['metadata'],
                'description' => $schema['description'],
                'category_name' => $schema['category_name'],
                'body' =>$schema['body'],
                'ignored' =>$schema['ignored'],
                'important' =>$schema['important'],
            ));

        }

        $tpl->display('agency_importer/filesXML/config.tpl');
    break;


    case 'save_config':


        $title    = filter_input( INPUT_POST, 'title' , FILTER_SANITIZE_STRING );
        $title_int = filter_input( INPUT_POST, 'title_int' , FILTER_SANITIZE_STRING );
        $subtitle = filter_input( INPUT_POST, 'subtitle' , FILTER_SANITIZE_STRING );
        $summary  = filter_input( INPUT_POST, 'summary' , FILTER_SANITIZE_STRING );
        $agency   = filter_input( INPUT_POST, 'agency' , FILTER_SANITIZE_STRING );
        $created   = filter_input( INPUT_POST, 'created' , FILTER_SANITIZE_STRING );
        $body     = filter_input( INPUT_POST, 'body' , FILTER_SANITIZE_STRING );
        $metadata = filter_input( INPUT_POST, 'metadata' , FILTER_SANITIZE_STRING );
        $description = filter_input( INPUT_POST, 'description' , FILTER_SANITIZE_STRING );
        $category_name = filter_input( INPUT_POST, 'category_name' , FILTER_SANITIZE_STRING );
        $body = filter_input( INPUT_POST, 'body' , FILTER_SANITIZE_STRING );
        $ignored = filter_input( INPUT_POST, 'ignored' , FILTER_SANITIZE_STRING );
        $important = filter_input( INPUT_POST, 'important' , FILTER_SANITIZE_STRING );

        $schema =  array(
            'title'    => $title,
            'title_int' => $title_int,
            'subtitle' => $subtitle,
            'summary'  => $summary,
            'agency'   => $agency,
            'created'   => $created,
            'body'     => $body,
            'metadata' => $metadata,
            'description' => $description,
            'category_name' => $category_name,
            'body' =>$body,
            'ignored' =>$ignored,
            'important' =>$important,
        );


        if (s::set('xml_file_schema', $schema) ) {
            m::add(_('Importer XML configuration saved successfully'), m::SUCCESS);
        } else {
            m::add(_('There was an error while saving importer XML configuration'), m::ERROR);
        }

        Application::forward($_SERVER['SCRIPT_NAME']. '?action=list');

    break;

    case 'list':
        $tpl->assign('formAttrs', 'enctype="multipart/form-data"');
        $tpl->display('agency_importer/filesXML/list.tpl');
    break;



    case 'import':

        $numCategories='0';

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

                    if ($extension == "zip") {
                        $dataZIP = array();
                        echo "<br><br><br>";
                        $dataZIP = FilesManager::decompressZIP($uploaddir.$name);

                        @chmod($uploaddir.$name,0775);
                        sort($dataZIP);
                        foreach($dataZIP as $elementZIP) {
                            @chmod($uploaddir.$elementZIP,0775);

                            $importer = ImporterXml::getInstance();
                            $eltoXML = $importer->importXML($uploaddir.$elementZIP);
                            if($eltoXML) {
                                 $XMLFile[$j]=$elementZIP;

                                 $values = $importer->getXMLData($eltoXML);
                                 $article =new Article();
                                 $article->create($values);



                                $dataXML[$j] = $values;
                                $j++;
                            }else{
                            //    m::add(_( 'No valid XML format' ));
                            }
                        }
                    } else {
                        $importer = ImporterXml::getInstance();

                        $eltoXML = $importer->importXML($uploaddir.$name);

                        $XMLFile[$j]=$nameFile;

                        $values = $importer->getXMLData($eltoXML);

                        $article =new Article();
                        $article->create($values);

                        $dataXML[$j] = $values;
                        $j++;
                    }

                 }else{
                       m::add( "<br> Ocurrió algún error al subir el fichero ".$uploaddir.$name." - ".$nameFile." . No pudo guardarse,
                       <br> Compruebe su tamaño (MAX 300 MB)");
                 }
            }


            $tpl->assign('numCategories', $numCategories);
            $tpl->assign('XMLFile', $XMLFile);
            $tpl->assign('dataXML', $dataXML);
            $tpl->assign('action', "import");
            $tpl->assign('total_num', $j);

            $tpl->assign('formAttrs', 'enctype="multipart/form-data"');

        }
        $tpl->display('agency_importer/filesXML/list.tpl');
        //Removed all of temp files in SITE_ADMIN_TMP_PATH
      //  foreach(glob(SITE_ADMIN_TMP_PATH.DS.'xml'.DS.'*.*') as $v){unlink($v);}
    break;

    case 'check':

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
                        $dataZIP = FilesManager::decompressZIP($uploaddir.$name);

                        sort($dataZIP);
                        foreach($dataZIP as $elementZIP) {
                            @chmod($uploaddir.$elementZIP,0775);
                            $importer = ImporterXml::getInstance();

                            $eltoXML = $importer->importXML($uploaddir.$elementZIP);
                            if($eltoXML){
                                $values = $importer->getXMLData($eltoXML);

                                $XMLFile[$j]=$elementZIP;
                                $dataXML[$j] = $values;
                            } else{
                                m::add(_( 'No valid XML format' ));

                            }
                            $j++;
                        }
                    }
                    else
                    {
                        $importer = ImporterXml::getInstance();

                        $eltoXML = $importer->importXML($uploaddir.$name);

                            $values = $importer->getXMLData($eltoXML);
                            $XMLFile[$j]=$nameFile;
                            $dataXML[$j] = $values;
                            $j++;
                    }

                 }else{
                       m:add ("<br> Ocurrió algún error al subir el fichero ".$uploaddir.$name." - ".$nameFile." . No pudo guardarse,
                       <br> Compruebe su tamaño (MAX 300 MB)" );
                 }
            }
            $tpl->assign('XMLFile', $XMLFile);
            $tpl->assign('dataXML', $dataXML);
            $tpl->assign('action', "check");
            $tpl->assign('total_num', $j);

            $tpl->assign('formAttrs', 'enctype="multipart/form-data"');

        }
        $tpl->display('agency_importer/filesXML/list.tpl');
        //Removed all of temp files in SITE_ADMIN_TMP_PATH
     //   foreach(glob(SITE_ADMIN_TMP_PATH.DS.'xml'.DS.'*.*') as $v){unlink($v);}
    break;

default:
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
break;
}



