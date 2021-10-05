<?php
/**
 * Renders the browser-update.org script.
 *
 * @param string  $output The current output.
 * @param \Smarty $smarty The Template object.
 *
 * @return string The current output after applying the filter.
 */
function smarty_outputfilter_browser_update($output, $smarty)
{
    if ($smarty->getContainer()->getParameter('kernel.environment') === 'dev') {
        return $output;
    };

    $request = $smarty->getContainer()->get('request_stack')->getCurrentRequest();

    if (empty($request)) {
        return $output;
    }

    $uri     = $request->getUri();
    $referer = $request->headers->get('referer');

    if (preg_match('/newsletter/', $smarty->source->resource)
        || preg_match('/\/admin\/frontpages/', $referer)
        || preg_match('/\/managerws/', $uri)
        || preg_match('/\/sharrre/', $uri)
        || preg_match('/\/ads\//', $uri)
        || preg_match('/\/comments\//', $uri)
        || preg_match('/\/rss/', $uri)
        || preg_match('@\.amp\.html@', $uri)
    ) {
        return $output;
    }

    if (!preg_match('/\/admin/', $uri) && !preg_match('/\/manager/', $uri)) {
        $ds = $smarty->getContainer()
            ->get('orm.manager')
            ->getDataSet('Settings', 'instance');

        if (empty($ds->get('browser_update'))) {
            return $output;
        }
    }

    $code = "\n<script>
var \$buoop = {vs:{i:9,f:3.5,o:10.6,s:4,n:9}};

\$buoop.ol = window.onload;
window.onload=function(){
 try {if (\$buoop.ol) \$buoop.ol();}catch (e) {};

 var e = document.createElement('script');
 e.setAttribute('src', 'https://browser-update.org/update.js');
 document.body.appendChild(e);
}
</script>";

    return str_replace('</body>', $code . '</body>', $output);
}
