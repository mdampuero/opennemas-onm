<?php
/**
 * Prints Google Analytics codes
 *
 * @param array $params the list of parameters
 * @param \Smarty $smarty the smarty instance
 *
 * @return string
 */
function smarty_outputfilter_backend_analytics($output, $smarty)
{
    $request = $smarty->getContainer()->get('request_stack')->getCurrentRequest();

    if (empty($request)) {
        return $output;
    }

    $uri = $request->getUri();

    if (!preg_match('/newsletter/', $smarty->source->resource)
        && preg_match('/\/admin/', $uri)
        && $smarty->getContainer()->getParameter('backend_analytics.enabled')
    ) {
        $code = '<!-- Google Analytics -->'
        . '<script>'
        . '(function(i,s,o,g,r,a,m){i[\'GoogleAnalyticsObject\']=r;i[r]=i[r]||function(){'
        . '(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),'
        . 'm=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)'
        . '})(window,document,\'script\',\'//www.google-analytics.com/analytics.js\',\'ga\');'
        . 'ga(\'create\', \'UA-40838799-4\', { cookieDomain: \''
        . $request->server->get('SERVER_NAME') . '\' });'
        . 'ga(\'send\', \'pageview\');'
        . '</script>'
        . '<!-- End Google Analytics -->';

        return str_replace('</head>', $code . '</head>', $output);
    }

    return $output;
}
