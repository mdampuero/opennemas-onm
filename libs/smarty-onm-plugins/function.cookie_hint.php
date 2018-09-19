<?php
/**
 * Renders the cookie hint
 *
 * @param array $params The list of parameters passed to the block.
 * @param \Smarty $smarty The instance of smarty.
 *
 * @return null|string
 */
function smarty_function_cookie_hint($params, &$smarty)
{
    if ($smarty->getContainer()->get('setting_repository')->get('cmp_script')) {
        return '';
    }

    $url = $smarty->getContainer()->get('setting_repository')->get('cookies_hint_url');

    $message = sprintf(
        _(
            'This website uses its own and third party cookies to elaborate '
            . 'statistical information and to be able to show you advertising '
            . 'related to your preferences through the analysis of your navigation.'
            . ' <a target="_blank" href="%s">See details &gt; </a>'
        ),
        $url
    );

    $html = "<div id='cookies_overlay' style='display: none;'>
            <div class='cookies-overlay'>
                <p>
                    <button class='closeover' onclick='acceptCookies()' type='button'>&times;</button>
                    $message
                </p>
            </div>
        </div>
        <script>
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

    return str_replace("\n", '', $html);
}
