<?php
use Onm\Settings as s;
function smarty_function_cookie_hint($params, &$smarty)
{
    $html = '';

    $enabled = s::get('cookies_hint_enabled');

    if ($enabled
        && !array_key_exists('cookieoverlay_accepted', $_COOKIE)
    ) {
        $message = _(
            'We use cookies to offer you a better experience. By using this site you agree to our use of cookies. <a target="_blank" href="/estaticas/cookies-policies.html"> Learn more &gt; </a>'
        );

        $html = '<link rel="stylesheet" type="text/css" href="/assets/css/cookies_overlay.css"></style>'
                ."<script type='text/javascript'>$(function() {
                    $('#cookies_overlay').on('click', '.closeover', function(e, ui) {
                        $(this).closest('#cookies_overlay').hide();
                        $.cookie('cookieoverlay_accepted', true);
                    });

                });
                </script>"
                ."<div id='cookies_overlay'><div class='cookies-overlay'>
                  <button data-dismiss='alert' class='closeover' type='button'>Aceptar</button>
                  <p>$message</p>
                </div></div>";
    }
    return $html;
}
