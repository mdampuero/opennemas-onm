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
        '@<figure class="image(.*)"(.*<img.*width="([0-9]+)".*<\/figure>)@sU',
        '<figure class="image $1 ckeditor-image" style="width: $3px"$2',
        $html
    );

    // Use data-src instead of src on images in order to apply lazyload.
    $html = preg_replace('@<img(.*)src=@', '<img$1 data-src=', $html);
    $html = preg_replace('@<img(.*)class="([^"]+)"?@', '<img$1 class="$2 lazyload"', $html);

    // Add the lazy load to the leftovers.
    return preg_replace('@<img(.*)(?!class)/?>@U', '<img$1 class="lazyload">', $html);
}
