<?php
// Fetching the last 6 available static pages
$statics = $cm->find('Static_Page', 'contents.available=1', 'ORDER BY created ASC LIMIT 0 , 6');
$tpl->assign('statics', $statics);
 