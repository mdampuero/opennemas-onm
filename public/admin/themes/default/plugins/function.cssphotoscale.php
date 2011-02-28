<?php
if( !function_exists('smarty_function_cssphotoscale') ) {
    function smarty_function_cssphotoscale($params, &$smarty=NULL) {
        // Return HTML 	
        $resolution = $params['resolution'];
        
        $width = $params['width'];
        $height = $params['height'];
        
        if($height>0 and $width>0){ //No divide by 0
            
	        if( $width > $height) {
	            $w = $resolution - 1;        
	            $h = floor( ($height*$w) / $width );
				
	        } elseif ($width == $height) {
				
				$w = $h = $resolution;
				
			} else {
				
	            $h = $resolution - 3;
	            $w = floor( ($width*$h) / $height );
	        }
            
        } else {
            
        	$w=$resolution;
        	$h=$resolution;
            
        }
        
        if( isset($params['getwidth']) ) {
            
            return( $w );
            
        }
		
        return( 'width: '.$w.'px; height: '.$h.'px;' );
    }
}