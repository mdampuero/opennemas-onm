<?php
/**
 * Smarty lower modifier plugin
 *
 * @param string $html
 *
 * @return string
 */
function smarty_modifier_improve_images($html)
{
    // Add class to identify ckeditor and width to the figure in the html.
    $html = preg_replace(
        '@<figure.*class="image([^"]*)"(.*)>[ \n]*(<img[^>]*width="([0-9]+)"[^>]*\/?>)@U',
        '<figure class="image$1 ckeditor-image" style="width: $4px"$2>$3',
        $html
    );

    // Use data-src instead of src on images in order to apply lazyload.
    $html = preg_replace('@<img(.*)src=@U', '<img$1data-src=', $html);
    $html = preg_replace('@<img(.*)class="([^"]+)"@U', '<img$1class="$2 lazyload"', $html);

    // Add the lazy load to the leftovers.
    $html = preg_replace('@<img(((?!class).)*)/?>@U', '<img$1 class="lazyload">', $html);

    preg_match_all(
        '/<img[^>]*(?(?=width)width="([0-9]+")|(?!.width="))[^>]*data-src="([^"]+)"[^>]+>/mU',
        $html,
        $out,
        PREG_OFFSET_CAPTURE
    );

    /*
     * out[0] => array of complete img tag matchs,
     * out[1] => array of widths from img match,
     * out[2] => array of src from img matchs,
     *
     * out[1][0] and out[2][0] correspond to width and src from first img tag match (out[0][0])
     */
    if ($out[0] && !empty($out[0])) {
        $ph = getService('core.helper.photo');
        foreach ($out[0] as $matchKey => $matchValue) {
            $width = $out[1][$matchKey][0] ? (int) $out[1][$matchKey][0] : 9999;

            $result = $ph->getSrcSetAndSizesFromImagePath($out[2][$matchKey][0], $width);

            $html = preg_replace(
                '@<img(((?!srcset).)*src="'
                . $out[2][$matchKey][0] .
                '".*)>@U',
                '<img$1 data-srcset="'
                . $result['srcset'] .
                '" sizes="' . $result['sizes'] . '">',
                $html
            );
        }
    }
    return $html;
}
