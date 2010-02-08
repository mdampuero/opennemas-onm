<?php
require_once('./config.inc.php');
require_once('./session_bootstrap.php');

require_once('core/privileges_check.class.php');
Privileges_check::CheckPrivileges("CHECK EXPIRE SESSION");

//Elimina el archivo adjunto de la noticia.
define('PATH_FILES', realpath(dirname(__FILE__).'/../media/files/'));
require_once "libs/class.dir.php";

require_once('core/application.class.php');
require_once('core/attachment.class.php');
require_once('core/content_manager.class.php');
require_once('core/content.class.php');
require_once('core/content_category.class.php');
require_once('core/article.class.php');
require_once('core/related_content.class.php');
require_once('core/attach_content.class.php');
Application::import_libs('*');
$app = Application::load();


//variable GET
$idem=$_GET['idem'];
$idart=$_GET['idart'];
$att = new Attach_content();	
$att->att_delete($idart,$idem);			  
// include('prion.php');  		 

