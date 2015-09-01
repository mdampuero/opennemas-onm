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

        $html = "<div id='cookies_overlay' style='display: none;'>
                <div class='cookies-overlay'>
                    <p>$message</p>
                    <button class='closeover' onclick='acceptCookies()' type='button'>Aceptar</button>
                </div>
            </div>
            <script type='text/javascript'>
                function getCookie(name) {
                    var cookies = document.cookie.split(';');
                    for (var i = 0; i < cookies.length; i++) {
                        var cookie = cookies[i].replace(/^\s+/,'').replace(/\s+$/,'');
                        if (cookie.indexOf(name) == 0) {
                            return cookie.substring(name.length + 1, cookie.length);
                        }
                    }
                }

                function acceptCookies() {
                    var date = new Date();
                    date.setTime(date.getTime() + 365*24*60*60*1000);
                    document.cookie = 'cookie_overlay_accepted=1; expires=' +
                        date.toGMTString() + ' ;path=/';
                    var overlay = document.getElementById('cookies_overlay');
                    overlay.parentElement.removeChild(overlay);
                }

                (function() {
                    if (getCookie('cookie_overlay_accepted') != 1) {
                        document.getElementById('cookies_overlay').style.display = 'block';
                    }
                })();
            </script>";
    }

    return str_replace("\n", '', $html);
}
