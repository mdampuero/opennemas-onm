<?php
/**
 * Smarty plugin
 * Parse link type="text/stylesheet" tags
 *
*/
function smarty_block_stylesection($params, $content, &$smarty, $open) {
    if( $open ) {
        // NADA
    } else {
        $output = '';
        $matches = array();
        preg_match_all( '@<link .*?href="([^"]+)".*?>@si', $content, $matches );
        
        $section = (!isset($params['name']))? 'head': $params['name'];
        if( !isset( $smarty->css_includes[ $section ] ) ) {
            $smarty->css_includes[ $section ] = array();
        }
        
        $sources = array();
        if( isset($matches[1]) ) {
            foreach($matches[1] as $src) {
                // Clean asset server
                if( defined('ASSET_HOST') ) {
                    $regex = '@^http[s]?://' . str_replace('%02d', '[0-9]{2}', preg_quote(ASSET_HOST)) . '@i';
                    if( preg_match($regex, $src) ) {
                        $src = preg_replace('@^(http[s]?://[^/]*)(/.*?)$@', '\2', $src);
                    }
                }
                
                $source = preg_replace('@'.$smarty->css_dir.'@', '', $src);
                
                if( !in_array($source, $smarty->css_includes[ $section ]) &&
                    !in_array('@-'.$source, $smarty->css_includes[ $section ]) )
                {                    
                    $sources[] = $source;
                }                
            }
        }               
       
        // Concat $prefix if using ASSET_HOST
        $prefix = '';
        if(defined('ASSET_HOST')) {
            if(!defined('NUM_ASSET_HOSTS')) {
                define('NUM_ASSET_HOSTS', 4);
            }
            
            $asset_server = sprintf(ASSET_HOST, rand(1, NUM_ASSET_HOSTS));
            $protocol = (!empty($_SERVER['HTTPS']))? 'https://': 'http://';
            $prefix = $protocol . $asset_server;
        }
       
        
        // include css added programatically
        foreach($smarty->css_includes[ $section ] as $css) {
            if( !preg_match('/^@\-/', $css) ){
                
                $output .= '<link rel="stylesheet" type="text/css" href="' . $prefix . $smarty->css_dir . $css.'" />'."\n";
            }
        }
        
        if(isset($params['compress']) && $params['compress']) {
            // Generate compressed file
            smarty_block_stylesection_compress($sources, $smarty->css_dir, $params['cssfilename']);
            $output .= '<link rel="stylesheet" type="text/css" href="' . $prefix . $smarty->css_dir . $params['cssfilename'] . '" />'."\n";
        } else {
            foreach($sources as $css) {
                $output .= '<link rel="stylesheet" type="text/css" href="' . $prefix . $smarty->css_dir . $css . '" />'."\n";
            }
        }
        
        return $output;
    }
}

function smarty_block_stylesection_compress($sources, $cssDir, $filename)
{
    $path = SITE_PATH . $cssDir;
    // Clean css file names
    foreach($sources as $i => $source) {
        $sources[$i] = $path . preg_replace('/^([^\?]+)(.*?)$/', '\1', $source);
    }
    
    $compress = true;
    
    $compressedFile = $path . $filename;    
    if(file_exists($compressedFile)) {
        $compressedTime = filemtime($compressedFile);
        
        $times = array();
        foreach($sources as $source) {
            $times[] = filemtime($source);
        }
        
        if($compressedTime > max($times)) {
            $compress = false;
        }
    }
    
    if($compress) {
        
        include(SITE_LIBS_PATH . '/csstidy/class.csstidy.php');        
        $css = new csstidy();
        
        $fp = fopen($compressedFile, 'w');
        
        if($fp !== false) {
            // SEE: http://www.fiftyfoureleven.com/weblog/web-development/css/gzipping-your-css-with-php
            $content = '<' . '?php 
ob_start ("ob_gzhandler");
header("Content-type: text/css");
header("Cache-Control: must-revalidate");
header("Expires: " . gmdate("D, d M Y H:i:s", time() + (60*60)) . " GMT");
?' . '>' . "\n";
            
            foreach($sources as $source) {                
                $css_code = file_get_contents($source);
                $css->parse($css_code);
                $css_code = $css->print->plain();
                
                $content .= $css_code;
            }
            
            fputs($fp, $content);
            
            fclose($fp);
        }        
    }
    
}





