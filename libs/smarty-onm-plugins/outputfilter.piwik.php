<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     outputfilter.piwik.php
 * Type:     outputfilter
 * Name:     canonical_url
 * Purpose:  Prints Piwik code
 * -------------------------------------------------------------
 */
function smarty_outputfilter_piwik($output, &$smarty)
{
    $request = getService('request');
    $uri     = $request->getUri();
    $referer = $request->headers->get('referer');

    if (!preg_match('/\/admin\/frontpages/', $referer)
        && !preg_match('/\/manager/', $uri)
        && !preg_match('/\/managerws/', $uri)
        && !preg_match('/\/share-by-email/', $uri)
        && !preg_match('/\/sharrre/', $uri)
        && !preg_match('/\/ads/', $uri)
        && !preg_match('/\/comments/', $uri)
    ) {
        return addPiwikCode($output);
    }

    return $output;
}

function addPiwikCode($output)
{
    $config = getService('setting_repository')->get('piwik');

    if (!is_array($config)
        || !array_key_exists('page_id', $config)
        || !array_key_exists('server_url', $config)
        || empty(trim($config['page_id']))
    ) {
        return $output;
    }

    $httpsHost = preg_replace("/http:/", "https:", $config['server_url']);

    $code = '<!-- Piwik -->
        <script type="text/javascript">
        var _paq = _paq || [];
        _paq.push([\'trackPageView\']);
        _paq.push([\'enableLinkTracking\']);
        (function() {
            var u = (("https:" == document.location.protocol) ? "'.
            $httpsHost . '" : "' . $config['server_url'] .'");
            _paq.push([\'setTrackerUrl\', u+\'piwik.php\']);
            _paq.push([\'setSiteId\', ' . $config['page_id'].']);
            var d=document, g=d.createElement(\'script\'), s=d.getElementsByTagName(\'script\')[0];
            g.type=\'text/javascript\';
            g.async=true; g.defer=true;
            g.src=u+\'piwik.js\'; s.parentNode.insertBefore(g,s);
        })();
        </script>
        <noscript>
            <img src="'. $config['server_url'] .'piwik.php?idsite='.
            $config['page_id'] .'" style="border:0" alt="" />
        </noscript>
        <!-- End Piwik Tracking Code -->';

    return str_replace('</head>', $code . '</head>', $output);
}
