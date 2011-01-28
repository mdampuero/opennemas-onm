<?php
/*
 * -------------------------------------------------------------
 * File:     	function.typecontent.php
 * Comprueba el tipo y escribe el class adecuado

Ejemplo:
    <li class="res-file"><a href="#">El viaje al sol, un sueño olvidado (PDF)</a></li>
    <li class="res-image"><a href="#">Fototeca: El viaje al sol, un sueño olvidado</a></li>
    <li class="res-link"><a href="#">Los grandes pelígros de la humanidad</a></li>
    <li class="res-video"><a href="#">Descubre el sistema planetario en este vídeo</a></li>
 *
 */
function smarty_function_renderTypeRelated($params,&$smarty) {

    $content = $params['content'];

    switch ($content->fk_content_type){
        case 1:
            $class='class="res-article" ';
        break;
        case 3: //    /.+\.(jpeg|jpg|gif)/
            if ((preg_match("/.+\.jpeg|jpg|gif/", $ext))) {
                 $class='class="res-image" ';
            }elseif ((preg_match("/.+\.doc/", $ext))) {
                $class='class="res-file" ';
            }elseif ((preg_match("/.+\.pdf/", $ext))) {
                 $class='class="res-file" ';
            }else{
                $class='class="res-file" ';
            }

        break;
        case 4://Opinion
             $class='class="res-opinion" ';
        break;
        case 7:
             $class='class="res-image" ';
        break;
        case 8:
             $class='class="res-image" ';
        break;
        case 9:
             $class='class="res-video" ';
        break;
        default:
             $class='class="res-link" ';
         break;
    }
    
    $patterns = array('/"/', '/\'/', '/“/');
    $replace = array('', '','');
       
    $title_cleaned = preg_replace($patterns,$replace, $content->title);
    $html=' <a title="Related: '.$title_cleaned.'" href="'.$content->permalink.'"';
                if(($content->fk_content_type==3)) { $html.='target="_blank"'; }
    $html.='   ><span '.$class.'>&nbsp;</span>'.clearslash($content->title).'</a>';


    return $html;
    
}