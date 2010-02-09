<?php
function smarty_function_renderitems($params, &$smarty) {
    $output = '';

    $items   = $params['items'];
    $tpl     = $params['tpl'];
    $filter  = '$__condition__ = '.$params['filter'].';';
    $varname = (!isset($params['varname']))? 'item': $params['varname'];
    
    $smarty->caching = 0;
    
    foreach($items as $i => $item) {
        // eval filter and set $condition variable
        eval($filter);
        
        if( $__condition__ ) {
            $smarty->clear_assign($varname);
            $smarty->assign($varname, $items[$i]);
            //$smarty->_tpl_vars[$varname] = $items[$i];            
            $output .= $smarty->fetch( $tpl, md5(serialize($item)) );
            //echo $smarty->fetch( $tpl );
            
            //unset( $smarty->_tpl_vars[$varname] );
        }
    }
    
    $smarty->caching = 2;

    return( $output );
}