<?php
use \Onm\Settings as s;

function smarty_outputfilter_generate_fb_admin_tag($output, &$smarty)
{
    // Get facebook settings
    $facebookSettings = s::get('facebook');
    $commentSystem    = s::get('comment_system');

    // Check facebook api_key
    $value = trim($facebookSettings['api_key']);
    if ($commentSystem == 'facebook' && !empty($value)) {
        $tag = '<meta property="fb:app_id" content="'.$facebookSettings['api_key'].'"/>';
        $output = str_replace('</head>', $tag.'</head>', $output);
    }

    return $output;
}
