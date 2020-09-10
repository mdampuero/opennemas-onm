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
    $attrs   = '';

    switch ($content->fk_content_type) {
        case 1: // Article
            $attrs = 'class="res-article" ';
            break;
        case 3: // Attachments
            $attrs = 'class="res-file"';
            break;
        case 4: //Opinion
            $attrs = 'class="res-opinion" ';
            break;
        case 7: // Album
            $attrs = 'class="res-image" ';
            break;
        case 9:
            // Video
            $attrs = 'class="res-video" ';
            break;
        default:
            // Link
            $attrs = 'class="res-link" ';
            break;
    }

    if (!$content instanceof \Content) {
        return;
    }

    return sprintf(
        '<a href="%s"><span %s></span>%s</a>',
        $smarty->getContainer()->get('core.helper.url_generator')->generate($content),
        $attrs,
        $content->title
    );
}
