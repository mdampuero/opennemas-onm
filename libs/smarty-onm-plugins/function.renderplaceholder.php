<?php
/* {renderplaceholder items=$articles tpl='xxx.tpl' placeholder="placeholder_0_0"} */
function smarty_function_renderplaceholder($params, &$smarty)
{
    $outputHTML    = '';

    // Get all the parameters passed to the function
    $items       = $params['items'];
    $tpl         = $params['tpl'];
    $placeholder = $params['placeholder'];
    $cssclass    = $params['cssclass'];
    $order       = (array_key_exists('order', $params))? $params['order'] : 'normal';
    unset($params['items']);

    // Assign smarty variables to params
    $params['category_name'] = $smarty->getTemplateVars('category_name');

    // Doing some checks if this method was called properly
    if (!isset($items)) {
        throw new Exception('RenderPlaceHolder: you must specify the items param');
    }

    // Iterate over all the items and try to get its html representation
    $tpl     = getService('core.template');
    $caching = $tpl->getCaching();

    $tpl->setCaching(\Smarty::CACHING_OFF);

    $filteredContents = [];
    if (isset($items) && count($items>0)) {
        foreach ($items as $i => $item) {
            if ($item->placeholder == $placeholder && ($item->content_status == 1)) {
                $filteredContents []= $item;
            }
        }
        $count=0;
        $filteredContents = \Onm\LayoutManager::orderContents($filteredContents, $order);
        foreach ($filteredContents as $content) {
            $content->render_position = $count++;
            $outputHTML .= $content->render($params, $smarty);
        }
    }

    $tpl->caching = $caching;

    // Return all the html collected
    return $outputHTML;
}
