<?php
function smarty_function_render_favico($params, &$smarty)
{
    // Use our default favicon
    $output =
        "<link rel='shorcut icon' href='/assets/images/favicon.png' />\n".
        "<link rel='apple-touch-icon' href='/assets/images/favicon.png' />\n".
        "<link rel='apple-touch-icon-precomposed' href='/assets/images/favicon.png' />\n";

    // Check if the user wants to use the logo
    $allowLogo = false;
    if ($sectionSettings = getService('setting_repository')->get('section_settings')) {
        $allowLogo = (bool) $sectionSettings['allowLogo'];
    }

    // If user wants to user the logo and the favicon is defined the use it
    if ($allowLkogo && $favico = getService('setting_repository')->get('favico')) {
        $favicoPath = MEDIA_URL.MEDIA_DIR."/sections/".rawurlencode($favico);
        $output =
            "<link rel='shortcut icon' href='{$favicoPath}'/>\n".
            "<link rel='apple-touch-icon' href='{$favicoPath}'/>\n".
            "<link rel='apple-touch-icon-precomposed' href='{$favicoPath}'/>\n";
    }

    return $output;
}
