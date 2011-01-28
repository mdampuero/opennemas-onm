<?php
/**
 * insert.comments.php, Smarty insert plugin to get comments to the one article
 * 
 * @package  OpenNeMas
 * @author Toni Martínez <toni@openhost.es>
 * @version  0.6-rc1
 */

/**
 * smarty_insert_comments, Smarty insert plugin to get comments to the one article
 * <code>
 * {insert name="comments" id="2009051723543313996"}
 * </code>
 *
 * @author Toni Martínez <toni@openhost.es>
 * @param array $params  Parameters of smarty function
 * @param Smarty $smarty Object reference to Smarty class
 * @return array ok objects Comment
 */
function smarty_insert_comments($params, &$smarty) {
    if (empty($params['id'])) {
        $smarty->trigger_error("insert comments: missing id");
        return;
    }
    
    // Check if it's clone article {{{
    $article = new Article($params['id']);
    if($article->isClone()) {
        $params['id'] = Article::getOriginalPk($params['id']);
    }
    // }}}

    $comentar = array();

    $comment = new Comment();
    $cm = new ContentManager();
    $comments = $comment->get_public_comments($params['id']);
    $comments = $cm->paginate_num_js($comments, 9, 1, 'get_paginate_comments',"'".$params['id']."'");
    $smarty->assign('num_comments', count($comments));
    $smarty->assign('paginacion', $cm->pager);

    $smarty->assign('comments', $comments);
    
    $caching = $smarty->caching;
    $smarty->caching = 0;
    $output = $smarty->fetch('internal_widgets/partials/_list_comments.tpl');
    $smarty->caching = $caching;
     
    return $output;
}
