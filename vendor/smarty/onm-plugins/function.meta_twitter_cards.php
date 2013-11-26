<?php
/*
 * -------------------------------------------------------------
 * File:        function.meta_twitter_cards.php
 */
use \Onm\Settings as s;

function smarty_function_meta_twitter_cards($params, &$smarty) {

    $output = array();

    // Only return anything if the Ganalytics is setted in the configuration
    if (array_key_exists('content', $smarty->tpl_vars)) {
        $output []= '<meta name="twitter:card" content="summary">';

        $content = $smarty->tpl_vars['content']->value;

        $output []= '<meta name="twitter:title" content="'.$content->title.'">';
        $output []= '<meta name="twitter:description" content="'.trim(html_attribute($content->summary)).'">';

        $user = preg_split('@.com/[#!/]*@', s::get('twitter_page'));
        $twitterUser = $user[1];
        if (!empty($twitterUser)) {
            $output []= '<meta name="twitter:site" content="@'.$twitterUser.'">';
        }

        if (array_key_exists('photoInt', $smarty->tpl_vars)) {
            $photoInt = $smarty->tpl_vars['photoInt']->value;
            $imageUrl = MEDIA_IMG_ABSOLUTE_URL.'/'.$photoInt->path_file.'/'.$photoInt->name;
            $output []= '<meta name="twitter:image:src" content="'.$imageUrl.'">';
        }

        $output []= '<meta name="twitter:domain" content="'."http://".SITE.$content->uri.'">';
    }

    return implode("\n", $output);
}
