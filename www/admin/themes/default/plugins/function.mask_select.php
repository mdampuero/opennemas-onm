<?php

/**
 *
 * <code>
 * {mask_select item=$item page=$args.page selected=$mask}
 * </code>
 */
function smarty_function_mask_select($params, &$smarty)
{    
    if(!isset($params['item'])) {        
        $smarty->trigger_error('[plugin] mask_select needs a "item" param');
        return;
    }
    $item = $params['item'];
    unset($params['item']);
    
    if(!isset($params['page'])) {
        $smarty->trigger_error('[plugin] mask_select needs a "page" param');
        return;
    }
    $page = $params['page'];
    unset($params['page']);    
    
    $ctypeMgr = ContentTypeManager::getInstance();
    $ctype = $ctypeMgr->get($item->fk_content_type);    
    
    $masks = $ctype->getMasksByTheme($page->theme);    
    
    $output = '';
    foreach($masks as $i => $mask) {
        $output .= '<option value="' . $mask['value'] . '"';
        
        if(isset($params['selected']) && ($params['selected'] == $mask['value'])) {
            // Is selected
            $output .= ' selected="selected"';            
        }
        
        $output .= '>' . $mask['title'] . '</option>';
    }
    
    return $output;
}