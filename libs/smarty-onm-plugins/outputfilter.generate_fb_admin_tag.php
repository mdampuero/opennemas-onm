<?php
function smarty_outputfilter_generate_fb_admin_tag($output, $smarty)
{
    $settings = $smarty->getContainer()
        ->get('orm.manager')
        ->getDataSet('Settings')
        ->get([ 'comment_settings' ])['comment_settings'];

    if (empty($settings)) {
        return $output;
    }

    $system = array_key_exists('comment_system', $settings)
        ? $settings['comment_system']
        : null;

    if (empty($system)
        || $system !== 'facebook'
        || empty($settings['facebook_apikey'])
    ) {
        return $output;
    }

    $tag = sprintf(
        '<meta property="fb:app_id" content="%s"/>',
        $settings['facebook_apikey']
    );

    return str_replace('</head>', $tag . '</head>', $output);
}
