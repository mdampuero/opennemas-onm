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
function smarty_insert_pagination_comments($params, &$smarty) {
    if (empty($params['total'])) {
        $smarty->trigger_error("insert comments: missing total");
        return;
    }
    
    $output = '';
    
    $total = $params['total'];
    
    if($total <= 0) {
        return $output;
    }
    
    $pages = ceil($total / 9.0);
    
    $output .= '<div class="num-pages span-6">Página 1 de ' . $pages . '</div>';
    $output .= '<div class="span-10 pagination last"><ul>';
    
    if($pages > 1) {        
        $output .= '<li class="active"><a href="#1">1</a></li>';
        
        for($i = 2; $i <= $pages; $i++) {
            $output .= '<li><a href="#' . $i . '">' . $i . '</a></li>';
        }
        
        // TODO: support next and previous link
        //$output .= '<li class="next"><a href="#next">Siguiente</a></li>';    
    }
    
    $output .= '</ul></div>';    
    
    return $output;
}
