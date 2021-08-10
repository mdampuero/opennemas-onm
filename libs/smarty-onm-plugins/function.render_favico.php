<?php
/**
 * Returns the favicon meta tag
 *
 * @param \Smarty $smarty the smarty instance
 *
 * @return string
 */
function smarty_function_render_favico($params, &$smarty)
{
    // Check if favico is defined on site
    $favicoUrl = '/assets/images/favicon.png';

    if (has_logo('favico')) {
        $path      = get_photo_path(get_logo('favico'));
        $favicoUrl = dirname($path)
            . '/'
            . rawurlencode(basename($path));
    }

    $fileInfo  = pathinfo($favicoUrl);
    $extension = $fileInfo['extension'] ?? 'png';

    $output = "<link rel='icon' type='image/" . $extension . "' href='" . $favicoUrl . "'>\n"
        . "\t<link rel='apple-touch-icon' href='" . $favicoUrl . "'>\n";

    $appleSizes = [
        '57x57', '60x60', '72x72', '76x76', '114x114', '120x120',
        '144x144', '152x152', '180x180'
    ];
    foreach ($appleSizes as $size) {
        $output .= "\t<link rel='apple-touch-icon' sizes='" . $size
            . "' href='" . $favicoUrl . "'>\n";
    }

    $iconSizes = ['192x192', '96x96', '32x32', '16x16'];
    foreach ($iconSizes as $size) {
        $output .= "\t<link rel='icon' type='image/" . $extension . "' sizes='" . $size
            . "' href='" . $favicoUrl . "'>\n";
    }

    return $output;
}
