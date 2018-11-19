<?php
/**
 * Prints adsense validation script
 *
 * @param array $params the list of parameters
 * @param \Smarty $smarty the smarty instance
 *
 * @return string
 */
function smarty_outputfilter_adsense_validator($output, $smarty)
{
    $request = $smarty->getContainer()
        ->get('request_stack')
        ->getCurrentRequest();

    if (is_null($request)) {
        return $output;
    }

    $uri = $request->getUri();

    if (!preg_match('/\/manager/', $uri)
        && !preg_match('/\/managerws/', $uri)
        && !preg_match('/\/share-by-email/', $uri)
        && !preg_match('/\/sharrre/', $uri)
        && !preg_match('/\/ads/', $uri)
        && !preg_match('/\/comments/', $uri)
        && !preg_match('/\/fb\/instant-articles/', $uri)
        && !preg_match('@\.amp\.html$@', $uri)
    ) {
        $adsenseId = $smarty->getContainer()->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('adsense_id');

        // Check for activated module
        if (!$smarty->getContainer()->get('core.security')->hasExtension('ADS_MANAGER')) {
            $adsenseId = 'ca-pub-7694073983816204';
        }

        if (empty($adsenseId)) {
            return $output;
        }

        $code = '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<script>
(adsbygoogle = window.adsbygoogle || []).push({
google_ad_client: "' . $adsenseId . '",
enable_page_level_ads: true
});
</script>';

        $output = preg_replace('@(</head>)@', "\n" . $code . '${1}', $output);
    }

    return $output;
}
