<?php

function smarty_function_imageattrs($params, &$smarty)
{
    $output = '';
    
    $image  = $params['image'];
    $width  = $params['width'];
    $height = $params['height'];    
    
    $filename = realpath(MEDIA_IMG_PATH.$image);
    if( file_exists($filename) ) {
        $resolution = getimagesize( $filename );
        
        $output = 'width="' . $resolution[0] . '" height="' . $resolution[1] . '"';
    }
    
    return $output;
}