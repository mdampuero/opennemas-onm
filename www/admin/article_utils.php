<?php
//error_reporting(E_ALL);
require_once('./config.inc.php');
require_once('./session_bootstrap.php');

// Ejemplo para tener objeto global
require_once('core/application.class.php');
Application::import_libs('*');
$app = Application::load();

require_once('libs/Pager/Pager.php');
require_once('core/content_manager.class.php');
require_once('core/content.class.php');
require_once('core/article.class.php');

require_once('core/search.class.php');
 			
if(isset($_REQUEST['action']) ) {
	switch($_REQUEST['action']) {
		
		case 'search_related':		
			//Noticias relacionadas			
	  			$mySearch = cSearch::Instance();
				$search = $mySearch->SearchContents("metadata", $_REQUEST['tags'], 'Article');
       			echo "<pre>Relacionadas: ";
				print_r($search);
				echo "</pre>";
		break;
	
		default:			
		break;
	}
} else {
	
}

?>
