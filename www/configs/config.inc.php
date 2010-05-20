<?php
if (eregi('config.inc.php', $_SERVER['PHP_SELF'])) {
	die();
}

define ('SS', "/");

define ('APP_NAME', $_SERVER['SERVER_NAME']);
define ('APP_DIR', '/var/lib/opennemas/' . APP_NAME);

define ('SITE', APP_NAME);

define ('SITE', "demo-opennemas.vifito.eu");
define ('SITE_PATH',  realpath(dirname(__FILE__) . '/../') . '/' );
define ('SITE_ADMIN_DIR', "admin");
define ('SITE_ADMIN_TMP_DIR', "tmp");
define ('SITE_ADMIN_PATH', SITE_PATH.SS.SITE_ADMIN_DIR.SS);
define ('SITE_ADMIN_TMP_PATH', SITE_ADMIN_PATH.SITE_ADMIN_TMP_DIR.SS);

$protocol = 'http://';
if(preg_match('@^/admin/@', $_SERVER['REQUEST_URI'])) {
    $protocol = (!empty($_SERVER['HTTPS']))? 'https://': 'http://';
}

define ('SITE_URL', $protocol.SITE.SS);
define ('SITE_URL_ADMIN', $protocol.SITE.'/admin/');

define ('SITE_LIBS_PATH', SITE_PATH . 'libs/');
define ('SITE_PATH_WEB', "/");
define ('SITE_TITLE', "OpenNemas - Sistema de xestión de contido");
define ('SITE_DESCRIPTION', "Noticias ");
define ('SITE_KEYWORDS', "news, digital news, newspaper");

define ('URL', SITE_URL_ADMIN);
define ('URL_PUBLIC', SITE_URL);
define ('RELATIVE_PATH', SITE_ADMIN_DIR);
define ('PATH_APP', SITE_ADMIN_PATH);


/* [DEFAULT LANGUAGE] ******************************************************* */
define ('LANG_DEFAULT', 'en');


/* [PAGER] ****************************************************************** */
define ('ITEMS_PAGE', "20");

/* [ BASE DE DATOS ] **********************************************************/
define ('BD_TYPE', "mysql");
define ('BD_HOST', "localhost");
define ('BD_USER', "root");
define ('BD_PASS', "root");
define ('BD_INST', "opennemasdemodb");
define ('BD_DSN', BD_TYPE."://".BD_USER.":".BD_PASS."@".BD_HOST."/".BD_INST);

/* [ SYSTEM CONFIGURATION ] ********************************************************* */
define ('SYS_LOG_DEBUG', "1");
define ('SYS_LOG_VERBOSE', "0");
define ('SYS_LOG_INFO', "1");

define ('SYS_LOG', APP_DIR . '/log/application.log');
define ('LOG_ENABLE', 0);

define ('SYS_SESSION_TIME', "15");
define ('SYS_LOG_EMAIL', 'desarrollo@openhost.es');
define ('SYS_NAME_GROUP_ADMIN', 'Administrador');

// session.save_path
define('OPENNEMAS_BACKEND_SESSIONS',  APP_DIR . '/sessions/backend/');
define('OPENNEMAS_FRONTEND_SESSIONS', APP_DIR . '/sessions/frontend/');

/* [ MEDIA CONFIGURATION ] ********************************************************* */
define ('MEDIA_UPLOAD', "0");
define ('MEDIA_UPLOAD_FLASH', "1");
define ('MEDIA_UPLOAD_VIDEO', "0");
define ('MEDIA_EXTENSIONS', "bmp,csv,doc,epg,gif,ico,jpg,odg,odp,ods,odt,pdf,png,ppt,swf,txt,xcf,xls");
define ('MEDIA_MAX_SIZE', "200000");
define ('MEDIA_DIR', "/media");
define ('MEDIA_PATH', SITE_PATH.MEDIA_DIR);
define ('MEDIA_PATH_URL', SITE_URL.MEDIA_DIR);
define ('MEDIA_IMG_DIR', "/images");
define ('MEDIA_IMG_PATH', MEDIA_PATH.MEDIA_IMG_DIR);
define ('MEDIA_IMG_PATH_URL', MEDIA_PATH_URL.MEDIA_IMG_DIR);
define ('MEDIA_IMG_PATH_WEB', MEDIA_DIR.MEDIA_IMG_DIR."/");

define ('MEDIA_CONECTA_WEB', MEDIA_DIR."/conecta/");
define ('MEDIA_CONECTA_PATH', MEDIA_PATH."/conecta/");
define ('MEDIA_CONECTA_URL', MEDIA_PATH_URL."/conecta/");

/* [TEMPLATES] *********************************************************** */ 
define ('TEMPLATE_USER', "lucidity");
define ('TEMPLATE_USER_PATH', SITE_PATH."themes/".TEMPLATE_USER.SS);
define ('TEMPLATE_USER_PATH_WEB', SITE_PATH_WEB."themes/".TEMPLATE_USER.SS);
define ('TEMPLATE_USER_URL', SITE_URL."themes/".TEMPLATE_USER.SS);

define ('TEMPLATE_ADMIN', "default");
define ('TEMPLATE_ADMIN_PATH', SITE_ADMIN_PATH."themes/default/");
define ('TEMPLATE_ADMIN_PATH_WEB', SITE_PATH_WEB.SITE_ADMIN_DIR.SS."themes/default/");

/* [PATH UPLOADS] *********************************************************** */ 
define ('PATH_UPLOAD', MEDIA_IMG_PATH);
define ('URL_UPLOAD', MEDIA_IMG_PATH_URL);

/* [ADVERTISEMENT] ********************************************************** */
define ('ADVERTISEMENT_ENABLE', 0);

/* [IPC SYSTEM] ************************************************************* */
define ('MUTEX_ENABLE', 0); // Experimental!!! 

/* [Facebook API KEY] ***************************************************** */
// demo.opennemas.com
define ('FB_APP_APIKEY', '82e3b37b545df691b03837a1bb9e9beb');
define ('FB_APP_SECRET', '567d110472d47c5fd9915be34a132f30');


/* [MAIL] *******************************************************************  */
#217.76.146.62, ssl://smtp.gmail.com:465, ssl://smtp.gmail.com:587
define ('MAIL_HOST', "localhost");
define ('MAIL_USER', "");
define ('MAIL_PASS', "");

define ('APC_PREFIX', 'demo');

/* [ASSSET SERVERS] *********************************************************  */
define('ASSET_HOST','assets%02d.opennemas.com');
