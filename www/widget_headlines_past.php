<?php
 
$now= date('Y-m-d H:m:s',time()); //2009-02-28 21:00:13
/*$ago24h= date('Y-m-d H:m:s',time()-24*60*60);
$ago3day= date('Y-m-d H:m:s',time()-24*60*60*3);
$ago1sem= date('Y-m-d H:m:s',time()-24*60*60*7);
*/
$articles_24h = $cm->find_by_category_name('Article', $category_data['name'], 'content_status=1 AND available=1 AND fk_content_type=1 AND created>='.$ago24h.' (starttime="0000-00-00 00:00:00" OR (starttime != "0000-00-00 00:00:00"  AND starttime<"'.$now.'")) AND (endtime="0000-00-00 00:00:00" OR (endtime != "0000-00-00 00:00:00"  AND endtime>"'.$now.'"))', 'ORDER BY ORDER BY views DESC, position ASC LIMIT 0 , 6');
$articles_3day = $cm->find_by_category_name('Article', $category_data['name'], 'content_status=1 AND available=1 AND fk_content_type=1 AND created>='.$ago3day.' (starttime="0000-00-00 00:00:00" OR (starttime != "0000-00-00 00:00:00"  AND starttime<"'.$now.'")) AND (endtime="0000-00-00 00:00:00" OR (endtime != "0000-00-00 00:00:00"  AND endtime>"'.$now.'"))', 'ORDER BY ORDER BY views DESC, position ASC LIMIT 0 , 6');
$articles_1sem = $cm->find_by_category_name('Article', $category_data['name'], 'content_status=1 AND available=1 AND fk_content_type=1 AND created>='.$ago1sem.' AND (starttime="0000-00-00 00:00:00" OR (starttime != "0000-00-00 00:00:00"  AND starttime<"'.$now.'")) AND (endtime="0000-00-00 00:00:00" OR (endtime != "0000-00-00 00:00:00"  AND endtime>"'.$now.'"))', 'ORDER BY ORDER BY views DESC, position ASC LIMIT 0 , 6');

//$articles_24h = $cm->getMostViewedContent('Article', true, $category_data['id'],'',1, 5);
$articles_24h = $cm->getAllMostViewed(true, $category_data['id'],1,5);
//$articles_3day = $cm->getMostViewedContent('Article', true, $category_data['id'],'',3, 5);
$articles_3day = $cm->getAllMostViewed(true, $category_data['id'],3,5);
//$articles_1sem = $cm->getMostViewedContent('Article', true, $category_data['id'],'',7, 5);
$articles_1sem = $cm->getAllMostViewed(true, $category_data['id'],7,5);
$tpl->assign('articles_24h', $articles_24h);
$tpl->assign('articles_3day', $articles_3day);
$tpl->assign('articles_1sem', $articles_1sem);

