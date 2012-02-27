<?php
// Fetching the last 6 available static pages
$statics = $cm->find('Static_Page', 'contents.available=1', 'ORDER BY created ASC LIMIT 10');
$tpl->assign('statics', $statics);

