<?php
/*
 * -------------------------------------------------------------
 * File:     	function.typecontent.php
 * Comprueba el tipo y escribe el nombre o la imag
 */
function smarty_function_breadcrub($params) {
    $output = '';
    if(!isset($params['values']) || count($params['values']) <= 0) {
        return $output;
    }
    
    $values = $params['values'];    
    
    // check if $values is empty
    
    
    $output = '<ul class="breadcrub">';
    for($i=0, $total=count($values); $i<$total; ++$i) {
        $output .= '<li><a href="' . $values[$i]['link'] . '" title="' .
            $values[$i]['text'] . '">' . $values[$i]['text'] . '</a>';
        $output .= '</li>';
            
        if(isset($values[ $i+1 ])) {
            $output .= '<li>';
            //$output .= ' &raquo; ';
            $output .= ' / ';
            $output .= '</li>';
        }
    }
    $output .= '</ul>';
    
	return $output;
}