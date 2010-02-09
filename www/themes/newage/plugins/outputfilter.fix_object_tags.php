<?php
/**
 * Fix object tags, remove embed tags for this page can be w3c complaint
 *
 */
function smarty_outputfilter_fix_object_tags($tpl_output, &$smarty) {
    
    /* 'smarty_outputfilter_fix_object_tags_callback_xhtml', */
    $tpl_output = preg_replace_callback( '@<object([^>]*>)(.*?<embed.*?)</object>@si',
                                'smarty_outputfilter_fix_object_tags_callback_swfobject',
                                
                                $tpl_output );
    
    $tpl_output = preg_replace( '/@@@PATHIMAGES@@@/', $smarty->image_dir, $tpl_output );
    $tpl_output = preg_replace( '/@@@PATHJS@@@/',     $smarty->js_dir,    $tpl_output );
    
    //$tpl_output = preg_replace('@<object([^>]*>)(.*?<embed.*?)</object>@si', '', $tpl_output);
    //$tpl_output = preg_replace('@<script([^>]*>)(.*?)</script>@si', '', $tpl_output);
    
    return($tpl_output);
}


/* Callbacks ***************************************************************** */

/* Clean embed tags: http://www.alejandroarco.es/seo-y-accesibilidad-web/w3c/validar-flash-en-xhtml-y-html/ */
function smarty_outputfilter_fix_object_tags_callback_xhtml($matches) {
    // <param name="movie" value="/media/images//publicidad/20090123/2009012312063393281.swf"><embed src="/media/images//publicidad/20090123/2009012312063393281.swf" width="728" height="90" alt="PublicidadTelefonica - HOME"></embed>
    $output = '<object type="application/x-shockwave-flash" data="%s">
        <param name="movie" value="%s" />
        <param name="width" value="%d" />
        <param name="height" value="%d" />
        <img src="@@@PATHIMAGES@@@flash-no-available.jpg" alt="Flash Player no disponible" />
    </object>
    ';
    $code = $matches[2];    
    
    preg_match('/width="([0-9]+)"/', $code, $matches);
    $width = $matches[1];
    
    preg_match('/height="([0-9]+)"/', $code, $matches);
    $height = $matches[1];
    
    preg_match('/src="([^"]+)"/', $code, $matches);
    $src = $matches[1];
    
    if( empty($src) ) {
        preg_match('/<param [^>]*name="movie" [^>]*value="([^"]+)"/', $code, $matches);
        $src = $matches[1];
    }
        
    $src = preg_replace('@([^:])//@', '$1/', $src);
    
    return sprintf($output, $src, $src, $width, $height);
}


/* http://www.alejandroarco.es/seo-y-accesibilidad-web/w3c/validar-flash-con-javascript-y-swfobject/ */
function smarty_outputfilter_fix_object_tags_callback_swfobject($matches) {    
    $output = '<div id="%s">
                <img src="@@@PATHIMAGES@@@flash-no-available.jpg" alt="Flash Player no disponible" />
            </div>
            <script type="text/javascript" language="javascript">'."
            swfobject.embedSWF('%s', '%s', '%d',' %d', '9.0.0', '@@@PATHJS@@@expressInstall.swf');
            ".'</script>';
    
    list($usec, $sec) = explode(' ', microtime());      
    srand( (float) $sec + ((float) $usec * 100000) );
    $id = 'swfobject'.rand();
    $code = $matches[2];
    
    preg_match('/width="([0-9]+)"/', $code, $matches);
    $width = $matches[1];
    
    preg_match('/height="([0-9]+)"/', $code, $matches);
    $height = $matches[1];
    
    preg_match('/<embed .*src="([^"]+)"/', $code, $matches);
    $src = $matches[1];
    
    if( empty($src) ) {
        preg_match('/<param .*name="movie" .*value="([^"]+)"/', $code, $matches);
        $src = $matches[1];
    }
    
    $src = preg_replace('@([^:])//@', '$1/', $src);
    
    return sprintf($output, $id, $src, $id, $width, $height);
}