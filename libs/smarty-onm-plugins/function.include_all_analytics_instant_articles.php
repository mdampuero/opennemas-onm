<?php

function smarty_function_include_all_analytics_instant_articles($params, &$smarty)
{
    $codes = [];
    // comScore
    $comScoreConfig = getService('setting_repository')->get('comscore');
    if (is_array($comScoreConfig)
        && array_key_exists('page_id', $comScoreConfig)
        && !empty(trim($comScoreConfig['page_id']))
    ) {
        $codes[] = '<!-- BegincomScore Tag -->'
            . '<script>'
            . 'var _comscore = _comscore || [];'
            . '_comscore.push({ c1: "2", c2: "'. $comScoreConfig['page_id'] .'" });'
            . '(function() {'
            . 'var s = document.createElement("script"), el = document.getElementsByTagName("script")[0]; s.async = true;'
            . 's.src = (document.location.protocol == "https:" ? "https://sb" :"http://b") + ".scorecardresearch.com/beacon.js";'
            . 'el.parentNode.insertBefore(s, el);'
            . '})();'
            . '</script>'
            . '<noscript>'
            . '<img src="http://b.scorecardresearch.com/p?c1=2&c2='. $comScoreConfig['page_id'] .'&cv=2.0&cj=1" />'
            . '</noscript>'
            . '<!-- EndcomScore  Tag -->'."\n";
    }
    // OJD
    $OJDconfig = getService('setting_repository')->get('ojd');
    if (is_array($OJDconfig)
        && array_key_exists('page_id', $OJDconfig)
        && !empty(trim($OJDconfig['page_id']))
    ) {
        $codes[] = '<!-- START Nielsen//NetRatings SiteCensus V5.3 -->'
            . '<!-- COPYRIGHT 2007 Nielsen//NetRatings -->'
            . '<script type="text/javascript">'
            . 'var _rsCI="'. $OJDconfig['page_id'] .'";'
            . 'var _rsCG="0";'
            . 'var _rsDN="//secure-uk.imrworldwide.com/";'
            . 'var _rsCC=0;'
            . '</script>'
            . '<script type="text/javascript" src="//secure-uk.imrworldwide.com/v53.js"></script>'
            . '<noscript>'
            . '<div><img src="//secure-uk.imrworldwide.com/cgi-bin/m?ci='
            . $OJDconfig['page_id'] .'&amp;cg=0" alt=""/></div>'
            . '</noscript>'
            . '<!-- END Nielsen//NetRatings SiteCensus V5.3 -->'."\n";
    }

    // Google Analytics
    $codes[] = getGoogleAnalyticsCode();
    // Piwik
    $codes[] = getPiwikCode();

    $output = '<figure class="op-tracker"><iframe>'.implode('<br>', $codes).'</iframe></figure>';

    return $output;
}
