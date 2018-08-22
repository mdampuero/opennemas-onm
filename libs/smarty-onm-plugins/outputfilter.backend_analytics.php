<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     outputfilter.backend_analytics.php
 * Type:     outputfilter
 * Name:     backend_analytics
 * Purpose:  Prints Piwik and Google Analytics codes
 * -------------------------------------------------------------
 */
function smarty_outputfilter_backend_analytics($output, $smarty)
{
    $request = getService('request');
    $uri     = $request->getUri();

    if (preg_match('/\/admin/', $uri)
        && getService('service_container')->getParameter('backend_analytics.enabled')
    ) {
        return addBackendCodes($output);
    }

    return $output;
}

function addBackendCodes($output)
{
    $code = '<!-- Piwik -->'
        . '<script>'
        . 'var _paq = _paq || [];'
        . '_paq.push(["setDocumentTitle", document.domain + "/" + document.title]);'
        . '_paq.push(["setCookieDomain", "*.opennemas.com"]);'
        . '_paq.push([\'trackPageView\']);'
        . '_paq.push([\'enableLinkTracking\']);'
        . '(function() {'
        . 'var u="//piwik.openhost.es/";'
        . '_paq.push([\'setTrackerUrl\', u+\'piwik.php\']);'
        . '_paq.push([\'setSiteId\', 139]);'
        . 'var d=document, g=d.createElement(\'script\'), s=d.getElementsByTagName(\'script\')[0];'
        . 'g.async=true; g.defer=true; g.src=u+\'piwik.js\'; s.parentNode.insertBefore(g,s);'
        . '})();'
        . '</script>'
        . '<noscript><p><img src="//piwik.openhost.es/piwik.php?idsite=139" style="border:0;" alt="" /></p></noscript>'
        . '<!-- End Piwik Code -->'
        . '<!-- Google Analytics -->'
        . '<script>'
        . '(function(i,s,o,g,r,a,m){i[\'GoogleAnalyticsObject\']=r;i[r]=i[r]||function(){'
        . '(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),'
        . 'm=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)'
        . '})(window,document,\'script\',\'//www.google-analytics.com/analytics.js\',\'ga\');'
        . 'ga(\'create\', \'UA-40838799-4\', { cookieDomain: \''
        . getService('request')->server->get('SERVER_NAME') . '\' });'
        . 'ga(\'send\', \'pageview\');'
        . '</script>'
        . '<!-- End Google Analytics -->';

    return str_replace('</head>', $code . '</head>', $output);
}
