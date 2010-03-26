<?php
/* -*- Mode: PHP; tab-width: 4 -*- */
/**
 * OpenNeMas project
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   OpenNeMas
 * @package    OpenNeMas
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once('config.inc.php');

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
        
        case 'paginate_comments': {
            $comment = new Comment();
            
            $comments = $comment->get_public_comments($_REQUEST['id']);
            
            //  if(count($comments) >0) {
            $cm = new ContentManager();
            
            $comments = $cm->paginate_num_js($comments, 9, 1, 'get_paginate_comments',"'".$_REQUEST['id']."'");
            
            $tpl->assign('paginacion', $cm->pager);
            $tpl->assign('comments', $comments);
            
            $caching = $tpl->caching;
            $tpl->caching = 0;
            $output = $tpl->fetch('module_print_comments.tpl');
            $tpl->caching = $caching;
            //}
            Application::ajax_out($output);
        } break;
        
    }
}
