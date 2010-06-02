<?php
/**
 * Smarty plugin
 * Parse link tags
 *
*/
function smarty_block_stylesection($params, $content, &$smarty, $open) {
    if( $open ) {
        // NADA
    } else {
        $output = '';
        
        // Create internal array if it doesn't exists
        $section = (!isset($params['name']))? 'head': $params['name'];
        if( !isset( $smarty->css_includes[ $section ] ) ) {
            $smarty->css_includes[ $section ] = array(
                'attrs' => array(),
                'deny' => array(),
            );
        }
        
        // Parse content block
        $matches = array();
        preg_match_all( '@<link(?P<attrs>.*?)[/]?>@si', $content, $matches );                
        
        // Parse attributes and cdata
        $tags = array();
        foreach($matches['attrs'] as $element) {
            $attributes = array();
            preg_match_all('@(?P<k>[a-z][a-z0-9\:_\-]+)="(?P<v>[^"]+)"@si', $element, $attributes);                        
            
            $tmp = array();
            foreach($attributes['k'] as $i => $attr) {
                $tmp[$attr] = $attributes['v'][$i];
            }            
            
            if(isset($tmp['href'])) {
                $href = $tmp['href'];
                unset($tmp['href']);
                
                $tags[] = array('href' => $href, 'attrs' => $tmp);
            }
        }
        
        // Merge arrays
        if(!isset($smarty->css_includes[ $section ]['tags'])) {
            $smarty->css_includes[ $section ]['tags'] = array();
        }
        $smarty->css_includes[ $section ]['tags'] = array_merge($tags, $smarty->css_includes[ $section ]['tags']);        
        
        foreach($smarty->css_includes[ $section ]['tags'] as $tag) {
            $source = preg_replace('@'.$smarty->css_dir.'@', '', $tag['href']);
            
            if( !isset($smarty->css_includes[ $section ]['deny']) ||
                !in_array($source, $smarty->css_includes[ $section ]['deny']) ) {
                
                $cssDir = '';
                if(!preg_match('|^http://|', $source)) {
                    $cssDir = $smarty->css_dir;
                }
                
                $output .= '<link href="'.$cssDir.$source.'"';
                $output .= smarty_block_scriptstyle_render_attributes($tag['attrs']) . ' />'."\n";
            }
        }
        
        return( $output );
    }
}

function smarty_block_scriptstyle_render_attributes($attrs) {
    $output = '';            
    if(!isset($attrs['type'])) $attrs['type'] = 'text/css';            
    if(!isset($attrs['rel']))  $attrs['rel']  = 'stylesheet';
    
    foreach($attrs as $a => $v) {
        $output .= ' ' . $a . '="' . $v . '"';
    }            
    return $output;
}