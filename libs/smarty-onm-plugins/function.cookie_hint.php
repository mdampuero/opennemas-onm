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
                'We use cookies to personalise content and ads, to provide social media '
                .'features and to analyse our traffic. We also share information about '
                .'your use of our site with our social media, advertising and analytics '
                .'partners who may combine it with other information you’ve provided '
                .'to them or they’ve collected from your use of their services. '
                .'<a target="_blank" href="%s"> See details &gt; </a>'
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
