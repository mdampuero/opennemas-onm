<?php
/**
 * Smarty plugin for rendering a banner for a position
 *
 * @param array $params
 * @param Template $tpl Template class which extends of Smarty
*/
function smarty_insert_renderbanner($params, &$smarty)
{
    // Get required params
    $type     = $params['type'];
    $ads      = $smarty->tpl_vars['advertisements']->value;
    $category = $smarty->tpl_vars['category']->value;

    // Filter advertisements by position
    $ads = array_filter(
        $ads,
        function ($ad) use ($type) {
            if ($ad->type_advertisement == $type) {
                return true;
            }
            return false;
        }
    );

    // Render the advertisement content
    $content = '';
    if (count($ads) > 0) {
        // Pick one random advertisement from those available
        $selectedAd = $ads[array_rand($ads)];

        $content = $selectedAd->render($params);
    }

    return $content;
}
