<?php

function smarty_function_include_all_analytics_instant_articles($params, &$smarty)
{
    $output   = '';
    $codes    = [];
    $settings = $smarty->getContainer()
        ->get('orm.manager')
        ->getDataSet('Settings', 'instance')
        ->get([ 'chartbeat', 'comscore', 'google_analytics', 'ojd', 'site_name' ]);

    // comScore
    if (is_array($settings['comscore'])
        && array_key_exists('page_id', $settings['comscore'])
        && !empty(trim($settings['comscore']['page_id']))
    ) {
        $codes[] = '<!-- BegincomScore Tag -->'
            . '<script>'
            . 'var _comscore = _comscore || [];'
            . '_comscore.push({ c1: "2", c2: "' . $settings['comscore']['page_id'] . '",
                options: {
                    url_append: "comscorekw=fbia"
                }});'
            . '(function() {'
            . 'var s = document.createElement("script"), '
            . 'el = document.getElementsByTagName("script")[0]; s.async = true;'
            . 's.src = (document.location.protocol == "https:" ? "https://sb" :"http://b") '
            . '+ ".scorecardresearch.com/beacon.js";'
            . 'el.parentNode.insertBefore(s, el);'
            . '})();'
            . '</script>'
            . '<noscript>'
            . '<img src="http://b.scorecardresearch.com/p?c1=2&c2=' . $settings['comscore']['page_id']
            . '&cv=2.0&cj=1&comscorekw=fbia" />'
            . '</noscript>'
            . '<!-- EndcomScore  Tag -->' . "\n";
    }

    // OJD
    if (is_array($settings['ojd'])
        && array_key_exists('page_id', $settings['ojd'])
        && !empty(trim($settings['ojd']['page_id']))
    ) {
        $codes[] = '<!-- START Nielsen//NetRatings SiteCensus V5.3 -->'
            . '<!-- COPYRIGHT 2007 Nielsen//NetRatings -->'
            . '<script>'
            . 'var _rsCI="' . $settings['ojd']['page_id'] . '";'
            . 'var _rsCG="0";'
            . 'var _rsDN="//secure-uk.imrworldwide.com/";'
            . 'var _rsCC=0;'
            . '</script>'
            . '<script src="//secure-uk.imrworldwide.com/v53.js"></script>'
            . '<noscript>'
            . '<div><img src="//secure-uk.imrworldwide.com/cgi-bin/m?ci='
            . $settings['ojd']['page_id'] . '&amp;cg=0" alt=""/></div>'
            . '</noscript>'
            . '<!-- END Nielsen//NetRatings SiteCensus V5.3 -->' . "\n";
    }

    // Chartbeat
    if (is_array($settings['chartbeat'])
        && array_key_exists('id', $settings['chartbeat'])
        && array_key_exists('domain', $settings['chartbeat'])
        && !empty(trim($settings['chartbeat']['id']))
        && !empty(trim($settings['chartbeat']['domain']))
    ) {
        // Get author if exists otherwise get agency
        $author = $category = '';
        if (array_key_exists('item', $smarty->tpl_vars)) {
            $content = $smarty->tpl_vars['item']->value;

            if (!empty($content->fk_author)) {
                $user   = $smarty->getContainer()->get('orm.manager')
                    ->getRepository('User', 'instance')
                    ->find($content->fk_author);
                $author = (!is_null($user->name)) ? $user->name : $content->agency;
            }

            if (empty($author)) {
                $author = $settings['site_name'];
            }

            $category = $content->category_name;
            $title    = $content->title;
        }

        $codes[] = '<script>'
            . 'var _sf_async_config = {};'
            . '_sf_async_config.uid = ' . $settings['chartbeat']['id'] . ';'
            . '_sf_async_config.domain = ' . $settings['chartbeat']['domain'] . ';'
            . '_sf_async_config.title = "' . $title . '";'
            . '_sf_async_config.sections = "' . $category . '";'
            . '_sf_async_config.authors = "' . $author . '";'
            . '_sf_async_config.useCanonical = true;'
            . 'window._sf_endpt = (new Date())./Time();'
        . '</script>'
        . '<script defer src="//static.chartbeat.com/js/chartbeat_fia.js"></script>' . "\n";
    }

    // Google Analytics
    $codes[] = generateFiaGAScriptCode(
        $settings['google_analytics'],
        $smarty->tpl_vars['item']->value
    );

    // Piwik
    $codes[] = getPiwikCode();


    if (!empty($codes)) {
        $output = '<figure class="op-tracker"><iframe>'
            . implode('<br>', $codes)
            . '</iframe></figure>';
    }

    return $output;
}

function generateFiaGAScriptCode($config, $content)
{
    $code = "\n<script>\n(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;"
        . "i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*"
        . "new Date();a=s.createElement(o), m=s.getElementsByTagName(o)[0];"
        . "a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,"
        . "'script','https://www.google-analytics.com/analytics.js','ga');\n";

    foreach ($config as $key => $account) {
        if (is_array($account)
            && array_key_exists('api_key', $account)
            && !empty(trim($account['api_key']))
        ) {
            if (array_key_exists('base_domain', $account)
                && !empty(trim($account['base_domain']))
            ) {
                $code .= "ga('create', '" . trim($account['api_key']) . "', '"
                    . trim($account['base_domain']) . "', 'account{$key}');\n";
            } else {
                $code .= "ga('create', '" . trim($account['api_key']) . "', 'auto', 'account{$key}');\n";
            }
            $code .= "ga('account{$key}.require', 'displayfeatures');\n";
            $code .= "ga('account{$key}.set', 'campaignSource', 'Facebook');\n";
            $code .= "ga('account{$key}.set', 'campaignMedium', 'Social Instant Article');\n";
            $code .= "ga('account{$key}.send', 'pageview', {title: '{$content->title}'});\n";
        }
    }

    // Add opennemas Account
    $code .= "ga('create', 'UA-40838799-5', 'opennemas.com','onm');\n";
    $code .= "ga('onm.require', 'displayfeatures');\n";
    $code .= "ga('onm.set', 'campaignSource', 'Facebook');\n";
    $code .= "ga('onm.set', 'campaignMedium', 'Social Instant Article');\n";
    $code .= "ga('onm.send', 'pageview', {title: '{$content->title}'});\n";
    $code .= "</script>\n";

    return $code;
}
