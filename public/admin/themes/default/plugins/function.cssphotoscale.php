<?php
if( !function_exists('smarty_function_cssphotoscale') ) {
    function smarty_function_cssphotoscale($params, &$smarty=NULL) {
        // Return HTML 	
        $resolution = $params['resolution'];
        
        $width = $params['width'];
        $height = $params['height'];
        
        if($height>0 and $width>0){ //No divide by 0
            
	        if( $width > $height) {
	            $w = $resolution;        
	            $h = floor( ($height*$w) / $width );
	        } else {
	            $h = $resolution - 4;
	            $w = floor( ($width*$h) / $height );
	        }
            
        } else {
            
        	$w=0;
        	$h=0;
            
        }
        
        if( isset($params['getwidth']) ) {
            
            return( $w );
            
        }
        
        return( 'width: '.$w.'px; height: '.$h.'px;' );
    }
}