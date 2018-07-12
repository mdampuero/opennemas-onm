<?php
/* {renderplaceholder items=$articles tpl='xxx.tpl' placeholder="placeholder_0_0"} */
function smarty_function_renderplaceholder($params, &$smarty)
{
    $outputHTML = '';

    // Get all the parameters passed to the function
    $items       = $params['items'];
    $tpl         = $params['tpl'];
    $placeholder = $params['placeholder'];
    $cssclass    = $params['cssclass'];
    $order       = (array_key_exists('order', $params)) ?
        $params['order'] :
        'normal';
    unset($params['items']);

    // Assign smarty variables to params
    $params['category_name'] = $smarty->getTemplateVars('category_name');
    $contentPositionByPos    = $smarty->getTemplateVars('contentPositionByPos');


    // Doing some checks if this method was called properly
    if (!isset($items)) {
        throw new Exception('RenderPlaceHolder: you must specify the items param');
    }

    // Iterate over all the items and try to get its html representation
    $tpl     = getService('core.template');
    $caching = $tpl->getCaching();

    $tpl->setCaching(\Smarty::CACHING_OFF);

    if (array_key_exists($placeholder, $contentPositionByPos)) {
        $count = 0;
        foreach ($contentPositionByPos[$placeholder] as $contentPosition) {
            if (!array_key_exists($contentPosition->pk_fk_content, $items)) {
                continue;
            }
            $content                  = $items[$contentPosition->pk_fk_content];
            $content->render_position = $count++;
            $outputHTML              .= $content->render($params, $smarty);
        }
    }

    $tpl->caching = $caching;

    // Return all the html collected
    return $outputHTML;
}
