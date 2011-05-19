<?php

/**
 * Start up and setup the app
*/
require_once('../bootstrap.php');

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
            $output = $tpl->fetch('internal_widgets/module_print_comments.tpl');
            $tpl->caching = $caching;
            //}
            Application::ajax_out($output);
        break;
        
    }
}
