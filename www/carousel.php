<?php
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest')) {
    require_once('config.inc.php');
    require_once('core/application.class.php');
    
    Application::import_libs('*');
    $app = Application::load();
        
    require_once('core/content_manager.class.php');
    require_once('core/content.class.php');

    require_once('core/author.class.php');
    require_once('core/opinion.class.php');
    require_once('core/photo.class.php');
}

$cm = new ContentManager();
//$cartadirector = $cm->find('Opinion', 'type_opinion=2 and in_home=1 and available=1 and content_status=1', 'ORDER BY created DESC LIMIT 0,1');

$offset  = (isset($_REQUEST['offset']) && ($_REQUEST['offset']>=0))? $_REQUEST['offset']  : 0;
$numrows = (isset($_REQUEST['numrows']))? $_REQUEST['numrows']: 10; 
$limit   = 'LIMIT '.intval($offset*$numrows).','.($numrows+1); // 10+1 to control if it's lastest row data

$opinions = $cm->find('Opinion',' opinions.type_opinion=0 and contents.available=1 and contents.content_status=1 and contents.in_home=1', 'ORDER BY position ASC, created DESC '.$limit);

$items = array();

if(!empty($opinions)){

    foreach($opinions as $opinion) {
        $author = new Author($opinion->fk_author);

        $obj = new stdClass();
        $obj->id        = $opinion->id;
        $obj->permalink = $opinion->permalink;
        $obj->title     = stripslashes($opinion->title);
        $obj->author    = stripslashes($author->name);
        $obj->condition = stripslashes($author->condition);
        $obj->photo     = $author->cache->get_photo($opinion->fk_author_img_widget)->path_img; // fk_author_img_widget

        $items[] = $obj;
    }
    $autores  = $author->cache->all_authors(NULL,'ORDER BY name');
    $tpl->assign('carousel_autores', $autores);
}
$isLastest = true;
if(count($items)>10) {    
    array_pop($items);
    $isLastest = false;
}

// Data carousel
$data = new stdClass();
$data->items = $items;
$data->isLastest = $isLastest;

// Petición desde ajax
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest')) {
    header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
    header('Content-type: application/json');
    
    echo json_encode( $data );
    exit(0);
} 

// Asignar opiniones
$tpl->assign('carousel_data', $data);

// Director
$director = $cm->find('Opinion',' opinions.type_opinion=2 and contents.in_home=1 and contents.available=1 and contents.content_status=1', 'ORDER BY created DESC LIMIT 0,1');
if( isset($director[0]) ) {
    $tpl->assign('carousel_director', $director[0]);
}

// Editorial
$editorial = $cm->find('Opinion','opinions.type_opinion=1 and contents.in_home=1 and contents.available=1  and contents.content_status=1', 'ORDER BY position ASC, created DESC LIMIT 0,2');
$tpl->assign('carousel_editorial', $editorial);

//$autores = $cm->find('Author',' type_opinion=2 and in_home=1 and available=1 and content_status=1', 'ORDER BY ');
//$author = new Author();

 