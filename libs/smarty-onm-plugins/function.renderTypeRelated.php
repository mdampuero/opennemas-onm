<?php
/**
 * Returns the css class property adecuate to the content type passed
 *
 * Ejemplo:
 *   <li class="res-file"><a href="#">El viaje al sol, un sueño olvidado (PDF)</a></li>
 *   <li class="res-image"><a href="#">Fototeca: El viaje al sol, un sueño olvidado</a></li>
 *   <li class="res-link"><a href="#">Los grandes pelígros de la humanidad</a></li>
 *   <li class="res-video"><a href="#">Descubre el sistema planetario en este vídeo</a></li>
 *
 * @param array $params the list of parameters
 * @param \Smarty $smarty the smarty instance
 *
 * @return string
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

    $html = sprintf('<a href="%s"><span %s></span>%s</a>', get_url($content), $class, get_title($content));

    return $html;
}
