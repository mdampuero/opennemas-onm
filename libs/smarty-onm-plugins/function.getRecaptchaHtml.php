<?php

use Onm\Settings as s;

function smarty_function_getRecaptchaHtml($params, &$smarty)
{
    // Get settings repository
    $sm = getService('setting_repository');

    // Get system language
    $lang = CURRENT_LANGUAGE;

    // Get publicKey if is not in params
    if (!isset($params['onm'])) {
        $recaptcha = $sm->get('recaptcha');
        // If not set public key, show a message
        if (!is_array($recaptcha) || !array_key_exists('public_key', $recaptcha)) {
            return _('reCaptcha public key is not set');
        }
    } else {
        $recaptcha['public_key'] = getContainerParameter('recaptcha_public_key');
    }

    // Generate reCaptcha html
    $output =
        "<script type=\"text/javascript\" src=\"https://www.google.com/recaptcha/api.js?hl=".$lang."\"></script>\n".
        "<div class=\"g-recaptcha\" data-sitekey=".$recaptcha['public_key']."></div>";

    return $output;
}
