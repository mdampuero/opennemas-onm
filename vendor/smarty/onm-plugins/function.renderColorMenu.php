<?php
/*
 * -------------------------------------------------------------
 * File:     	function.render_color_menu.php
 * Assigns color the each category in menu
 */
function smarty_function_RenderColorMenu($params, &$smarty)
{
    $categories = (isset($params['categories']) ? $params['categories'] : null);
    $current = (isset($params['current']) ? $params['current'] : null);
    $configColor = Onm\Settings::get('site_color');
    $siteColor = (isset($configColor) ? '#'.$configColor : '#dedede');

    // Styles to print each category's new
    $output = '';
    $actual = '';
    if (isset($categories) && !empty($categories)) {
        foreach ($categories as $theCategory) {

            if (empty($theCategory->color)) {

                $theCategory->color = $siteColor;
            } else {
                if (!preg_match('@^#@', $theCategory->color)) {
                    $theCategory->color = '#'.$theCategory->color;

                }
            }

            $output.= "\tdiv.onm-new .onm-new-category-name ". $theCategory->name .
                      " { color:" . $theCategory->color . "; }\n\t\t";

            $output.= "\tdiv.onm-new div.". $theCategory->name .
                      " { color:" . $theCategory->color . "; }\n\t\t";


            if ($current == $theCategory->name) {
                $actual = $theCategory->color;
            }
        }//end-foreach

        if ($current == 'home') {
            $actual = $siteColor;
        }

        $output.= "\tdiv#header-menu nav.menu > ul, " .
                  " .transparent-logo { background-color:" . $actual . " !important;}\n";

        $output.= "\tdiv.main-menu, div#footer-container  { background-color:" . $actual . " !important;}\n";

        $output.= "\th1#title a.big-text-logo  { color:" . $actual . " !important;}\n";
        $output.= "\tarticle.onm-new .nw-title a:hover, div.widget .widget-header, ".
            ".frontpage article .article-info span { color:" . $actual . " !important;}\n";


        $output.= "\tdiv.widget-last-articles .header-title { background-color:" . $actual . " !important;}\n";
        $output.= "\tarticle.onm-new.highlighted-2-cols div.nw-subtitle div, ".
            "article.onm-new.highlighted-3-cols div.nw-subtitle div { background-color:" . $actual . " !important;}\n";

        $output.= "\t.frontpage article.album .nw-subtitle, .frontpage article.video .nw-subtitle, ".
            ".frontpage article.opinion .nw-subtitle a { color:" . $actual . " !important;}\n";


        $output.= "\tdiv.widget .title h5, div.widget .title h5 {color: ". $actual. " !important; }\n";
        $output.= "\tdiv.widget-today-news .number {background-color: ". $actual. " !important; }\n";

    } elseif ($current == "mobile") {
        $output.= "\t#footerwrap { background-color: ".$siteColor." !important;}";
        $output.= "\t#navtabs li a { background-color: ".$siteColor." !important;}";

        $output.= "\tli.post .category, li.post:hover .title { color: ".$siteColor." !important;}";

        $output.= "\t#infoblock .subtitle strong { color: ".$siteColor." !important;}";

    } else {

        $output.= "\tdiv.main-menu, div#footer-container  { background-color:" . $siteColor . " !important;}\n";

        $output.= "\th1#title a.big-text-logo  { color:" . $siteColor . " !important;}\n";
        $output.= "\tarticle.onm-new .nw-title a:hover, div.widget .widget-header, ".
            ".frontpage article .article-info span { color:" . $siteColor . " !important;}\n";

        $output.= "\tdiv.widget-last-articles .header-title { background-color:" . $siteColor . " !important;}\n";

        $output.= "\t.frontpage article.album .nw-subtitle, .frontpage article.video .nw-subtitle, ".
            ".frontpage article.opinion .nw-subtitle a { color:" . $siteColor . " !important;}\n";

        $output.= "\tarticle.article-inner .author-and-date .author { color:" . $siteColor . " !important;}\n";

     /*   $output.= "\tdiv.toolbar-bottom a, div.utilities a { background-color:" . $siteColor . " !important;}\n";*/
        $output.= "\tdiv.widget-today-news .number {background-color: ". $siteColor. " !important; }\n";

        $output.= "\tdiv .opinion-element div.more a, ".
            "div .opinion-element .author_name a { color:" . $siteColor . " !important;}\n";

        $output.= "\tdiv.opinion-inner header div.author-info a.opinion-author-name, ".
            "div.opinion-index-author header h1.section-title a { color:" . $siteColor . " !important;}\n";


        $output.= "\tdiv.letter-inner span.author{ color:" . $siteColor . " !important;}\n";

        $output.= "\tdiv.list-of-videos article.interested-video div.info-interested-video ".
            "div.category a{ color:" . $siteColor . " !important;}\n";
    }

    return $output;
}

