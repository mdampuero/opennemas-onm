<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     outputfilter.google_tags_manager.php
 * Type:     outputfilter
 * Name:     google_tags_manager
 * Purpose:  Prints Google Tags Manager code
 * -------------------------------------------------------------
 */
function smarty_outputfilter_google_tags_manager($output, $smarty)
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
        && !preg_match('/\/fb\/instant-articles/', $uri)
        && !preg_match('@\.amp\.html$@', $uri)
    ) {
        $containerId = getService('setting_repository')->get('google_tags_id');

        if (empty(trim($containerId))) {
            return $output;
        }

        $headCode = getHeadGoogleTagsManagerCode(trim($containerId));
        $bodyCode = getBodyGoogleTagsManagerCode(trim($containerId));

        $output = preg_replace('@(</head>)@', $headCode.'${1}', $output);
        $output = preg_replace('@(<body.*>)@', '${1}'."\n".$bodyCode, $output);
    }

    return $output;
}


function getHeadGoogleTagsManagerCode($containerId)
{
    $code = "<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','" . $containerId . "');</script>
<!-- End Google Tag Manager -->";

    return $code;
}

function getBodyGoogleTagsManagerCode($containerId)
{
    $code = '<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=' . $containerId . '"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->';

    return $code;
}
