<?php
 
 
//$articles_views = $cm->find_by_category_name('Article', $category_data['name'], 'content_status=1 AND available=1 AND fk_content_type=1  (starttime="0000-00-00 00:00:00" OR (starttime != "0000-00-00 00:00:00"  AND starttime<"'.$now.'")) AND (endtime="0000-00-00 00:00:00" OR (endtime != "0000-00-00 00:00:00"  AND endtime>"'.$now.'"))', 'ORDER BY ORDER BY views DESC, position ASC LIMIT 0 , 6');
$articles_viewed = $cm->cache->getMostViewedContent('Article', true, $category_data['id']);
$articles_comments = $cm->cache->getMostComentedContent('Article', true, $category_data['id']);

$tpl->assign('articles_views', $articles_views);
$tpl->assign('articles_comments', $articles_comments);


