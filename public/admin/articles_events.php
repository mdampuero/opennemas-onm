<?php
// Application events
if (preg_match('@articles_events.php@', $_SERVER['PHP_SELF'])) {
	die();
}

/*
 clearCacheContent, callback registered into Content
*/
$GLOBALS['application']->register('onAfterUpdate', 'onUpdateClearCacheContent');
$GLOBALS['application']->register('onAfterAvailable',   'onUpdateClearCacheContent');

$GLOBALS['application']->register('onAfterSetFrontpage', 'onAfterSetFrontpage');
$GLOBALS['application']->register('onAfterSetInhome',    'refreshHome');

$GLOBALS['application']->register('onAfterPosition',     'refreshFrontpage');