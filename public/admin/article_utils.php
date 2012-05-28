<?php

/**
 * Setup app
*/
require_once('../bootstrap.php');
require_once('./session_bootstrap.php');

require_once(SITE_LIBS_PATH.'Pager/Pager.php');

if(isset($_REQUEST['action']) ) {
	switch($_REQUEST['action']) {

		case 'search_related':
			//Noticias relacionadas
	  			$mySearch = cSearch::getInstance();
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
