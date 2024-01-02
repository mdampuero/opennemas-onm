<?php
/**
 * Handles the needed js for Web Push notifications.
 *
 * @param string $output
 * @param \Smarty $smarty
 *
 * @return string
 */
function smarty_outputfilter_webpush_notifications_script($output, $smarty)
{
    $request = $smarty->getContainer()->get('request_stack')->getCurrentRequest();

    if (is_null($request)) {
        return $output;
    }

    $uri = $request->getUri();

    if (!preg_match('/newsletter/', $smarty->source->resource)
        && !preg_match('/\/manager/', $uri)
        && !preg_match('/\/managerws/', $uri)
        && !preg_match('/\/sharrre/', $uri)
        && !preg_match('/\/ads\//', $uri)
        && !preg_match('/\/comments\//', $uri)
        && !preg_match('/\/hbbtv/', $uri)
        && !preg_match('/\/rss\/(?!listado$)/', $uri)
        && !preg_match('@\.amp\.html@', $uri)
    ) {
        // Get webpushr configuration
        $webpushSettings = $smarty->getContainer()->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get(['webpush_service', 'webpush_apikey', 'webpush_token', 'webpush_publickey']);

        if ($smarty->getContainer()->get('core.security')->hasExtension('es.openhost.module.webpush_notifications')
            && $webpushSettings
            && $webpushSettings['webpush_service'] == 'webpushr'
            && $webpushSettings['webpush_publickey']
            && !$smarty->getContainer()->get('core.instance')->hasMultilanguage()
        ) {
            $script = "<script>(function(w,d, s, id) {if(typeof(w.webpushr)!=='undefined') "
                . "return;w.webpushr=w.webpushr||function(){(w.webpushr.q=w.webpushr.q||[]).push(arguments)};var js, "
                . "fjs = d.getElementsByTagName(s)[0];js = d.createElement(s); js.id = id;js.async=1;js.src = "
                . "\"https://cdn.webpushr.com/app.min.js\";"
                . "fjs.parentNode.appendChild(js);}(window,document, 'script', 'webpushr-jssdk'));"
                . "webpushr('setup',{'key':'" . $webpushSettings['webpush_publickey'] . "' });"
                . "</script>";

            $output = preg_replace(
                '@(<body.*>)@',
                '${1}' . "\n" . $script . "\n",
                $output
            );
        }
    }

    return $output;
}
