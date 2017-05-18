<?php
use Onm\Settings as s;

function smarty_function_render_favico($params, &$smarty)
{
    // Default favico code
    $output = '<link rel="shorcut icon" href="/assets/images/favicon.png" />
    <link rel="apple-touch-icon" href="/assets/images/favicon.png" />
    <link rel="apple-touch-icon" sizes="57x57" href="/assets/images/favicon.png" />
    <link rel="apple-touch-icon" sizes="60x60" href="/assets/images/favicon.png" />
    <link rel="apple-touch-icon" sizes="72x72" href="/assets/images/favicon.png" />
    <link rel="apple-touch-icon" sizes="76x76" href="/assets/images/favicon.png" />
    <link rel="apple-touch-icon" sizes="114x114" href="/assets/images/favicon.png" />
    <link rel="apple-touch-icon" sizes="120x120" href="/assets/images/favicon.png" />
    <link rel="apple-touch-icon" sizes="144x144" href="/assets/images/favicon.png" />
    <link rel="apple-touch-icon" sizes="152x152" href="/assets/images/favicon.png" />
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/images/favicon.png" />
    <link rel="icon" sizes="192x192"  href="/assets/images/favicon.png">
    <link rel="icon" sizes="32x32" href="/assets/images/favicon.png">
    <link rel="icon" sizes="96x96" href="/assets/images/favicon.png">
    <link rel="icon" sizes="16x16" href="/assets/images/favicon.png">';

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
    <link rel="apple-touch-icon" sizes="57x57" href="'.$favicoPath.rawurlencode($favico).'" />
    <link rel="apple-touch-icon" sizes="60x60" href="'.$favicoPath.rawurlencode($favico).'" />
    <link rel="apple-touch-icon" sizes="72x72" href="'.$favicoPath.rawurlencode($favico).'" />
    <link rel="apple-touch-icon" sizes="76x76" href="'.$favicoPath.rawurlencode($favico).'" />
    <link rel="apple-touch-icon" sizes="114x114" href="'.$favicoPath.rawurlencode($favico).'" />
    <link rel="apple-touch-icon" sizes="120x120" href="'.$favicoPath.rawurlencode($favico).'" />
    <link rel="apple-touch-icon" sizes="144x144" href="'.$favicoPath.rawurlencode($favico).'" />
    <link rel="apple-touch-icon" sizes="152x152" href="'.$favicoPath.rawurlencode($favico).'" />
    <link rel="apple-touch-icon" sizes="180x180" href="'.$favicoPath.rawurlencode($favico).'" />
    <link rel="icon" sizes="192x192"  href="'.$favicoPath.rawurlencode($favico).'">
    <link rel="icon" sizes="32x32" href="'.$favicoPath.rawurlencode($favico).'">
    <link rel="icon" sizes="96x96" href="'.$favicoPath.rawurlencode($favico).'">
    <link rel="icon" sizes="16x16" href="'.$favicoPath.rawurlencode($favico).'">';
    }

    // Render favico code
    return $output;
}
