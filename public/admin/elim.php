<?php

/**
 * Setup app
*/
require_once('../bootstrap.php');
require_once('./session_bootstrap.php');

require_once(SITE_CORE_PATH.'privileges_check.class.php');
Privileges_check::CheckPrivileges("CHECK EXPIRE SESSION");

//Elimina el archivo adjunto de la noticia.
define('PATH_FILES', realpath(dirname(__FILE__).'/../media/files/'));
require_once(SITE_LIBS_PATH.'class.dir.php');

//variable GET
$idem=$_GET['idem'];
$idart=$_GET['idart'];
$att = new Attach_content();	
$att->att_delete($idart,$idem);			  
// include('prion.php');