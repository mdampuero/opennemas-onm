<?php
use Onm\Settings as s;

function smarty_function_render_favico($params, &$smarty)
{
    // Default favico code
    $output = '<link rel="shorcut icon" href="/assets/images/favicon.png" />
    <link rel="apple-touch-icon" href="/assets/images/favicon.png" />
    <link rel="apple-touch-icon-precomposed" href="/assets/images/favicon.png" />';

    // Check if is allowed logo on site
    $allowLogo = false;
    if ($sectionSettings = s::get('section_settings')) {
        $allowLogo = $sectionSettings['allowLogo'];
    }

    // Check if is allowed logo and favico exists
    $favicoPath = MEDIA_URL.MEDIA_DIR.'/sections/';
    if ($allowLogo && $favico = s::get('favico')) {
        $output = '<link rel="shortcut icon" href="'.$favicoPath.rawurlencode($favico).'"/>
    <link rel="apple-touch-icon" href="'.$favicoPath.rawurlencode($favico).'"/>
    <link rel="apple-touch-icon-precomposed" href="'.$favicoPath.rawurlencode($favico).'"/>';
    }

    // Render favico code
    return $output;
}
