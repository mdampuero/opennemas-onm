<?php
/**
 * Smarty plugin to render a banner into OpenNeMas
 * 
 * @param array $params
 * @param Template $tpl Template class which extends of Smarty
 * @param boolean $cache Use cache
 * @todo Pasar toda logica a una clase con metodos para renderizar los distintos tipos de banners
*/
function smarty_function_renderbanner($params, &$smarty, $cache=false) {
    $output = '';

    $banner   = $params['banner'];
    
    // Return file name of template for debug
    /* if(is_null($banner)) {
        $trace = debug_backtrace();            
        $matches = array();
        preg_match('/%([^%]+)\.php$/i', $trace[0]['file'], $matches);                
        return $matches[1];
    } */

    if(is_null($banner) || (defined('ADVERTISEMENT_ENABLE') && !ADVERTISEMENT_ENABLE)) {
        return( $output );
    }
    
    $photo    = $params['photo'];
    
    if(!isset($params['cssclass'])) {
        $smarty->trigger_error("renderbanner: missing 'cssclass' parameter");
        return;
    }    
    $cssclass = $params['cssclass'];
    
    // Extract width and height properties from CSS
    if(!isset($params['width']) || !isset($params['height'])) {
        $width  = preg_replace('/^[a-z]+(\d+)x\d+$/i', '\1', $cssclass);
        $height = preg_replace('/^[a-z]+\d+x(\d+)$/i', '\1', $cssclass);
    } else {
        $width  = $params['width'];
        $height = $params['height'];
    }
    
    // If $height is equals to * then calculate using GD
    if($height == '*') {
        list($w, $h, $type, $attr) = getimagesize( MEDIA_IMG_PATH. $photo->path_file. $photo->name );
        if($w == $width) { // La imagen es proporcional
            $height = $h;
        } else {
            if($w != 0) {
                $height = floor( ($width*$h)/$w );
            } else {
                $height = 0;
            }
        }
    }
    
    // Overlap flash?
    $overlap  = (isset($params['overlap']))? $params['overlap']: false;
    $isBastardIE = preg_match('/MSIE /', $_SERVER['HTTP_USER_AGENT']);
            
    if( isset($params['beforeHTML']) ) {
        $output .= $params['beforeHTML'];
    }
    
    // Initial container
    $output .= '<div class="'.$cssclass.'">';    
    
    if( $banner->with_script == 1 ) {        
        // Original method
        // $output .= $banner->script;
        
        // Parallelized method using iframes
        $output .= '<iframe src="'.SITE_URL.'advertisement.php?action=get&amp;id=' . $banner->pk_content  . '" ' .
                   'scrolling="no" frameborder="0" width="' . $width . '" height="' . $height . '" ' . 
                   'marginwidth="0" marginheight="0" rel="nofollow">Publicidad Xornal.com</iframe>';
        
    } elseif( !empty($banner->pk_advertisement) ) {                
        
        // TODO: controlar los banners swf especiales con div por encima
        if( strtolower($photo->type_img)=='swf' ) {
            if(!$overlap && !$banner->overlap) {
                // Flash object            
                // FIXME: build flash object with all tags and params
                $output .= '<a target="_blank" href="/publicidade/'. $banner->pk_advertisement .'.html" rel="nofollow">';
                $output .= '<object>
                        <param name="movie" value="'. MEDIA_IMG_PATH_WEB. $photo->path_file. $photo->name. '">
                        <param name="width" value="'.$width.'" />
                        <param name="height" value="'.$height.'" />            
                        <embed src="'. MEDIA_IMG_PATH_WEB. $photo->path_file. $photo->name. '"
                            width="'.$width.'" height="'.$height.'" alt="Publicidad '. $banner->title. '"></embed>
                    </object>';
            } else {                
                if(!$isBastardIE) {
                    $output .= '<div style="position: relative; width: '.$width.'px; height: '.$height.'px;">
                        <div style="left:0px;top:0px;cursor:pointer;background-color:transparent;position:absolute;z-index:100;width:'.
                            $width.'px;height:'.$height.'px;"
                            onclick="javascript:window.open(\'/publicidade/'.$banner->pk_advertisement.'.html\', \'_blank\');return false;"></div>';
                } else {
                    $output .= '<div style="position: relative; width: '.$width.'px; height: '.$height.'px;">
                        <div style="left:0px;top:0px;cursor:pointer;background-color:#FFF;filter:alpha(opacity=0);position:absolute;z-index:100;width:'.
                            $width.'px;height:'.$height.'px;"
                            onclick="javascript:window.open(\'/publicidade/'.$banner->pk_advertisement.'.html\', \'_blank\');return false;"></div>';
                }
                
                $output .= '<div style="position: absolute; z-index: 0; width: '.$width.'px; left: 0px;">
                        <object width="'.$width.'" height="'.$height.'">
                            <param name="movie" value="'. MEDIA_IMG_PATH_WEB. $photo->path_file. $photo->name. '">
                            <param name="wmode" value="opaque" />
                            <param name="width" value="'.$width.'" />
                            <param name="height" value="'.$height.'" />            
                            <embed src="'. MEDIA_IMG_PATH_WEB. $photo->path_file. $photo->name. '" wmode="opaque"
                                width="'.$width.'" height="'.$height.'" alt="Publicidad '. $banner->title. '"></embed>
                        </object>
                    </div>
                  </div>';
                  
                $output .= '</div>';
                
                if( isset($params['afterHTML']) ) {
                    $output .= $params['afterHTML'];
                }
                
                return( $output );
            }
        } else {
            // Image
            $output .= '<a target="_blank" href="/publicidade/'. $banner->pk_advertisement .'.html" rel="nofollow">';
            $output .= '<img src="'. MEDIA_IMG_PATH_WEB. $photo->path_file. $photo->name.'"
                    alt="Publicidad '.$banner->title.'" width="'.$width.'" height="'.$height.'" />';
        }
        
        $output .= '</a>';
    } else {
        // Empty banner, don't return anything
        $output = '';
        return( $output );
    }
    
    $output .= '</div>';
    
    // Post content of banner
    if( isset($params['afterHTML']) ) {
        $output .= $params['afterHTML'];
    }

    return( $output );
}