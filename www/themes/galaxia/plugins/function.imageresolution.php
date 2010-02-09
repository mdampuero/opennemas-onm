<?php

function smarty_function_imageresolution($params, &$smarty) {
    $output = '';
    
    $image  = $params['image'];
    $width  = $params['width'];
    $height = $params['height'];    
    
    $filename = realpath(MEDIA_IMG_PATH.$image);
    if( file_exists($filename) ) {
        $resolution = getimagesize( $filename );
        
        if(($resolution[0] < $width) && ($resolution[1] < $height)) {
            $output = 'style="margin-top: '.($height-$resolution[1]).'px;"';
            
        } else {
            
            if($resolution[0] >= $resolution[1]) {
                $h = floor(($width * $resolution[1]) / $resolution[0]);
                $output = 'width="'.$width.'" height="'.$h.'" style="margin-top: '.($height-$h).'px;"';
                
            } else {
                $w = floor(($height * $resolution[0]) / $resolution[1]);
                $output = 'width="'.$w.'" height="'.$height.'"';
            }
        }    
    }
    
    return( $output );
}