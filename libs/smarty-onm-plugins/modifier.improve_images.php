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
    $html = preg_replace('@<figure class="image"@s', '<figure class="image ckeditor-image"', $html);

    return preg_replace('@<img(.*)src=@', '<img$1 class="lazyload" data-src=', $html);
}
