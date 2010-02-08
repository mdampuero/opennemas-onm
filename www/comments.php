<?php
require_once('config.inc.php');

// Ejemplo para tener objeto global
require_once('core/application.class.php');
Application::import_libs('*');
$app = Application::load();



require_once('core/content_manager.class.php');
require_once('core/content.class.php');
require_once('core/content_category.class.php');
require_once('core/comment.class.php');

$tpl = new Template(TEMPLATE_USER);

if(isset($_REQUEST['action']) ) {
    switch($_REQUEST['action']) {
	case 'paginate_comments':

            $comment = new Comment();

            $comments = $comment->get_public_comments($_REQUEST['id']);       
          //  if(count($comments) >0) {
                $cm = new ContentManager();

                $comments = $cm->paginate_num_js($comments, 9, 1, 'get_paginate_comments',"'".$_REQUEST['id']."'");
               
                $tpl->assign('paginacion', $cm->pager);

                $tpl->assign('comments', $comments);

               $caching = $tpl->caching;
                $tpl->caching = 0;
                $output = $tpl->fetch('modulo_comments.tpl');
               $tpl->caching = $caching;
            //}
            Application::ajax_out($output);

         break;
    }
}
