<?php

function smarty_function_include_google_analytics_code($params, &$smarty)
{
    // Fetch parameters
    $onlyImage = (isset($params['onlyimage']) ? $params['onlyimage'] : null);

    $code = '';
    if (!is_null($onlyImage) && $onlyImage=="true") {
        $code = getGoogleAnalyticsCode([ 'type' => 'image']);
    }

    return $code;
}
