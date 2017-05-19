<?php

function smarty_function_render_favico($params, &$smarty)
{
    // Default favico
    $favicoUrl = '/assets/images/favicon.png';

    // Check if favico is defined on site
    $favicoFileName  = getService('setting_repository')->get('favico');
    $sectionSettings = getService('setting_repository')->get('section_settings');

    $allowLogo = false;
    if (is_array($sectionSettings) && array_key_exists('allowLogo', $sectionSettings)) {
        $allowLogo = $sectionSettings['allowLogo'];
    }

    if ($allowLogo && $favicoFileName) {
        $favicoUrl = MEDIA_URL . MEDIA_DIR . '/sections/' . rawurlencode($favicoFileName);
    }

    $output = "<link rel='shorcut icon' href='" . $favicoUrl . "'>\n";
    $output .= "\t<link rel='apple-touch-icon' href='" . $favicoUrl . "'>\n";

    $appleSizes = ['57x57', '60x60', '72x72', '76x76', '114x114', '120x120', '144x144', '152x152', '180x180'];
    foreach ($appleSizes as $size) {
        $output .= "\t<link rel='apple-touch-icon' sizes='" . $size . "' href='" . $favicoUrl . "'>\n";
    }

    $iconSizes = ['192x192', '96x96', '32x32', '16x16'];
    foreach ($iconSizes as $size) {
        $output .= "\t<link rel='icon' sizes='" . $size . "' href='" . $favicoUrl . "'>\n";
    }

    // Render favico code
    return $output;
}
