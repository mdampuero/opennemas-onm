<?php
$now = date('Y-m-d H:m:s', time()); //2009-02-28 21:00:13
if (!isset($actual_category_id) || empty($actual_category_id)) {
    $actual_category_id = 0;
}
// Search las 24h, 3days, 1week available articles.
$articles_24h = $cm->getAllMostViewed(true, $actual_category_id, 1, 5);
$articles_3day = $cm->getAllMostViewed(true, $actual_category_id, 3, 5);
$articles_1sem = $cm->getAllMostViewed(true, $actual_category_id, 7, 5);
$tpl->assign('articles_24h', $articles_24h);
$tpl->assign('articles_3day', $articles_3day);
$tpl->assign('articles_1sem', $articles_1sem);
