<?php

function smarty_function_include_piwik_code($params, &$smarty)
{
    // Fetch parameters
    $onlyImage = (isset($params['onlyimage']) ? $params['onlyimage'] : null);

    $code = '';
    if (!is_null($onlyImage) && $onlyImage=="true") {
        $code = getPiwikCode('image');
    }

    return $code;
}
