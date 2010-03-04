<?php
/* {renderplaceholder items=$articles tpl='xxx.tpl' placeholder="placeholder_0_0"} */
function smarty_function_renderplaceholder($params, &$smarty) {
    $output = '';

    $items   = $params['items'];
    $tpl     = $params['tpl'];
    $placeholder  = $params['placeholder'];

    $category_name = $smarty->get_template_vars('category_name');
    $property = ($category_name=='home')? 'home_placeholder': 'placeholder';
    $varname = (!isset($params['varname']))? 'item': $params['varname'];
        
    $caching = $smarty->caching;
    $smarty->caching = 0;
    if(isset($items) && count($items>0)){
        foreach($items as $i => $item) {
            if( property_exists($item, $property) && $item->{$property} == $placeholder ) {
                $smarty->clear_assign($varname);
                $smarty->assign($varname, $items[$i]);
                //$smarty->_tpl_vars[$varname] = $items[$i];
                $output .= $smarty->fetch( $tpl, md5(serialize($item)) );
                //echo $smarty->fetch( $tpl );

                //unset( $smarty->_tpl_vars[$varname] );
            }
        }
    }
    
    $smarty->caching = $caching;    

    return( $output );
}