<?php

/**
 *
 * <code>
 * {mask_select item=$content page=$page}
 * </code>
 */
function smarty_function_mask_list($params, &$smarty)
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
    
    $output = '<ul>';
    $output .= '<li data-value=""><img src="' . SITE_URL . 'themes/system/images/system.png" alt="" /></li>';
    
    try {
        $ctypeMgr = ContentTypeManager::getInstance();
        $ctype = $ctypeMgr->get($item->fk_content_type);        
        
        $masks = $ctype->getMasksByTheme($page->theme);                
        
        if(count($masks) > 0) {
            foreach($masks as $i => $mask) {
                $output .= '<li data-mask="' . $mask['value'] . '">';        
                $output .= '<img src="' . SITE_URL . 'themes/' . $page->theme . '/tpl/masks/' . $ctype->name .
                    '/' . $mask['title'] . '.png" alt="" /></li>';
            }
        }
    } catch(Exception $ex) {
        // Fault-tolerance        
    }
    
    $output .= '</ul>';        
    
    return $output;
}