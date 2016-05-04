<?php

function smarty_outputfilter_generate_fb_pages_tag($output, $smarty)
{
    if (\Onm\Module\ModuleManager::isActivated('FIA_MODULE')) {
        // Get facebook settings
        $facebookSettings = getService('setting_repository')->get('facebook');

        // Check facebook instant articles config
        if (!empty($facebookSettings) &&
            array_key_exists('instant_articles_tag', $facebookSettings) &&
            !empty($facebookSettings['instant_articles_tag'])
        ) {
            $value = trim($facebookSettings['instant_articles_tag']);
            $tag = '<meta property="fb:pages" content="'.$value.'"/>';
            $output = str_replace('</head>', $tag.'</head>', $output);
        }
    }

    return $output;
}
