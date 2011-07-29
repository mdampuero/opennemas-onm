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
 
if(isset($_REQUEST['action']) ) {
    switch($_REQUEST['action']) {
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

                        if ($extension == "zip"){
                            $dataZIP = array();
                            echo "<br><br><br>";
                            $dataZIP = decompressZIP($uploaddir.$name);

                            @chmod($uploaddir.$name,0775);
                            sort($dataZIP);
                            foreach($dataZIP as $elementZIP) {
                                @chmod($uploaddir.$elementZIP,0775);

                                $eltoXML = \Onm\Import\importerIdeal::importXML($uploaddir.$elementZIP);
                                if($eltoXML) {
                                     $XMLFile[$j]=$elementZIP;

                                     $values = \Onm\Import\importerIdeal::checkXMLData($eltoXML);
                                     $article =new Article();
                                     $article->create($values);
                                     


                                    $dataXML[$j] = $values;
                                    $j++;
                                }else{
                                    echo 'xml no valido';
                                }
                            }
                        }else{
                            $eltoXML = \Onm\Import\importerIdeal::importXML($uploaddir.$name);

                            $XMLFile[$j]=$nameFile;
                            
                            $values = \Onm\Import\importerIdeal::checkXMLData($eltoXML);
                            
                            $article =new Article();
                         

                            $article->create($values);

                            $dataXML[$j] = $values;
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
                                $eltoXML = \Onm\Import\importerIdeal::importXML($uploaddir.$elementZIP);
                                $values = \Onm\Import\importerIdeal::checkXMLData($eltoXML);
                                $XMLFile[$j]=$elementZIP;
                                $dataXML[$j] = $values;
                                $j++;
                            }
                        }
                        else
                        {
                                $eltoXML = \Onm\Import\importerIdeal::importXML($uploaddir.$name);
                                $values = \Onm\Import\importerIdeal::checkXMLData($eltoXML);
                                $XMLFile[$j]=$nameFile;
                                $dataXML[$j] = $values;
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
$tpl->display('agency_importer/ideal/importXML.tpl');


 //TODO: sacarla de aquí
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

        $zip->extractTo(SITE_TMP_PATH.DS);
        
        $zip->close();
        return $dataZIP;
    }

