<?php

/**
 * Setup app
*/
require_once('../bootstrap.php');
require_once('./session_bootstrap.php');

require_once(SITE_CORE_PATH.'privileges_check.class.php');
require_once(SITE_CORE_PATH.'method_cache_manager.class.php');

// Register events
require_once('articles_events.php');

$tpl = new TemplateAdmin(TEMPLATE_ADMIN);
$tpl->assign('titulo_barra', 'Control panel');

$tpl->addScript(array('prototype.js', 'FeedReader.js?cacheburst=1257954926'));
 
/*$xml = simplexml_load_file(SITE_URL . 'rss/');

$result = $xml->xpath('//item');

$news = array();
while(list( , $node) = each($result)) {
    $news[] = array( 'title' => $node->title,
                     'link'  => $node->link,
                     'description' => $node->description );
}
$tpl->assign('news', $news);*/

$feeds = array (
                array('name' => 'El pais', 'url'=> 'http://www.elpais.com/rss/feed.html?feedId=1022'),
                array('name' => '20 minutos', 'url'=> 'http://20minutos.feedsportal.com/c/32489/f/478284/index.rss'),
                array('name' => 'Publico.es', 'url'=> 'http://www.publico.es/rss/'),
                array('name' => 'El mundo', 'url'=> 'http://elmundo.feedsportal.com/elmundo/rss/portada.xml'),
                );

$tpl->assign('feeds',$feeds);

if(isset($_SESSION['authGmail'])) {
    $user = new User();
    $messages = $user->cache->parseGmailInbox(base64_decode($_SESSION['authGmail']));        
    
    $tpl->assign('messages', $messages);
}

$tpl->display('welcome/index.tpl');