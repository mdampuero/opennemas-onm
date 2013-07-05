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
 *
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
            if ((preg_match("/.+\.jpeg|jpg|gif/", $ext))) {
                $class = 'class="res-image" ';
            } elseif ((preg_match("/.+\.doc/", $ext))) {
                $class = 'class="res-file" ';
            } elseif ((preg_match("/.+\.pdf/", $ext))) {
                $class = 'class="res-file" ';
            } else {
                $class = 'class="res-file" ';
            }

            break;
        case 4:
            //Opinion
            $class = 'class="res-opinion" ';

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
    $replace = array('', '','');

    $title_cleaned = preg_replace($patterns, $replace, $content->title);

    $uri = $content->uri;

    if ($content->content_type == 3 || $content->fk_content_type == 3) {
        $pathFile = ContentManager::getFilePathFromId($content->id);
        // Check if is attachment from synchronize
        if ($pathFile) {
            $content->uri = INSTANCE_MEDIA.FILE_DIR . $pathFile;
        } elseif ($content->fullFilePath) {
            $content->uri = $content->fullFilePath;
        }

        $html =' <a title="Relacionado: '.$title_cleaned.'" href="'.$content->uri .'"';
    } else {
        $html =' <a title="Relacionado: '.$title_cleaned.'" href="'. SITE_URL . $uri .'"';
    }

    if ($content->fk_content_type==3) {
        $html.=' target="_blank"';
    }
    $html.='><span '.$class.'>&nbsp;</span>'.clearslash($content->title).'</a>';

    return $html;
}
