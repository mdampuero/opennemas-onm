<?php
/**
 * Smarty plugin
 * Parse meta tags
 *
*/
function smarty_block_meta($params, $content, &$smarty, $open) {
    if( $open ) {
        // NADA
    } else {
        $output = '';
        
        // Extract all meta-tags
        $matches = array();
        preg_match_all( '@<meta([^>]+)>@si', $content, $matches );        
        
        $metatags = array();
        foreach($matches[1] as $entry) {
            $matchAttr = array();
            preg_match('/(http\-equiv|name)="(.*?)"/si', $entry, $matchAttr);            
            $nameAttr = $matchAttr[2];
            
            $matchAttr = array();
            preg_match('/content="(.*?)"/si', $entry, $matchAttr);            
            $contentAttr = $matchAttr[1];                        
            
            $metatags[$nameAttr] = $contentAttr;
        }
        
        // Remove meta-tags of <head> section
        $content = preg_replace('@<meta([^>]+)>@si', '', $content);
        $content = preg_replace('@[ ][ ]+@si', ' ', $content);        
        $output = $content;
        
        // Create metatags included from PHP code
        foreach($smarty->metatags as $k => $v) {
            // Remove previous metatag
            if(isset($metatags[$k])) {
                unset($metatags[$k]);
            }
            
            if($smarty->isHttpEquiv($k)) {
                $output .= '<meta http-equiv="' . $k . '" content="' . $v . '" />' . PHP_EOL;
            } else {
                $output .= '<meta name="' . $k . '" content="' . $v . '" />' . PHP_EOL;
            }
        }
        
        // Leave other tags
        foreach($metatags as $k => $v) {            
            if($smarty->isHttpEquiv($k)) {
                $output .= '<meta http-equiv="' . $k . '" content="' . $v . '" />' . PHP_EOL;
            } else {
                $output .= '<meta name="' . $k . '" content="' . $v . '" />' . PHP_EOL;
            }
        }        
        
        return $output;
    }
}