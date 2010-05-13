<?php
/**
 * Smarty plugin
 * Parse script tags
 *
*/
function smarty_block_scriptsection($params, $content, &$smarty, $open) {
    if( $open ) {
        // NADA
    } else {
        $output = '';
        
        // Create internal array if it doesn't exists
        $section = (!isset($params['name']))? 'head': $params['name'];
        if( !isset( $smarty->js_includes[ $section ] ) ) {
            $smarty->js_includes[ $section ] = array(
                'attrs' => array(),
                'deny' => array(),
            );
        }
        
        // Parse content block
        $matches = array();
        preg_match_all( '@<script(?P<attrs>.*?)></script>@si', $content, $matches );
        
        // Parse attributes and cdata
        $tags = array();
        foreach($matches['attrs'] as $element) {
            $attributes = array();
            preg_match_all('@(?P<k>[a-z][a-z0-9\:_\-]+)="(?P<v>[^"]+)"@si', $element, $attributes);
            
            $tmp = array();
            foreach($attributes['k'] as $i => $attr) {
                $tmp[$attr] = $attributes['v'][$i];
            }            
            
            if(isset($tmp['src'])) {
                $src = $tmp['src'];
                unset($tmp['src']);
                
                $tags[] = array('src' => $src, 'attrs' => $tmp);
                //array_unshift(&$smarty->js_includes[ $section ]['tags'], array('src' => $src, 'attrs' => $tmp));
            }
        }
        
        // Merge arrays
        if(!isset($smarty->js_includes[ $section ]['tags'])) {
            $smarty->js_includes[ $section ]['tags'] = array();
        }
        $smarty->js_includes[ $section ]['tags'] = array_merge($tags, $smarty->js_includes[ $section ]['tags']);                        
        
        foreach($smarty->js_includes[ $section ]['tags'] as $tag) {
            $source = preg_replace('@'.$smarty->js_dir.'@', '', $tag['src']);
            
            if( !isset($smarty->js_includes[ $section ]['deny']) ||
                !in_array($source, $smarty->js_includes[ $section ]['deny']) ) {
                
                $output .= '<script src="'.$smarty->js_dir.$source.'"';
                $output .= smarty_block_scriptsection_render_attributes($tag['attrs']) . '></script>'."\n";
            }
        }
        
        return( $output );
    }
}

function smarty_block_scriptsection_render_attributes($attrs) {
    $output = '';            
    if(!isset($attrs['type'])) $attrs['type'] = 'text/javascript';            
    if(!isset($attrs['language'])) $attrs['language'] = 'javascript';
    
    foreach($attrs as $a => $v) {
        $output .= ' ' . $a . '="' . $v . '"';
    }            
    return $output;
}