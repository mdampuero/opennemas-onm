<?php
/**
 * Handles the needed js for Web Push notifications.
 *
 * @param string $output
 * @param \Smarty $smarty
 *
 * @return string
 */
function smarty_outputfilter_webpush_script($output, $smarty)
{
    $webpushService = $smarty->getContainer()->get('orm.manager')
        ->getDataSet('Settings', 'instance')
        ->get('webpush_service');
    $webpushKeys = $smarty->getContainer()->get('orm.manager')
        ->getDataSet('Settings', 'instance')
        ->get(['webpush_apikey', 'webpush_token', 'webpush_publickey']);

    if ($smarty->getContainer()->get('core.security')->hasExtension('es.openhost.module.webpush_notifications')
        && $webpushService == 'webpushr'
        && $webpushKeys
        && !$smarty->getContainer()->get('core.instance')->hasMultilanguage()) {
            $script = "<script>(function(w,d, s, id) {if(typeof(w.webpushr)!=='undefined') "
                . "return;w.webpushr=w.webpushr||function(){(w.webpushr.q=w.webpushr.q||[]).push(arguments)};var js, "
                . "fjs = d.getElementsByTagName(s)[0];js = d.createElement(s); js.id = id;js.async=1;js.src = "
                . "\"https://cdn.webpushr.com/app.min.js\";"
                . "fjs.parentNode.appendChild(js);}(window,document, 'script', 'webpushr-jssdk'));"
                . "webpushr('setup',{'key':'" . $webpushKeys['webpush_publickey'] . "', 'integration':'popup'  });"
                . "</script>";

            $output = str_replace('</body>', $script . '</body>', $output);
    }

    return $output;
}
