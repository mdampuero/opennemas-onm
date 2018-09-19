<?php
/**
 * Assigns color the each category in menu
 *
 * @param array $params the list of parameters
 * @param \Smarty $smarty the smarty instance
 *
 * @return string
 */
function smarty_function_RenderColorMenu($params, &$smarty)
{
    $categories  = (isset($params['categories']) ? $params['categories'] : null);
    $current     = (isset($params['current']) ? $params['current'] : null);
    $configColor = getService('setting_repository')->get('site_color');
    $siteColor   = (isset($configColor) ? '#' . $configColor : '#dedede');

    // Styles to print each category's new
    $output = '';
    $actual = '';
    if (isset($categories) && !empty($categories)) {
        foreach ($categories as $theCategory) {
            if (empty($theCategory->color)) {
                $theCategory->color = $siteColor;
            } else {
                if (!preg_match('@^#@', $theCategory->color)) {
                    $theCategory->color = '#' . $theCategory->color;
                }
            }

            $output .=
                "div.onm-new .onm-new-category-name .{$theCategory->name} { color: {$theCategory->color}; }\n"
                . "div.onm-new div.{$theCategory->name} { color:{$theCategory->color}; }\n";

            if ($current == $theCategory->name) {
                $actual = $theCategory->color;
            }
        }

        if ($current == 'home') {
            $actual = $siteColor;
        }

        $output .=
            "div#header-menu nav.menu > ul, .transparent-logo { background-color: {$actual} !important;}\n" .
            "div.main-menu, div#footer-container  { background-color:{$actual} !important;}\n" .
            "h1#title a.big-text-logo  { color:{$actual} !important;}\n" .
            "article.onm-new .nw-title a:hover, div.widget .widget-header, " .
            " .frontpage article .article-info span { color:{$actual} !important;}\n" .
            "div.widget-last-articles .header-title { background-color:{$actual} !important;}\n" .
            "article.onm-new.highlighted-2-cols div.nw-subtitle div, " .
            "article.onm-new.highlighted-3-cols div.nw-subtitle div { background-color:{$actual} !important;}\n" .
            " .frontpage article.album .nw-subtitle, .frontpage article.video .nw-subtitle, " .
            " .frontpage article.opinion .nw-subtitle a { color:{$actual} !important;}\n" .
            "div.widget .title h5, div.widget .title h5 {color:{$actual} !important; }\n" .
            "div.widget-today-news .number {background-color:{$actual} !important; }\n";
    } elseif ($current == "mobile") {
        $output .=
            "#footerwrap { background-color:{$siteColor} !important;}" .
            "#navtabs li a { background-color:{$siteColor} !important;}" .
            "li.post .category, li.post:hover .title { color:{$siteColor} !important;}" .
            "#infoblock .subtitle strong { color:{$siteColor} !important;}";
    } else {
        $output .= "div.main-menu, div#footer-container  { background-color:{$siteColor} !important;}\n" .
            "h1#title a.big-text-logo  { color:{$siteColor} !important;}\n" .
            "article.onm-new .nw-title a:hover, div.widget .widget-header, " .
            " .frontpage article .article-info span { color:{$siteColor} !important;}\n" .
            "div.widget-last-articles .header-title { background-color:{$siteColor} !important;}\n" .
            " .frontpage article.album .nw-subtitle, .frontpage article.video .nw-subtitle, " .
            " .frontpage article.opinion .nw-subtitle a { color:{$siteColor} !important;}\n" .
            "article.article-inner .author-and-date .author { color:{$siteColor} !important;}\n" .
            // ."div.toolbar-bottom a, div.utilities a { background-color:{$siteColor} !important;}\n" .
            "div.widget-today-news .number {background-color: " . $siteColor . " !important; }\n" .
            "div .opinion-element div.more a, div .opinion-element .author_name a { color:{$siteColor} !important;}\n" .
            "div.opinion-inner header div.author-info a.opinion-author-name, " .
            "div.opinion-index-author header h1.section-title a { color:{$siteColor} !important;}\n" .
            "div.letter-inner span.author { color:{$siteColor} !important;}\n" .
            "div.list-of-videos article.interested-video div.info-interested-video " .
            "div.category a { color:{$siteColor} !important; }\n";
    }

    return $output;
}
