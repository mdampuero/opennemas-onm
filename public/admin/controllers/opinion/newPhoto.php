<?php

/**
 * Setup app
*/
require_once(dirname(__FILE__).'/../../../bootstrap.php');
require_once(SITE_ADMIN_PATH.'session_bootstrap.php');

/**
 * Check privileges
*/
Acl::checkOrForward('AUTHOR_CREATE');

//require_once(SITE_LIBS_PATH.'Pager/Pager.php');

$tpl = new TemplateAdmin(TEMPLATE_ADMIN);

$tpl->assign('titulo_barra', 'Nuevas Fotos Autor Opinion');


if(isset($_REQUEST['action']) && $_REQUEST['action']=='addPhoto') {
    $nameCat=$_REQUEST['nameCat'];
    $nameAuthor= String_Utils::normalize_name($_REQUEST['nameAuthor']);
    $tpl->assign('nameCat', $_REQUEST['nameCat']);
    $tpl->assign('category', $_REQUEST['category']);

    $path_file ="/authors/".$nameAuthor."/" ;
    $uploaddir =  MEDIA_IMG_PATH .$path_file;



    if(!is_dir($uploaddir)) {
        mkdir($uploaddir, 0777);
        @chmod($uploaddir,0777); //Permisos de lectura y escritura del fichero
    }
    //arrays con Tags y descripcion de cada una
    $tags=$_REQUEST['tags'];
    $descript= $_REQUEST['descript'];

    $dateStamp = date('Y') . date ('m') . date ('d');
    if (count($_FILES["file"]["name"]) >= 1 && !empty($_FILES["file"]["name"][0]) ) {

        for($i=0;$i<count($_FILES["file"]["name"]);$i++) {

            $nameFile = $_FILES["file"]["name"][$i];	//Nombre del archivo a subir
            $datos=pathinfo($nameFile);					 //sacamos inofr del archivo

            //Preparamos el nuevo nombre YYYYMMDDHHMMSSmmmmmm
            $extension=$datos['extension'];
            $t=gettimeofday(); //Sacamos los microsegundos
            $micro=intval(substr($t['usec'],0,5)); //Le damos formato de 5digitos a los microsegundos

            $name= date("YmdHis").$micro.".".$extension;
            
            if(!is_dir($uploaddir)) {
                FilesManager::createDirectory($uploaddir);
            }
                
            $uploader_status = move_uploaded_file($_FILES["file"]["tmp_name"][$i], $uploaddir.$name);
            
            if ($uploader_status) {

                @chmod($uploaddir.$name,0777); //Permisos   del fichero

                $datos = pathinfo($nameFile);     //sacamos infor del archivo
                //
                //Preparamos el nuevo nombre YYYYMMDDHHMMSSmmmmmm
                $extension = strtolower($datos['extension']);

                $data['title']=$nameFile;
                $data['name']=$name;

                $data['path_file']=  $path_file ;
                $data['description']=$descript[$i];
                $data['metadata']=$tags[$i];
                $data['nameCat']=$_REQUEST['nameCat']; //nombre de la category
                $data['category']=$_REQUEST['category'];

                $infor  = new MediaItem( $uploaddir.$name ); 	//Para sacar todos los datos de la imag

                $data['created']=$infor->atime;
                $data['changed']=$infor->mtime;
                $data['date']=$infor->mtime;
                $data['size']=round($infor->size/1024,2) ;
                $data['width']=$infor->width;
                $data['height']=$infor->height;
                $data['type']=$infor->type;
                $data['type_img'] = $extension;
                $data['media_type'] = 'image';
                $data['author_name']  = '';

                $foto = new Photo();
                $elid = $foto->create($data);
                if( !empty($elid) ) {
                    //recuperar id. para meter la miniatura // $elid = $GLOBALS['application']->conn->Insert_ID();
                     //no se utiliza $elid pero ojo no funciona con el id por date. create de photo return el id
                    $script = " <script>
                           var nuevo =  \" <div id='capa".$elid."' style='display: inline;' ><table  border='0' cellpadding='0' cellspacing='4' class='fuente_cuerpo' width='100%'><tr bgcolor='#ffffff'> <td width='50%'>Foto ".($i+1).": ".$name."   <input type='text' id='titles[".$elid."]' name='titles[".$elid."]' class='required' size='38' value='".$data['path_file']."/".$data['name']."' /> <input type='text' id='descript[".$elid."]' name='descript[".$elid."]' class='required' size='38' value='".$descript[$i]."' /> </td><td> Tags: <input type='text' id='comenta[".$elid."]' name='comenta[".$elid."]' class='required' size='38' value='".$tags[$i]."' /> </td></tr></table> </div> \";

                            parent.document.getElementById( 'contenedor' ).innerHTML = parent.document.getElementById( 'contenedor' ).innerHTML  + nuevo ;
                             putMini('".$elid."','".MEDIA_IMG_PATH_WEB.$data['path_file']."/".$data['name']."');
                          </script>   ";
                           $tpl->assign('script', $script);
                }  else {
                    echo "<br> Ocurrió algún error al guardar la foto inténtelo otra vez";
                }
            }else{
               echo "<br> Ocurrió algún error al subir el fichero ".$nameFile." - ".$name." . No pudo guardarse,
               <br> Compruebe su tamaño (MAX 300 MB)";


            }

			}
	}
}

$tpl->display('opinion/newPhoto.tpl');
