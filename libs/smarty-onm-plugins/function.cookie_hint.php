<?php
use Onm\Settings as s;

function smarty_function_cookie_hint($params, &$smarty)
{
    $html = '';

    $enabled = s::get('cookies_hint_enabled');

    if ($enabled) {
        $url = s::get('cookies_hint_url');
        $message = sprintf(
            _(
                'We use cookies to offer you a better experience. By using this site you '
                .'agree to our use of cookies. <a target="_blank" href="%s"> Learn more &gt; </a>'
            ),
            $url
        );

        $html = '<link rel="stylesheet" type="text/css" href="/assets/css/cookies_overlay.css">'
                ."<script type='text/javascript'>$(function() {

                    if($.cookie('cookieoverlay_accepted') == null) {
                        $('#cookies_overlay').css('display', 'block');
                    }

                    $('#cookies_overlay').on('click', '.closeover', function(e, ui) {
                        $.cookie('cookieoverlay_accepted', 1, { expires: 365, path: '/' });
                        $(this).closest('#cookies_overlay').hide();
                    });

                });
                </script>"
                ."<div id='cookies_overlay' style='display:none'><div class='cookies-overlay'>
                  <p>$message</p>
                  <button data-dismiss='alert' class='closeover' type='button'>Aceptar</button>
                </div></div>";
    }

    return $html;
}
