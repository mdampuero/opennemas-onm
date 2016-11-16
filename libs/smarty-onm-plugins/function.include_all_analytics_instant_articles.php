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
    // Chartbeat
    $ChartBeatconfig = getService('setting_repository')->get('chartbeat');
    if (is_array($ChartBeatconfig)
        && array_key_exists('id', $ChartBeatconfig)
        && array_key_exists('domain', $ChartBeatconfig)
        && !empty(trim($ChartBeatconfig['id']))
        && !empty(trim($ChartBeatconfig['domain']))
    ) {
        // Get author if exists otherwise get agency
        $author = $category = '';
        if (array_key_exists('item', $smarty->tpl_vars)) {
            $content = $smarty->tpl_vars['item']->value;
            $user = getService('user_repository')->find($content->fk_author);
            $author = (!is_null($user->name)) ? $user->name : $content->agency;
            if (empty($author)) {
                $author = getService('setting_repository')->get('site_name');
            }
            $category = $content->category_name;
            $title = $content->title;
        }

        $codes[] = '<script type="text/javascript">'
            . 'var _sf_async_config = {};'
            . '_sf_async_config.uid = '.$ChartBeatconfig['id'].';'
            . '_sf_async_config.domain = '.$ChartBeatconfig['domain'].';'
            . '_sf_async_config.title = "'.$title.'";'
            . '_sf_async_config.sections = "'.$category.'";'
            . '_sf_async_config.authors = "'.$author.'";'
            . '_sf_async_config.useCanonical = true;'
            . 'window._sf_endpt = (new Date()).getTime();'
        . '</script>'
        . '<script defer src="//static.chartbeat.com/js/chartbeat_fia.js"></script>'."\n";
    }

    // Google Analytics
    $codes[] = getGoogleAnalyticsCode();
    // Piwik
    $codes[] = getPiwikCode();

    $output = '';
    if (!empty($codes)) {
        $output = '<figure class="op-tracker"><iframe>'.implode('<br>', $codes).'</iframe></figure>';
    }

    return $output;
}
