<?php
//error_reporting(E_ALL);
require_once('./config.inc.php');
require_once('./session_bootstrap.php');

// Ejemplo para tener objeto global
require_once('./core/application.class.php');
require_once('./core/user.class.php');

require_once('./core/method_cache_manager.class.php');

Application::import_libs('*');
$app = Application::load();

// Register events
require_once('articles_events.php');


$tpl = new TemplateAdmin(TEMPLATE_ADMIN);
$tpl->assign('titulo_barra', 'Panel de Control');

$tpl->addScript(array('prototype.js', 'FeedReader.js?cacheburst=1257954926'));
 
$xml = simplexml_load_file(SITE_URL . 'rss/');

$result = $xml->xpath('//item');

$news = array();
while(list( , $node) = each($result)) {
    $news[] = array( 'title' => $node->title,
                     'link'  => $node->link,
                     'description' => $node->description );
}
$tpl->assign('news', $news);

if(isset($_SESSION['authGmail'])) {
    $user = new User();
    $messages = $user->cache->parseGmailInbox(base64_decode($_SESSION['authGmail']));        
    
    $tpl->assign('messages', $messages);
}


$tpl->display('welcome.tpl');