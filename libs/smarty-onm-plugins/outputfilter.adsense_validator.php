<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     outputfilter.adsense_validator.php
 * Type:     outputfilter
 * Name:     adsense_validator
 * Purpose:  Prints adsense validation script
 * -------------------------------------------------------------
 */
function smarty_outputfilter_adsense_validator($output, $smarty)
{
    $uri = $smarty->getContainer()->get('request_stack')->getCurrentRequest()->getUri();

    if (!preg_match('/\/manager/', $uri)
        && !preg_match('/\/managerws/', $uri)
        && !preg_match('/\/share-by-email/', $uri)
        && !preg_match('/\/sharrre/', $uri)
        && !preg_match('/\/ads/', $uri)
        && !preg_match('/\/comments/', $uri)
        && !preg_match('/\/fb\/instant-articles/', $uri)
        && !preg_match('@\.amp\.html$@', $uri)
    ) {
        $adsenseId = $smarty->getContainer()->get('setting_repository')->get('adsense_id');

        if (!empty($adsenseId)) {
            $code = '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<script>
(adsbygoogle = window.adsbygoogle || []).push({
google_ad_client: "' . $adsenseId . '",
enable_page_level_ads: true
});
</script>';

            $output = preg_replace('@(</head>)@', "\n" . $code . '${1}', $output);
        }
    }

    return $output;
}
