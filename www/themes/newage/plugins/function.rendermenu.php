<?php

function smarty_function_rendermenu($params, &$smarty) {
    $output = '';
    
    $ccm        = $params['ccm'];
    $section    = $params['section'];    
    $tree       = $ccm->getCategoriesTreeMenu();
    
    foreach($ccm->categories as $node) {
        if((preg_match('/'.preg_quote($section).'/i', $node->name)) && ($node->inmenu == 1)) {
            $father_id = $node->fk_content_category;
            break;
        }
    }
    
    foreach($ccm->categories as $node) {
        if((preg_match('/'.preg_quote($section).'/i', $node->name)) && ($node->inmenu == 1)) {
            $section_id = $node->pk_content_category;
            break;
        }
    }
	
    $output .= '<ul class="principal">';
    
    $output .= '<li';
    if($section=='ultimas') {
        $output .= ' class="current"';
    }
    $output .= '><a href="' . BASE_URL . '/ultimas-noticias/">Últimas noticias</a></li>';            
    
    foreach($tree as $item) {
        if($item->inmenu == 1) {
            if((preg_match('/'.preg_quote($section).'/i', $item->name)) || ($item->pk_content_category == $father_id)) {
                $output .= '<li class="current">';
            } else {
                $output .= '<li>';
            }
            
            $output .= '<a href="' . BASE_URL . '/seccion/' . $item->name . '/">' . mb_strtolower($item->title, 'UTF-8') . '</a></li>';            
        }
    }
    $output .= '<br class="clearer" /></ul>';
    
    if(!empty($father_id)) {
        $output .= '<ul class="submenu">';
        foreach($tree[$father_id]->childNodes as $item) {
            if($item->inmenu == 1) {
                if(preg_match('/'.preg_quote($section).'/i', $item->name))  {
                    $output .= '<li class="current">';
                } else {
                    $output .= '<li>';
                }
                
                $output .= '<a href="' . BASE_URL . '/seccion/' . $item->name . '/">' . mb_strtolower($item->title, 'UTF-8') . '</a></li>';            
            }
        }
        $output .= '<br class="clearer" /></ul>';
        
    } elseif( count($tree[$section_id]->childNodes) > 0 ) {
        
        $output .= '<ul class="submenu">';
        foreach($tree[$section_id]->childNodes as $item) {
            if($item->inmenu == 1) {
                if(preg_match('/'.preg_quote($section).'/i', $item->name)) {
                    $output .= '<li class="current">';
                } else {
                    $output .= '<li>';
                }
                
                $output .= '<a href="' . BASE_URL . '/seccion/' . $item->name . '/">' . mb_strtolower($item->title, 'UTF-8') . '</a></li>';            
            }
        }
        $output .= '<br class="clearer" /></ul>';
    }
    
    return $output;
}


function smarty_rendermenu_get_father($section, $tree) {    
            
}