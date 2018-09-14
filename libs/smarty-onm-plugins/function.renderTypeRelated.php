<?php
/**
 * -------------------------------------------------------------
 * File:        function.typecontent.php
 * Comprueba el tipo y escribe el class adecuado
 *
 * Ejemplo:
 *   <li class="res-file"><a href="#">El viaje al sol, un sueño olvidado (PDF)</a></li>
 *   <li class="res-image"><a href="#">Fototeca: El viaje al sol, un sueño olvidado</a></li>
 *   <li class="res-link"><a href="#">Los grandes pelígros de la humanidad</a></li>
 *   <li class="res-video"><a href="#">Descubre el sistema planetario en este vídeo</a></li>
 */
function smarty_function_renderTypeRelated($params, &$smarty)
{
    $content = $params['content'];

    switch ($content->fk_content_type) {
        case 1:
            // Article
            $class = 'class="res-article" ';
            break;
        case 3:
            // Attachments
            if ((preg_match("/.+\.jpeg|jpg|gif/", $content->path))) {
                $class = 'class="res-image" ';
            } else {
                $class = 'class="res-file" ';
            }
            // Generate correct attachment uri
            if ($content->fullFilePath) {
                $content->uri = $content->fullFilePath;
            } else {
                $path = ContentManager::getFilePathFromId($content->id);
                $content->uri = 'media'.DS.INSTANCE_UNIQUE_NAME.DS.FILE_DIR.$path;
            }
            break;
        case 4:
            //Opinion
            $class = 'class="res-opinion" ';
            $author = new \User($content->fk_author);
            $content->category_name = \Onm\StringUtils::getTitle($author->name);
            break;
        case 7:
            // Album
            $class = 'class="res-image" ';
            break;
        case 8:
            // Photo
            $class = 'class="res-image" ';
            break;
        case 9:
            // Video
            $class = 'class="res-video" ';
            break;
        default:
            // Link
            $class = 'class="res-link" ';
            break;
    }

    $patterns = array('/"/', '/\'/', '/“/');
    $replace = array('', '', '');
    $titleCleaned = preg_replace($patterns, $replace, $content->title);

    $html ='<a title="'.$titleCleaned.'" href="/'.$content->uri .'"';
    if ($content->fk_content_type==3) {
        $html.=' target="_blank"';
    }
    $html.='><span '.$class.'></span>'.clearslash($content->title).'</a>';

    return $html;
}
