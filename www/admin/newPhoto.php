<?php
//error_reporting(E_ALL);
require_once('./config.inc.php');
require_once('./session_bootstrap.php');

define('PATH_FILES', realpath(dirname(__FILE__).'/../media/'));
// Ejemplo para tener objeto global
require_once('core/application.class.php');
Application::import_libs('*');
$app = Application::load();
require_once('core/content_manager.class.php');
require_once('core/content.class.php');
require_once('core/photo.class.php');
require_once('core/media.manager.class.php');

require_once('libs/utils.functions.php');
require_once('libs/Pager/Pager.php');

$tpl = new TemplateAdmin(TEMPLATE_ADMIN);

$tpl->assign('titulo_barra', 'Nuevas Fotografias');
$tpl->assign('nameCat', $_REQUEST['nameCat']);
$tpl->assign('category', $_REQUEST['category']);
echo '
  <script type="text/javascript" src="themes/default/js/prototype.js" language="javascript"></script>
	<script type="text/javascript" src="themes/default/js/scriptaculous/scriptaculous.js" language="javascript"></script>
	 <script type="text/javascript" src="themes/default/js/photos.js" language="javascript"></script>
	';


echo "
<script type='text/javascript' language='javascript'>

function putMini(pkfoto,imag){

	  var ul = parent.document.getElementById('thelist');	  
	  Nodes = parent.document.getElementById('thelist').getElementsByTagName('li');	  			
	
	 // alert(pkfoto);

		    li= document.createElement('li');  		
			li.setAttribute('id', pkfoto);			
			li.setAttribute('value', imag);			
		    li.setAttribute('class', 'family');
		    li.setAttribute('style', 'cursor: move; list-style-type: none;');		
		        min = document.createElement('img');
			 	min.id= pkfoto;
			 	min.border=1;
			 	min.src= imag;
			 	li.appendChild(min); 
			 			
			ul.appendChild(li);	
  } 
 
 
 
</script> "; 
//Nombre categoria a la que se suben las fotos. Directorio de las fotos media/images/authors/nameAuthor.
//	echo "autor".$_REQUEST['nameAuthor'];

	 $nameCat=$_REQUEST['nameCat'];
	 $nameAuthor=normalize_name($_REQUEST['nameAuthor']);
	 
	 $uploaddir =  MEDIA_IMG_PATH ."/".$nameCat."/".$nameAuthor."/" ;
	if(!is_dir($uploaddir)) {
		mkdir($uploaddir, 0775);     
		@chmod($uploaddir,0775); //Permisos de lectura y escritura del fichero
	}						
//arrays con Tags y descripcion de cada una
$tags=$_REQUEST['tags'];
$descript= $_REQUEST['descript'];

	$dateStamp = date('Y') . date ('m') . date ('d');
if($nameAuthor) {
	if (count($_FILES["file"]["name"]) >= 1 && !empty($_FILES["file"]["name"][0]) ) {
			
			for($i=0;$i<count($_FILES["file"]["name"]);$i++) {

					$nameFile = $_FILES["file"]["name"][$i];	//Nombre del archivo a subir
					$datos=pathinfo($nameFile);					 //sacamos inofr del archivo	
					
					//Preparamos el nuevo nombre YYYYMMDDHHMMSSmmmmmm
					$extension=$datos['extension']; 
					$t=gettimeofday(); //Sacamos los microsegundos
					$micro=intval(substr($t['usec'],0,5)); //Le damos formato de 5digitos a los microsegundos
						
					$name= date("YmdHis").$micro.".".$extension;
						
			
				if (move_uploaded_file($_FILES["file"]["tmp_name"][$i], $uploaddir.$name)) {
						
						@chmod($uploaddir.$name,0775); //Permisos de lectura y escritura del fichero
					
					
						  	
					       $data['title']=$nameFile; 
					       $data['name']=$name;
				
					   //    $data['path_file']=$name;
							$data['path_file']= "/".$nameCat."/".$nameAuthor ;
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
					     
	      				$foto = new Photo();  
						if( $elid = $foto->create($data)) {						
			      			//recuperar id. para meter la miniatura
			      			// $elid = $GLOBALS['application']->conn->Insert_ID();	
			      			 //no se utiliza $elid pero ojo no funciona con el id por date. create de photo return el id
			      	//		 echo "in bd ok";
							}		
				      	echo " <script> 
					      	   var nuevo =  \" <div id='capa".$elid."' style='display: inline;' ><table  border='0' cellpadding='0' cellspacing='4' class='fuente_cuerpo' width='100%'><tr bgcolor='#ffffff'> <td width='50%'>Foto ".($i+1).": ".$name."   <input type='text' id='titles[".$elid."]' name='titles[".$elid."]' class='required' size='38' value='".$data['path_file']."/".$data['name']."' /> <input type='text' id='descript[".$elid."]' name='descript[".$elid."]' class='required' size='38' value='".$descript[$i]."' /> </td><td> Tags: <input type='text' id='comenta[".$elid."]' name='comenta[".$elid."]' class='required' size='38' value='".$tags[$i]."' /> </td></tr></table> </div> \";				      	 
					      	   
					      	    parent.document.getElementById( 'contenedor' ).innerHTML = parent.document.getElementById( 'contenedor' ).innerHTML  + nuevo ;
						
							   putMini('".$elid."','".MEDIA_IMG_PATH_WEB.$data['path_file']."/".$data['name']."');							 			    					  
					      	  </script>   ";
				     
		         
			    }else{ 
			       echo "<br> Ocurrió algún error al subir el fichero ".$nameFile." - ".$name." . No pudo guardarse, 
			       <br> Compruebe su tamaño (MAX 300 MB)";
			     
			       
			    }

			}
	}		
}else{	
		//echo "Escriba el nombre de un author";
}
	 $tpl->display('newPhoto.tpl');
	
	
?>

 