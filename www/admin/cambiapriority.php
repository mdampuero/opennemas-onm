<?php
//Elimina el archivo adjunto de la noticia.
require_once('./config.inc.php');
require_once('./session_bootstrap.php');

define('PATH_FILES', realpath(dirname(__FILE__).'/../media/files/'));
require_once "libs/class.dir.php";

require_once('core/application.class.php');

require_once('core/content_category.class.php');
require_once('core/content_category_manager.class.php');

Application::import_libs('*');
$app = Application::load();


//variable GET

   $orden=$_GET['orden'];
 
   
     $ok=0;

   if(isset($orden)){
			$tok = strtok($orden,",");	    
			$pos=1;
			while (($tok !== false) AND ($tok !=" ")) {			   		   				   			   		 
		   		   	$category = new ContentCategory($tok);			
					$category->set_priority($pos); 
		    		$tok = strtok(",");
		    		$pos+=1;
		    		
		    }
	      $ok=1;
	
   }

 if( $ok==1){
			echo '<script>
			alert("Guardado correctamente");
			</script>';
}

?>