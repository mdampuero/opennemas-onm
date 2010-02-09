<?php

/**
 * Smarty plugin to render a intersticial banner into OpenNeMas
 * 
 * @param array $params
 * @param Template $tpl Template class which extends of Smarty
 * @param boolean $cache Use cache
 * @todo Pasar toda logica a una clase con metodos para renderizar los distintos tipos de banners
*/
function smarty_insert_intersticial($params, &$smarty) {
    // nested function to render intersticial
    // FIXME: include function into Advertisement static method 
    if(!function_exists('render_output')) {
        function render_output($content, $banner) {
            if((($banner->type_advertisement + 50)%100) == 0) {
                $content = json_encode($content);
                $content = str_replace('\n', '', $content);
                $content = preg_replace('/[ ][ ]+/', ' ', $content);
                $timeout = intval($banner->timeout) * 1000; // convert to ms
                $pk_advertisement = $banner->pk_advertisement;
                
                /*
                 * intersticial = new IntersticialBanner({iframeSrc: '/sargadelos.html?cacheburst=1254325526',
                 *                                        timeout: -1,
                 *                                        useIframe: true});
                 */
                $output = <<< JSINTERSTICIAL
<script type="text/javascript" language="javascript">
/* <![CDATA[ */
    var intersticial = new IntersticialBanner({
        publiId: "$pk_advertisement",
        cookieName: "ib_$pk_advertisement",
        content: $content,
        timeout: $timeout});
/* ]]> */
</script>
JSINTERSTICIAL;
            
                return $output;
            }
            
            return $content;
        }
    }
    
    $output = '';
    $type = $params['type'];
    if(empty($type) || (defined('ADVERTISEMENT_ENABLE') && !ADVERTISEMENT_ENABLE)) {
        // Banners not enabled
        return $output;
    }
    
    $advertisement = Advertisement::getInstance();    
    
    $banner = $advertisement->fetch('banner' . $type);    
    $photo  = $advertisement->fetch('photo' . $type);
    
    
    if(isset($photo)) {
        $width  = $photo->width;
        $height = $photo->height;
    } else {
        $width  = '100%';
        $height = '100%';
    }
    
    // Overlap flash?
    $overlap  = (isset($params['overlap']))? $params['overlap']: false;
    //$overlap  = (isset($params['cssclass']))? $params['cssclass']: 'wrapper_intersticial';
    $isBastardIE = preg_match('/MSIE /', $_SERVER['HTTP_USER_AGENT']);
            
    if( isset($params['beforeHTML']) ) {
        $output .= $params['beforeHTML'];
    }
    
    // Initial container
    $output .= '<div class="'.$cssclass.'" align="center">';    
    
    if( $banner->with_script == 1 ) {        
        // Original method
        // $output .= $banner->script;
        
        // Parallelized method using iframes
        $output .= '<iframe src="'.SITE_URL.'advertisement.php?action=get&amp;id=' . $banner->pk_content  . '" ' .
                   'scrolling="no" frameborder="0" width="100%" height="100%" ' . 
                   'marginwidth="0" marginheight="0" rel="nofollow">Publicidad Xornal.com</iframe>';
        
    } elseif( !empty($banner->pk_advertisement) ) {                
        
        // TODO: controlar los banners swf especiales con div por encima
        /* if( strtolower($photo->type_img)=='swf' ) {
            // Flash object            
            // FIXME: build flash object with all tags and params
            $output .= '<a target="_blank" href="/publicidade/'. $banner->pk_advertisement .'.html" rel="nofollow">';
            $output .= '<object>                    
                    <param name="movie" value="'. MEDIA_IMG_PATH_WEB. $photo->path_file. $photo->name. '" />
                    <param name="width" value="'.$width.'" />
                    <param name="height" value="'.$height.'" />            
                    <embed src="'. MEDIA_IMG_PATH_WEB. $photo->path_file. $photo->name. '"
                        width="'.$width.'" height="'.$height.'" alt="Publicidad '. $banner->title. '"></embed>
                </object>';
        } */
        
        if( strtolower($photo->type_img)=='swf' ) {
            if(!$overlap && !$banner->overlap) {
                // Flash object            
                // FIXME: build flash object with all tags and params
                $output .= '<a target="_blank" href="/publicidade/'. $banner->pk_advertisement .'.html" rel="nofollow">';
                $output .= '<object>
                        <param name="wmode" value="opaque" />
                        <param name="movie" value="'. MEDIA_IMG_PATH_WEB. $photo->path_file. $photo->name. '" />
                        <param name="width" value="'.$width.'" />
                        <param name="height" value="'.$height.'" />            
                        <embed src="'. MEDIA_IMG_PATH_WEB. $photo->path_file. $photo->name. '"
                            width="'.$width.'" height="'.$height.'" alt="Publicidad '. $banner->title. '"></embed>
                    </object>';
                $output .= '</a>';
            } else {
                
                if(!$isBastardIE) {
                    $output .= '<div style="position:relative;width:'.$width.'px;height:'.$height.'px;">
                        <div style="left:0px;top:0px;cursor:pointer;background-color:transparent;position:absolute;z-index:3344;
                            width:'.$width.'px;height:'.$height.'px;"></div>';
                } else {
                    $output .= '<div style="position:relative;width:'.$width.'px;height:'.$height.'px;">
                        <div style="left:0px;top:0px;cursor:pointer;background-color:#FFF;filter:alpha(opacity=0);position:absolute;z-index:3344;
                            width:'.$width.'px;height:'.$height.'px;"></div>';
                }
                
                $output .= '<div style="position: absolute; z-index: 0; width: '.$width.'px; left: 0px; margin: 0 auto;">
                        <object width="'.$width.'" height="'.$height.'">
                            <param name="wmode" value="opaque" />
                            <param name="movie" value="'. MEDIA_IMG_PATH_WEB. $photo->path_file. $photo->name. '" />
                            <param name="wmode" value="opaque" />
                            <param name="width" value="'.$width.'" />
                            <param name="height" value="'.$height.'" />            
                            <embed src="'. MEDIA_IMG_PATH_WEB. $photo->path_file. $photo->name. '" wmode="opaque"
                                width="'.$width.'" height="'.$height.'" alt="Publicidad '. $banner->title. '"></embed>
                        </object>
                    </div>
                  </div>';                  
                
                if( isset($params['afterHTML']) ) {
                    $output .= $params['afterHTML'];
                }
            }        
        } else {
            // Image
            $output .= '<a target="_blank" href="/publicidade/'. $banner->pk_advertisement .'.html" rel="nofollow">';
            $output .= '<img src="'. MEDIA_IMG_PATH_WEB. $photo->path_file. $photo->name.'"
                    alt="Publicidad '.$banner->title.'" width="'.$width.'" height="'.$height.'" />';
            $output .= '</a>';
        }
    } else {
        // Empty banner, don't return anything
        $output = '';
        return render_output($output, $banner);
    }
    
    $output .= '</div>';
    
    // Post content of banner
    if( isset($params['afterHTML']) ) {
        $output .= $params['afterHTML'];
    }

    return render_output($output, $banner);
}
