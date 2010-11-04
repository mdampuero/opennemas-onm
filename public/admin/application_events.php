<?php
// Application events
if (preg_match('%application_events.php%', $_SERVER['PHP_SELF'])) {
	die();
}

/*
 clearCacheContent, callback registered into Content
*/
$GLOBALS['application']->register('onAfterUpdate',       'onUpdateClearCacheContent');

// Frontpage
$GLOBALS['application']->register('onAfterSetFrontpage', 'refreshFrontpage');
$GLOBALS['application']->register('onAfterPosition',     'refreshFrontpage');

// Home
$GLOBALS['application']->register('onAfterSetInhome', 'refreshHome');
$GLOBALS['application']->register('onAfterHomePosition', 'refreshHome');

