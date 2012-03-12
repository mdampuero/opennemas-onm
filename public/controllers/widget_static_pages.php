<?php
// Fetching the last 6 available static pages
$statics = $cm->find('StaticPage', 'contents.available=1', 'ORDER BY created ASC LIMIT 10');
$tpl->assign('statics', $statics);

