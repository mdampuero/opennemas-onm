<?php
// Albums events
if (preg_match('%albums_events.php%', $_SERVER['PHP_SELF'])) {
	die();
}

/*
 clearCacheContent, callback registered into Content
*/
$GLOBALS['application']->register('onAfterUpdate', '');
$GLOBALS['application']->register('onAfterAvailable',   '');

$GLOBALS['application']->register('onAfterSetFrontpage', '');
$GLOBALS['application']->register('onAfterSetFavorite', 'refreshHome');

