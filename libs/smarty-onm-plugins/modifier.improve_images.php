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
    $html = preg_replace('@<img(.*)src=@', '<img$1data-src=', $html);
    $html = preg_replace('@<img(.*)class="([^"]+)"?@', '<img$1class="$2 lazyload"', $html);

    // Add the lazy load to the leftovers.
    return preg_replace('@<img(((?!class).)*)/?>@U', '<img$1 class="lazyload">', $html);
}
