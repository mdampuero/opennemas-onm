<?php
$articles_viewed = $cm->cache->getMostViewedContent('Article', true, $actual_category_id, 0, 7, 6);
$articles_comments = $cm->cache->getMostComentedContent('Article', true, $actual_category_id, 0, 7, 6);
$articles_voted = $cm->cache->getMostVotedContent('Article', true, $actual_category_id, 0, 7, 6);
$arts_commented = array();
if (!empty($articles_comments) && count($articles_comments) > 0) {
    foreach ($articles_comments as $arts) {
        $this_article = new Article($arts['pk_content']);
        $arts_commented[] = $this_article;
    }
}
$tpl->assign('articles_viewed', $articles_viewed);
$tpl->assign('articles_voted', $articles_voted);
$tpl->assign('articles_comments', $arts_commented);
