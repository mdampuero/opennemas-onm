<?php
function smarty_function_cookie_hint($params, &$smarty)
{
    $cookie = $_COOKIE['cookieoverlay_accepted'];

    if ($_COOKIE['cookieoverlay_accepted'] !== 'true') {

        $message = _(
            'Utilizamos cookies propias y de terceros para mostrarle publicidad relacionada con sus '
            .'preferencias según su navegación.  Si continua navegando consideramos que acepta'
            .' el uso de cookies. <a target="_blank" href="/estaticas/cookies-policies.html"> Más información &gt; </a>'
        );

        $html = '<link rel="stylesheet" type="text/css" href="/assets/css/cookies_overlay.css"></style>">'
                .'<script type="text/javascript" src="/assets/js/onm/jquery.cookies_overlay.js"></script>'
                ."<div id='cookies-overlay'><div class='cookies-overlay'>
      <button data-dismiss='alert' class='closeover' type='button'>×</button>
      <p>$message</p>
    </div></div>";

    }
    return $html;
}
