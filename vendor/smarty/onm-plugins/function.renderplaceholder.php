<?php
/* {renderplaceholder items=$articles tpl='xxx.tpl' placeholder="placeholder_0_0"} */
function smarty_function_renderplaceholder($params, &$smarty) {

    $outputHTML = '';

    // get all the parameters passed to the function
    $items   = $params['items'];
    $tpl     = $params['tpl'];
    $placeholder  = $params['placeholder'];
    $cssclass = $params['cssclass'];
    $category_name = $smarty->getTemplateVars('category_name');
    $placeholder_property = ($category_name=='home') ? 'home_placeholder': 'placeholder';
    $varname = (!isset($params['varname']))? 'item': $params['varname'];

    // Doing some checks if this method was called properly
    if(!isset($items)) { throw new Exception('RenderPlaceHolder: you must specify the items param'); }

    // Iterate over all the items and try to get its html representation
    $caching = $smarty->caching;
    $smarty->caching = 0;
    if(isset($items) && count($items>0)){
        $iteration = 0;
        foreach($items as $i => $item) {

            if( $item->{$placeholder_property} == $placeholder && ($item->available == 1) ) {

                if(method_exists($item, 'render')){

                    $outputHTML .= $item->render($params);

                } else {

                    $smarty->clearAssign($varname);
                    $smarty->assign($varname, $items[$i]);
                    $smarty->clearAssign('cssclass');
                    if($iteration == 0) {
                        $smarty->assign('cssclass', $cssclass);
                    }
                    $outputHTML .= "\n". $smarty->fetch( $tpl, md5(serialize($item)) );

                }

                $iteration++;

            }

        }
        unset($iteration);
    }

    $smarty->caching = $caching;

    // return all the html collected
    return( $outputHTML );
}
