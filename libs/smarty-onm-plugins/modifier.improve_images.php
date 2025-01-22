<?php
/**
 * Smarty lower modifier plugin
 *
 * @param string $html
 *
 * @return string
 */
function smarty_modifier_improve_images($html, $lazyjs = true)
{
    // Add class to identify ckeditor and width to the figure in the html.
    $html = preg_replace(
        '@<figure.*class="image([^"]*)"(.*)>[ \n]*(<img[^>]*width="([0-9]+)"[^>]*\/?>)@U',
        '<figure class="image$1 ckeditor-image" style="max-width: 100%; width: $4px;"$2>$3',
        $html
    );

    $regex = '@<img\s+[^>]*\b(?:width="(\d+)")?[^>]*\b(?:height="(\d+)")?[^>]*\b' .
        'src="((?!https?:\/\/)(?!.*zoomcrop)[^"]+)"[^>]*>@mU';

    if ($lazyjs) {
        // Use data-src instead of src on images in order to apply lazyload.
        $html = preg_replace('@<img(.*)(src|data-src)=@U', '<img$1data-src=', $html);
        $html = preg_replace('@<img(.*)class="([^"]+)"@U', '<img$1class="$2 lazyload"', $html);

        // Add the lazy load to the leftovers.
        $html = preg_replace('@<img(((?!class=).)*)/?>@U', '<img$1 class="lazyload">', $html);

        $regex = '@<img\s+[^>]*\b(?:width="(\d+)")?[^>]*\b(?:height="(\d+)")?[^>]*\b' .
            'data-src="((?!https?:\/\/)(?!.*zoomcrop)[^"]+)"[^>]*>@mU';
    } else {
        $html = preg_replace('@<img(.*)/?>@U', '<img$1 loading="lazy">', $html);
    }

    preg_match_all(
        $regex,
        $html,
        $out
    );

    $out[1] = array_map(function ($img) {
        preg_match('@\bwidth="(\d+)"@', $img, $widthMatch);
        return $widthMatch[1] ?? '0';
    }, $out[0]);

    $out[2] = array_map(function ($img) {
        preg_match('@\bheight="(\d+)"@', $img, $heightMatch);
        return $heightMatch[1] ?? '0';
    }, $out[0]);

    /**
     * out[0] => array of complete img tag matchs,
     * out[1] => array of widths from img match,
     * out[2] => array of heights from img match,
     * out[3] => array of src from img matchs,
     */
    if ($out[0] && !empty($out[0])) {
        $ph = getService('core.helper.photo');
        foreach ($out[0] as $matchKey => $matchValue) {
            $width  = !empty($out[1][$matchKey]) ? (int) $out[1][$matchKey] : PHP_INT_MAX;
            $height = !empty($out[2][$matchKey]) ? (int) $out[2][$matchKey] : 0;
            if ($height > $width) {
                continue;
            }
            $result = $ph->getSrcSetAndSizesFromImagePath($out[3][$matchKey], $width);

            $srcsetAttr = $lazyjs ? 'data-srcset' : 'srcset';
            $sizestAttr = $lazyjs ? 'data-sizes' : 'sizes';
            $html       = preg_replace(
                '@<img((?:(?!srcset).)*src="' . $out[3][$matchKey] . '".*)>@U',
                '<img$1 ' . $srcsetAttr . '="' . $result['srcset'] . '" ' . $sizestAttr . '="auto">',
                $html
            );
        }
    }

    return $html;
}
