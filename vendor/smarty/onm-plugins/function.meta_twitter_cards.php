<?php
/*
 * -------------------------------------------------------------
 * File:        function.meta_twitter_cards.php
 */
use \Onm\Settings as s;

function smarty_function_meta_twitter_cards($params, &$smarty)
{
    $output = array();

    // only return if th page where is printed
    // this twitter card is a content page
    if (array_key_exists('content', $smarty->tpl_vars)) {
        // Check if the twitter user is not empty
        $user = preg_split('@.com/[#!/]*@', s::get('twitter_page'));
        $twitterUser = $user[1];

        if (empty($twitterUser)) {
            return '';
        }

        $content = $smarty->tpl_vars['content']->value;

        // Preparing content data for the twitter card
        $summary = $content->summary;
        if (empty($summary)) {
            $summary = mb_substr($content->body, 0, 80)."...";
        }
        $summary = trim(html_attribute($summary));
        $url = "http://".SITE.'/'.$content->uri;

        // Writing Twitter card info
        $output []= '<meta name="twitter:card"        content="summary">';
        $output []= '<meta name="twitter:title"       content="'.$content->title.'">';
        $output []= '<meta name="twitter:description" content="'.$summary.'">';
        $output []= '<meta name="twitter:site"        content="@'.$twitterUser.'">';
        $output []= '<meta name="twitter:domain"      content="'.$url.'">';

        if (array_key_exists('photoInt', $smarty->tpl_vars)) {
            $photoInt = $smarty->tpl_vars['photoInt']->value;
            $imageUrl = MEDIA_IMG_ABSOLUTE_URL.'/'.$photoInt->path_file.'/'.$photoInt->name;
            $output []= '<meta name="twitter:image:src" content="'.$imageUrl.'">';
        }
    }

    return implode("\n", $output);
}
