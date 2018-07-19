<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     outputfilter.comscore.php
 * Type:     outputfilter
 * Name:     canonical_url
 * Purpose:  Prints ComScore analytics code
 * -------------------------------------------------------------
 */
function smarty_outputfilter_comscore($output, $smarty)
{
    $request = $smarty->getContainer()->get('request_stack')->getCurrentRequest();

    if (is_null($request)) {
        return $output;
    }

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
    ) {
        $isAmp = preg_match('@\.amp\.html$@', $uri);
        if ($isAmp) {
            $code = addComScoreCode($output, 'amp');
        } else {
            $code = addComScoreCode($output);
        }

        return $code;
    }

    return $output;
}

function addComScoreCode($output, $type = null)
{
    $config = getService('setting_repository')->get('comscore');

    if (!is_array($config)
        || !array_key_exists('page_id', $config)
        || empty(trim($config['page_id']))
    ) {
        return $output;
    }

    if ($type === 'amp') {
        $code = '<!-- comScore UDM pageview tracking -->
            <amp-analytics type="comscore">
                <script type="application/json">
                {
                    "vars": {
                        "c2": "' . $config['page_id'] . '"
                    }
                }
                </script>
            </amp-analytics>
            <!-- End comScore -->';
    } else {
        $code = '<!-- BegincomScore Tag -->'
            . '<script>'
            . 'var _comscore = _comscore || [];'
            . '_comscore.push({ c1: "2", c2: "' . $config['page_id'] . '" });'
            . '(function() {'
            . 'var s = document.createElement("script"), '
            . 'el = document.getElementsByTagName("script")[0]; s.async = true;'
            . 's.src = (document.location.protocol == "https:" '
            . '? "https://sb" :"http://b") + ".scorecardresearch.com/beacon.js";'
            . 'el.parentNode.insertBefore(s, el);'
            . '})();'
            . '</script>'
            . '<noscript>'
            . '<img src="http://b.scorecardresearch.com/p?c1=2&c2=' . $config['page_id'] . '&cv=2.0&cj=1" />'
            . '</noscript>'
            . '<!-- EndcomScore  Tag -->';
    }

    return str_replace('</body>', $code . '</body>', $output);
}
