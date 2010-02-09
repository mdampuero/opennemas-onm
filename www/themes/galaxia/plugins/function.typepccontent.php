<?php
/*
 * -------------------------------------------------------------
 * File:     	function.typecontent.php
 * Comprueba el tipo y escribe el nombre o la imag
 */


function smarty_function_typepccontent($params,&$smarty) {

    $content_type = $params['content_type'];
 

    switch ($content_type){
        case 1:
            $html.='fotografias';
        break;
        case 2:
            $html.='videos';
        break;
        case 3:
            $html.='cartas';
        break;
        case 4:
            $html.='opiniones';
        break;
        case 6:
            $html.='enquisas';
        break;
        case 7:
            $html.='comentarios';
        break;
        default:
            $html.='';
         break;
    }

	return $html;
}