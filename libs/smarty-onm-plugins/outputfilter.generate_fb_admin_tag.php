<?php
function smarty_outputfilter_generate_fb_admin_tag($output, $smarty)
{
    $settings = $smarty->getContainer()
        ->get('orm.manager')
        ->getDataSet('Settings')
        ->get([ 'facebook', 'comment_system' ]);

    if (empty($settings['comment_system'])
        || $settings['comment_system'] !== 'facebook'
        || empty($settings['facebook']['api_key'])
    ) {
        return $output;
    }

    $tag = sprintf(
        '<meta property="fb:app_id" content="%s"/>',
        $settings['facebook']['api_key']
    );

    return str_replace('</head>', $tag . '</head>', $output);
}
