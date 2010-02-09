<?php
/**
 * insert.numComments.php, Smarty insert plugin to get num of comments to the one article
 * 
 * @package  OpenNeMas
 * @author Toni Martínez <toni@openhost.es>
 * @version  0.6-rc1
 */

/**
 * smarty_insert_numComments, Smarty insert plugin to get num of comments to the one article
 * <code>
 * {insert name="numComments" id="2009051723543313996"}
 * </code>
 *
 * @author Toni Martínez <toni@openhost.es>
 * @param array $params  Parameters of smarty function
 * @param Smarty $smarty Object reference to Smarty class
 * @return array ok objects Comment
 */
function smarty_insert_numComments($params, &$smarty) {
    if (empty($params['id'])) {
        $smarty->trigger_error("insert comments: missing id");
        return;
    }
    
    // Check it's clone article {{{
    if(Article::isClone($params['id'])) {
        $params['id'] = Article::getOriginalPk($params['id']);
    }
    // }}}    

    $numComments = 0;
    if($params['where']=='pc'){
        $comment = new PC_Comment();
        $comments = $comment->count_public_comments($params['id']);

    }else{
        $comment = new Comment();
        $cm = new ContentManager();
        $numComments = $comment->count_public_comments($params['id']);
    }
     
    return $numComments;
}
