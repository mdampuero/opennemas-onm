<?php
/*
 * -------------------------------------------------------------
 * File:     	function.typecontent.php
 * Comprueba el tipo y escribe el nombre o la imag
 */


function smarty_function_typecontent($params,&$smarty) {

    $content = $params['content'];
 
    $view_date = $params['view_date'];
$view_date=0; //Para que no muestre sección / fecha / hora. ticket 1029
       if($view_date==1){
                $html=' <div class="item_related">';
       }
    switch ($content->fk_content_type){
        case 1:
            $html.='<div class="article_icon"> ';
        break;
        case 3: //    /.+\.(jpeg|jpg|gif)/
            if ((preg_match("/.+\.jpeg|jpg|gif/", $ext))) {
                $html.='<div class="image_icon">  ';
            }elseif ((preg_match("/.+\.doc/", $ext))) {
                $html.='<div class="file_icon"> ';
            }elseif ((preg_match("/.+\.pdf/", $ext))) {
                $html.='<div class="file_icon">  ';
            }else{
                $html.='<div class="file_icon">';
            }
       
        break;
        case 4:
            $html.='<div class="opinion_icon">';
        break;       
        case 7:
            $html.='<div class="image_icon">';
        break;        
        case 8:
            $html.='<div class="image_icon">';
        break;
         case 9:
            $html.='<div class="video_icon">';
        break;
        default:
            $html.='<div class="item_icon">';
         break;
    }
        $html.='     <div class="headline">'.
                         '<a class="related_link" href="'.$content->permalink.'"';
                        if(($content->fk_content_type==3)) { $html.='target="_blank"'; }
         $html.='   >'.clearslash($content->title).'</a>';
                           
         $html.='              </div>';

         if($view_date==1){
                $html.='       <span class="section">';
                        if (!empty($content->category_name)){
                            $html.= strtoupper($content->category_name).'-';
                        }
                       $html.=' </span>';
                     $html.='   <span class="date"> '.date("d-m-Y H:i",strtotime($content->changed)).'</span>';
                      $html.='  </div>         ';
             }
         $html.='  </div>         ';

         
	return $html;
}